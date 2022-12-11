<?php

namespace App\Http\Controllers;

use App\Item;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class ItemRestController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('allowed');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Collection
     */
    public function index()
    {
        $columns = [
            'title',
            'colour',
            'url',
            'description',
            'appid',
            'appdescription',
        ];

        return Item::select($columns)
            ->where('deleted_at', null)
            ->where('type', '0')
            ->orderBy('order', 'asc')
            ->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return object
     */
    public function store(Request $request): object
    {
        $item = ItemController::storelogic($request);

        if ($item) {
            return (object) ['status' => 'OK'];
        }

        return (object) ['status' => 'FAILED'];
    }

    /**
     * Display the specified resource.
     *
     * @param Item $item
     * @return Response
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Item $item
     * @return Response
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Item $item
     * @return Response
     */
    public function update(Request $request, Item $item)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Item $item
     * @return Response
     */
    public function destroy(Item $item)
    {
        //
    }
}
