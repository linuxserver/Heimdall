<?php

namespace App\Http\Controllers;

use App\Application;
use App\Item;
use App\Jobs\ProcessApps;
use App\User;
use Artisan;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ItemController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('allowed');
    }

    /**
     * Display a listing of the resource on the dashboard.
     *
     * @return View
     */
    public function dash(): View
    {
        $data['apps'] = Item::whereHas('parents', function ($query) {
            $query->where('id', 0);
        })->orWhere('type', 1)->pinned()->orderBy('order', 'asc')->get();

        $data['all_apps'] = Item::whereHas('parents', function ($query) {
            $query->where('id', 0);
        })->orWhere('type', 1)->orderBy('order', 'asc')->get();

        //$data['all_apps'] = Item::doesntHave('parents')->get();
        //die(print_r($data['apps']));
        return view('welcome', $data);
    }

    /**
     * Set order on the dashboard.
     *
     * @return void
     */
    public function setOrder(Request $request)
    {
        $order = array_filter($request->input('order'));
        foreach ($order as $o => $id) {
            $item = Item::find($id);
            $item->order = $o;
            $item->save();
        }
    }

    /**
     * Pin item on the dashboard.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function pin($id): RedirectResponse
    {
        $item = Item::findOrFail($id);
        $item->pinned = true;
        $item->save();
        $route = route('dash', []);

        return redirect($route);
    }

    /**
     * Unpin item on the dashboard.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function unpin($id): RedirectResponse
    {
        $item = Item::findOrFail($id);
        $item->pinned = false;
        $item->save();
        $route = route('dash', []);

        return redirect($route);
    }

    /**
     * Unpin item on the dashboard.
     *
     * @return RedirectResponse|View
     */
    public function pinToggle($id, $ajax = false, $tag = false)
    {
        $item = Item::findOrFail($id);
        $new = !(((bool)$item->pinned === true));
        $item->pinned = $new;
        $item->save();
        if ($ajax) {

            $item = Item::whereId($tag)->first();

            $data['apps'] = new \Illuminate\Database\Eloquent\Collection;

            if ((int)$tag === 0) {
                $tags = Item::where('type', 1)->pinned()->orderBy('order', 'asc')->get();
                $data['apps'] = $data['apps']->merge($tags);
            }

            $apps = $item->children()->pinned()->orderBy('order', 'asc')->get();
            $data['apps'] = $data['apps']->merge($apps);


            $data['ajax'] = true;

            return view('sortable', $data);
        } else {
            $route = route('dash', []);

            return redirect($route);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(Request $request)
    {
        $trash = (bool) $request->input('trash');

        $data['apps'] = Item::ofType('item')->orderBy('title', 'asc')->get();
        $data['trash'] = Item::ofType('item')->onlyTrashed()->get();
        if ($trash) {
            return view('items.trash', $data);
        } else {
            return view('items.list', $data);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        //
        $data['tags'] = Item::ofType('tag')->orderBy('title', 'asc')->pluck('title', 'id');
        $data['tags']->prepend(__('app.dashboard'), 0);
        $data['current_tags'] = '0';

        return view('items.create', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        // Get the item
        $item = Item::find($id);
        if ($item->appid === null && $item->class !== null) { // old apps wont have an app id so set it
            $app = Application::where('class', $item->class)->first();
            if ($app) {
                $item->appid = $app->appid;
            }
        }
        $data['item'] = $item;
        $data['tags'] = Item::ofType('tag')->orderBy('title', 'asc')->pluck('title', 'id');
        $data['tags']->prepend(__('app.dashboard'), 0);
        $data['current_tags'] = $data['item']->tags();
        //$data['current_tags'] = $data['item']->parent;
        //die(print_r($data['current_tags']));
        // show the edit form and pass the nerd
        return view('items.edit', $data);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Item
     */
    public static function storelogic(Request $request, $id = null): Item
    {
        $application = Application::single($request->input('appid'));
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'url' => 'required',
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('icons');
            $request->merge([
                'icon' => $path,
            ]);
        } elseif (strpos($request->input('icon'), 'http') === 0) {
            $options = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $contents = file_get_contents($request->input('icon'), false, stream_context_create($options));

            if ($application) {
                $icon = $application->icon;
            } else {
                $file = $request->input('icon');
                $path_parts = pathinfo($file);
                $icon = md5($contents);
                $icon .= '.' . $path_parts['extension'];
            }
            $path = 'icons/' . $icon;

            // Private apps could have here duplicated icons folder
            if (strpos($path, 'icons/icons/') !== false) {
                $path = str_replace('icons/icons/', 'icons/', $path);
            }
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, $contents);
            }
            $request->merge([
                'icon' => $path,
            ]);
        }

        $config = Item::checkConfig($request->input('config'));

        // Don't overwrite the stored password if it wasn't submitted when updating the item
        if ($id !== null && strpos($config, '"password":null') !== false) {
            $storedItem = Item::find($id);
            $storedConfigObject = json_decode($storedItem->getAttribute('description'));

            $configObject = json_decode($config);
            $configObject->password = $storedConfigObject->password;

            $config = json_encode($configObject);
        }

        $current_user = User::currentUser();
        $request->merge([
            'description' => $config,
            'user_id' => $current_user->getId(),
        ]);

        if ($request->input('appid') === 'null') {
            $request->merge([
                'class' => null,
            ]);
        } else {
            $request->merge([
                'class' => Application::classFromName($application->name),
            ]);
        }

        if ($id === null) {
            $item = Item::create($request->all());
        } else {
            $item = Item::find($id);
            $item->update($request->all());
        }

        $item->parents()->sync($request->tags);
        return $item;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        self::storelogic($request);

        $route = route('dash', []);

        return redirect($route)
            ->with('success', __('app.alert.success.item_created'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function show(int $id): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        self::storelogic($request, $id);
        $route = route('dash', []);

        return redirect($route)
            ->with('success', __('app.alert.success.item_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        //
        $force = (bool) $request->input('force');
        if ($force) {
            Item::withTrashed()
                ->where('id', $id)
                ->forceDelete();
        } else {
            Item::find($id)->delete();
        }

        $route = route('items.index', []);

        return redirect($route)
            ->with('success', __('app.alert.success.item_deleted'));
    }

    /**
     * Restore the specified resource from soft deletion.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function restore(int $id): RedirectResponse
    {
        //
        Item::withTrashed()
            ->where('id', $id)
            ->restore();

        $route = route('items.index', []);

        return redirect($route)
            ->with('success', __('app.alert.success.item_restored'));
    }

    /**
     * Return details for supported apps
     *
     * @param Request $request
     * @return string|null
     * @throws GuzzleException
     */
    public function appload(Request $request): ?string
    {
        $output = [];
        $appid = $request->input('app');

        if ($appid === 'null') {
            return null;
        }

        $output['config'] = null;
        $output['custom'] = null;

        $app = Application::single($appid);
        $output = (array) $app;

        $appdetails = Application::getApp($appid);

        if ((bool) $app->enhanced === true) {
            // if(!isset($app->config)) { // class based config
            $output['custom'] = className($appdetails->name) . '.config';
            // }
        }

        $output['colour'] = ($app->tile_background == 'light') ? '#fafbfc' : '#161b1f';

        if (strpos($app->icon, '://') !== false) {
            $output['iconview'] = $app->icon;
        } elseif (strpos($app->icon, 'icons/') !== false) {
            // Private apps have the icon locally
            $output['iconview'] = URL::to('/') . '/storage/' . $app->icon;
            $output['icon'] = str_replace('icons/', '', $output['icon']);
        } else {
            $output['iconview'] = config('app.appsource') . 'icons/' . $app->icon;
        }


        return json_encode($output);
    }

    public function testConfig(Request $request)
    {
        $data = $request->input('data');
        //$url = $data[array_search('url', array_column($data, 'name'))]['value'];
        $single = Application::single($data['type']);
        $app = $single->class;

        // If password is not resubmitted fill it from the database when in edit mode
        if (
            array_key_exists('password', $data) &&
            $data['password'] === null &&
            array_key_exists('id', $data)
        ) {
            $item = Item::find($data['id']);
            if ($item) {
                $itemConfig = $item->getConfig();
                $data['password'] = $itemConfig->password;
            }
        }

        $app_details = new $app();
        $app_details->config = (object) $data;
        $app_details->test();
    }

    public function execute($url, $attrs = [], $overridevars = false)
    {
        $res = null;

        $vars = ($overridevars !== false) ?
            $overridevars : [
                'http_errors' => false,
                'timeout' => 15,
                'connect_timeout' => 15,
                'verify' => false,
            ];

        $client = new Client($vars);

        $method = 'GET';

        try {
            return $client->request($method, $url, $attrs);
        } catch (ConnectException $e) {
            Log::error('Connection refused');
            Log::debug($e->getMessage());
        } catch (ServerException $e) {
            Log::debug($e->getMessage());
        }

        return $res;
    }

    public function websitelookup($url)
    {
        $url = base64_decode($url);
        $data = $this->execute($url);

        return $data->getBody();
    }

    public function getStats($id)
    {
        $item = Item::find($id);

        $config = $item->getconfig();
        if (isset($item->class)) {
            $application = new $item->class;
            $application->config = $config;
            echo $application->livestats();
        }
    }

    public function checkAppList()
    {
        ProcessApps::dispatch();
        $route = route('items.index');

        return redirect($route)
            ->with('success', __('app.alert.success.updating'));
    }
}
