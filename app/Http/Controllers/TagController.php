<?php

namespace App\Http\Controllers;

use App\Item;
use App\Setting;
use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function __construct()
    {
        $this->middleware('allowed');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $trash = (bool) $request->input('trash');

        $data['apps'] = Item::ofType('tag')->where('id', '>', 0)->orderBy('title', 'asc')->get();
        $data['trash'] = Item::ofType('tag')->where('id', '>', 0)->onlyTrashed()->get();
        if ($trash) {
            return view('tags.trash', $data);
        } else {
            return view('tags.list', $data);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $data = [];

        return view('tags.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'file' => 'image'
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('icons');
            $request->merge([
                'icon' => $path,
            ]);
        }

        $slug = str_slug($request->title, '-', 'en_US');

        $current_user = User::currentUser();

        // set item type to tag
        $request->merge([
            'type' => '1',
            'url' => $slug,
            'user_id' => $current_user->getId(),
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
     * @param $slug
     * @return View
     */
    public function show($slug): View
    {
        $item = Item::whereUrl($slug)->first();
        //print_r($item);
        $data['apps'] = $item->children()->pinned()->orderBy('order', 'asc')->get();
        $data['tag'] = $item->id;
        $data['all_apps'] = $item->children;

        $data['show_tag_line'] = Setting::fetch('show_tag_line');
        $data['pin_tags'] = Item::getAllPinTags();

        return view('welcome', $data);
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

        // show the edit form and pass the nerd
        return view('tags.edit')
            ->with('item', $item);
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
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'file' => 'image'
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('icons');
            $request->merge([
                'icon' => $path,
            ]);
        }

        $slug = str_slug($request->title, '-', 'en_US');
        // set item type to tag
        $request->merge([
            'url' => $slug,
        ]);

        Item::find($id)->update($request->all());

        $route = route('dash', []);

        return redirect($route)
        ->with('success', __('app.alert.success.tag_updated'));
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

        $route = route('tags.index', []);

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
        $route = route('tags.index', []);

        return redirect($route)
            ->with('success', __('app.alert.success.item_restored'));
    }

    /**
     * Add item to tag
     *
     * @param $tag
     * @param $item
     * @return int 1|0
     */
    public function add($tag, $item): int
    {
        $tag = Item::find($tag);
        $item = Item::find($item);
        if ($tag && $item) {
            // only add items, not cats
            if ((int) $item->type === 0) {
                $tag->children()->attach($item);

                return 1;
            }
        }

        return 0;
    }
}
