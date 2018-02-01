<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
//use App\SupportedApps\Contracts\Applications;
use App\SupportedApps\Nzbget;

class ItemController extends Controller
{

     /**
     * Display a listing of the resource on the dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dash()
    {
        $data['apps'] = Item::all();
        return view('welcome', $data);
    }

   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['apps'] = Item::all();
        return view('items.list', $data);
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
    public function destroy($id)
    {
        //
        Item::find($id)->delete();
        return redirect()->route('dash')
            ->with('success','Item deleted successfully');
    }
}
