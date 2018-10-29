<?php

namespace App\Http\Controllers;

use App\Application;
use App\Item;
use App\Setting;
use App\User;
use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\SupportedApps;

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
        $data['apps'] = Item::doesntHave('parents')->pinned()->orderBy('order', 'asc')->get();
        $data['all_apps'] = Item::doesntHave('parents')->get();
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
        $route = route('dash', [], false);
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
        $route = route('dash', [], false);
        return redirect($route);
    }

     /**
     * Unpin item on the dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function pinToggle($id, $ajax=false)
    {
        $item = Item::findOrFail($id);
        $new = ((bool)$item->pinned === true) ? false : true;
        $item->pinned = $new;
        $item->save();
        if($ajax) {
            $data['apps'] = Item::pinned()->get();
            $data['ajax'] = true;
            return view('sortable', $data);
        } else {
            $route = route('dash', [], false);
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
        $data['current_tags'] = [];
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
            'url' => 'required|url',
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

        $item->parents()->sync($request->tags);

        $route = route('dash', [], false);
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
        $data['current_tags'] = $data['item']->parents;

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
            'url' => 'required|url',
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

        $item->parents()->sync($request->tags);

        $route = route('dash', [], false);
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

        $route = route('items.index', [], false);
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
        
        $route = route('items.inded', [], false);
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
            $output['config'] = $app_details->name.'.config';
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
        $localapps = Application::all();
        $list = json_decode(SupportedApps::getList()->getBody());
        $validapps = [];
        foreach($list->apps as $app) {
            $validapps[] = $app->appid;
            if(!file_exists(app_path('SupportedApps/'.className($app->name)))) {
                SupportedApps::getFiles($app);
                $application = new Application;
                SupportedApps::saveApp($app, $application);
            } else {
                // check if there has been an update for this app
                $localapp = $localapps->where('appid', $app->appid)->first();
                if($localapp) {
                    if($localapp->sha !== $app->sha) {
                        SupportedApps::getFiles($app);
                        SupportedApps::saveApp($app, $localapp);
                    }
                }  else {
                    SupportedApps::getFiles($app);
                    $application = new Application;
                    SupportedApps::saveApp($app, $application);
      
                }
            }
        }
        //$delete = Application::whereNotIn('appid', $validapps)->delete(); // delete any apps not in list
        // removed the delete so local apps can be added
    }

    
    

    
}
