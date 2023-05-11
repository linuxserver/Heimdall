<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\User;
use DB;

class TagController extends Controller
{
    public function __construct()
    {
        $this->middleware('allowed');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $trash = (bool)$request->input('trash');

        $data['apps'] = Item::ofType('tag')->where('id', '>', 0)->orderBy('title', 'asc')->get();
        $data['trash'] = Item::ofType('tag')->where('id', '>', 0)->onlyTrashed()->get();
        if($trash) {
            return view('tags.trash', $data);
        } else {
            return view('tags.list', $data);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];
        return view('tags.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
        ]);

        if($request->hasFile('file')) {
            $path = $request->file('file')->store('icons');
            $request->merge([
                'icon' => $path
            ]);
        }

        $slug = str_slug($request->title, '-');

        $current_user = User::currentUser();

        // set item type to tag
        $request->merge([
            'type' => '1',
            'url' => $slug,
            'user_id' => $current_user->id
        ]);
        //die(print_r($request->all()));
        Item::create($request->all());

        $route = route('dash', []);
        return redirect($route)
            ->with('success', __('app.alert.success.tag_created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $item = Item::whereUrl($slug)->first();
        //print_r($item);
        $data['apps'] = $item->children()->pinned()->orderBy('order', 'asc')->get();
        $data['tag'] = $item->id;
        $data['all_apps'] = $item->children;
        return view('welcome', $data);
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
        $item = Item::find($id);

        // show the edit form and pass the nerd
        return view('tags.edit')
            ->with('item', $item);    
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
        ]);

        if($request->hasFile('file')) {
            $path = $request->file('file')->store('icons');
            $request->merge([
                'icon' => $path
            ]);
        }

        $slug = str_slug($request->title, '-');
        // set item type to tag
        $request->merge([
            'url' => $slug
        ]);

        Item::find($id)->update($request->all());

        $route = route('dash', []);
        return redirect($route)
        ->with('success',__('app.alert.success.tag_updated'));
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
        
        $route = route('tags.index', []);
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
        $route = route('tags.index', []);
        return redirect($route)
            ->with('success',__('app.alert.success.item_restored'));
    }

    public function add($tag, $item)
    {
        $output = 0;
        $tag = Item::find($tag);
        $item = Item::find($item);
        if($tag && $item) {
            // only add items, not cats
            if((int)$item->type === 0) {
                $tag->children()->attach($item);
                return 1;
            }
        }
        return $output;
    }

}
