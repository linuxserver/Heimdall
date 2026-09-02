<?php

namespace App\Http\Controllers;

use App\Item;
use App\User;
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
     */
    public function index(): Collection
    {
        $columns = [
            'id',
            'title',
            'colour',
            'url',
            'description',
            'appid',
            'appdescription',
            'pinned',
            'order',
        ];

        return Item::with('parents')
            ->select($columns)
            ->where('deleted_at', null)
            ->where('type', '0')
            ->orderBy('order', 'asc')
            ->get()
            ->map(function (Item $item) {
                return [
                    'title' => $item->title,
                    'colour' => $item->colour,
                    'url' => $item->url,
                    'description' => $item->description,
                    'appid' => $item->appid,
                    'appdescription' => $item->appdescription,
                    'pinned' => $item->pinned,
                    'pinned_order' => $item->order,
                    'tags' => $item->parents
                        ->where('id', '!=', 0)
                        ->pluck('title')
                        ->values()
                        ->all(),
                ];
            });
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
     */
    public function store(Request $request): object
    {
        // Imports pass tags as an array of tag titles so they can round-trip
        // across instances. Resolve those titles into local tag ids (creating
        // any that don't yet exist) before handing off to the shared store
        // logic. When no tags are supplied we keep the previous behaviour.
        if ($request->has('tags')) {
            $request->merge([
                'tags' => $this->resolveTags($request->input('tags')),
            ]);
        }

        $item = ItemController::storelogic($request);

        if ($item) {
            return (object) ['status' => 'OK'];
        }

        return (object) ['status' => 'FAILED'];
    }

    /**
     * Resolve an incoming list of tags into tag ids.
     *
     * Numeric 0 (or "0") maps to the root/default dashboard. Every other entry
     * is treated as a tag title: an existing tag with that title is reused, and
     * a missing one is created. The lookup keeps the operation idempotent so
     * importing many items that share a tag title only ever creates one tag.
     *
     * @param  mixed  $tags
     * @return array<int, int>
     */
    private function resolveTags($tags): array
    {
        if (! is_array($tags)) {
            return [0];
        }

        $ids = [];

        foreach ($tags as $tag) {
            if ($tag === 0 || $tag === '0') {
                $ids[] = 0;
                continue;
            }

            $title = is_string($tag) ? trim($tag) : $tag;

            if ($title === '' || $title === null) {
                continue;
            }

            $existing = Item::where('type', '1')
                ->where('title', $title)
                ->first();

            if ($existing) {
                $ids[] = (int) $existing->id;
                continue;
            }

            $created = Item::create([
                'title' => $title,
                'type' => '1',
                'url' => str_slug($title, '-', 'en_US'),
                'user_id' => User::currentUser()->getId(),
            ]);

            $ids[] = (int) $created->id;
        }

        $ids = array_values(array_unique($ids));

        return empty($ids) ? [0] : $ids;
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item): Response
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item): Response
    {
        //
    }
}
