<?php

namespace App\Http\Controllers;

use Artisan;
use App\Application;
use App\Item;
use App\Setting;
use App\User;
use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\SupportedApps;
use App\Jobs\ProcessApps;
use App\Search;
use Illuminate\Support\Facades\Route;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('allowed');
    }
     /**
     * Display a listing of the resource on the dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dash()
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
     * @return \Illuminate\Http\Response
     */
    public function setOrder(Request $request)
    {
        $order = array_filter($request->input('order'));
        foreach($order as $o => $id) {
            $item = Item::find($id);
            $item->order = $o;
            $item->save();
        }
    }

    /**
     * Pin item on the dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function pin($id)
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
     * @return \Illuminate\Http\Response
     */
    public function unpin($id)
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
     * @return \Illuminate\Http\Response
     */
    public function pinToggle($id, $ajax=false, $tag=false)
    {
        $item = Item::findOrFail($id);
        $new = ((bool)$item->pinned === true) ? false : true;
        $item->pinned = $new;
        $item->save();
        if($ajax) {
            if(is_numeric($tag) && $tag > 0) {
                $item = Item::whereId($tag)->first();
                $data['apps'] = $item->children()->pinned()->orderBy('order', 'asc')->get();
            } else {
                $data['apps'] = Item::pinned()->orderBy('order', 'asc')->get();
            }
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
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $trash = (bool)$request->input('trash');

        $data['apps'] = Item::ofType('item')->orderBy('title', 'asc')->get();
        $data['trash'] = Item::ofType('item')->onlyTrashed()->get();
        if($trash) {
            return view('items.trash', $data);
        } else {
            return view('items.list', $data);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $data['tags'] = Item::ofType('tag')->orderBy('title', 'asc')->pluck('title', 'id');
        $data['tags']->prepend(__('app.dashboard'), 0);
        $data['current_tags'] = collect([0 => __('app.dashboard')]);
        return view('items.create', $data);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'url' => 'required',
        ]);

        if($request->hasFile('file')) {
            $path = $request->file('file')->store('icons');
            $request->merge([
                'icon' => $path
            ]);
        }

        $config = Item::checkConfig($request->input('config'));
        $current_user = User::currentUser();
        $request->merge([
            'description' => $config,
            'user_id' => $current_user->id
        ]);

        if($request->input('class') === 'null') {
            $request->merge([
                'class' => null,
            ]);
        }


        //die(print_r($request->input('config')));
        
        $item = Item::create($request->all());

        //Search::storeSearchProvider($request->input('class'), $item);

        $item->parents()->sync($request->tags);

        $route = route('dash', []);
        return redirect($route)
            ->with('success', __('app.alert.success.item_created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Get the item
        $data['item'] = Item::find($id);
        $data['tags'] = Item::ofType('tag')->orderBy('title', 'asc')->pluck('title', 'id');
        $data['tags']->prepend(__('app.dashboard'), 0);
        $data['current_tags'] = $data['item']->tags();
        //$data['current_tags'] = $data['item']->parent;
        //die(print_r($data['current_tags']));
        // show the edit form and pass the nerd
        return view('items.edit', $data);    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'url' => 'required',
        ]);
            //die(print_r($request->all()));
        if($request->hasFile('file')) {
            $path = $request->file('file')->store('icons');
            $request->merge([
                'icon' => $path
            ]);
        }
        
        $config = Item::checkConfig($request->input('config'));
        $current_user = User::currentUser();
        $request->merge([
            'description' => $config,
            'user_id' => $current_user->id
        ]);

        if($request->input('class') === 'null') {
            $request->merge([
                'class' => null,
            ]);
        }


        $item = Item::find($id);
        $item->update($request->all());

        //Search::storeSearchProvider($request->input('class'), $item);

        $item->parents()->sync($request->tags);

        $route = route('dash', []);
        return redirect($route)
            ->with('success',__('app.alert.success.item_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //
        $force = (bool)$request->input('force');
        if($force) {
            Item::withTrashed()
                ->where('id', $id)
                ->forceDelete();
        } else {
            Item::find($id)->delete();
        }

        $route = route('items.index', []);
        return redirect($route)       
            ->with('success',__('app.alert.success.item_deleted'));
    }

    /**
     * Restore the specified resource from soft deletion.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        //
        Item::withTrashed()
                ->where('id', $id)
                ->restore();      
        
        $route = route('items.index', []);
        return redirect($route)
            ->with('success',__('app.alert.success.item_restored'));
    }

    /**
     * Return details for supported apps
     *
     * @return Json
     */
    public function appload(Request $request)
    {
        $output = [];
        $appname = $request->input('app');
        //die($appname);

        $app_details = Application::where('name', $appname)->firstOrFail();
        $appclass = $app_details->class();
        $app = new $appclass;

        // basic details
        $output['icon'] = $app_details->icon();
        $output['name'] = $app_details->name;
        $output['iconview'] = $app_details->iconView();
        $output['colour'] = $app_details->defaultColour();
        $output['class'] = $appclass;

        // live details
        if($app instanceof \App\EnhancedApps) {
            $output['config'] = className($app_details->name).'.config';
        } else {
            $output['config'] = null;
        }
        
        return json_encode($output);
    }

    public function testConfig(Request $request)
    {
        $data = $request->input('data');
        //$url = $data[array_search('url', array_column($data, 'name'))]['value'];
        
        $app = $data['type'];

        $app_details = new $app();
        $app_details->config = (object)$data;
        $app_details->test();
    }

    public function getStats($id)
    {
        $item = Item::find($id);

        $config = $item->getconfig();
        if(isset($item->class)) {
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
