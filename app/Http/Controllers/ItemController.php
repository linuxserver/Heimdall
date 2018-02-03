<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\SupportedApps\Nzbget;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{

     /**
     * Display a listing of the resource on the dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dash()
    {
        $data['apps'] = Item::pinned()->orderBy('order', 'asc')->get();
        $data['all_apps'] = Item::all();
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
        return redirect()->route('dash');
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
        return redirect()->route('dash');
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
            return redirect()->route('dash');           
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

        $data['apps'] = Item::all();
        $data['trash'] = Item::onlyTrashed()->get();
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
        $data = [];
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
        
        Item::create($request->all());

        return redirect()->route('dash')
            ->with('success','Item created successfully');
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
        $item = Item::find($id);

        // show the edit form and pass the nerd
        return view('items.edit')
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
            'url' => 'required',
        ]);

        if($request->hasFile('file')) {
            $path = $request->file('file')->store('icons');
            $request->merge([
                'icon' => $path
            ]);
        }
        

        Item::find($id)->update($request->all());

        return redirect()->route('dash')
            ->with('success','Item updated successfully');
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
        
        return redirect()->route('items.index')
            ->with('success','Item deleted successfully');
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
        return redirect()->route('items.index')
            ->with('success','Item restored successfully');
    }
}
