<?php

namespace App;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use stdClass;
use Symfony\Component\ClassLoader\ClassMapGenerator;

// @codingStandardsIgnoreStart
/**
 * App\Item
 *
 * @property int $id
 * @property string $title
 * @property string|null $colour
 * @property string|null $icon
 * @property string $url
 * @property string|null $description
 * @property int $pinned
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $type
 * @property int $user_id
 * @property string|null $class
 * @property string|null $appid
 * @property string|null $appdescription
 * @property-read \Illuminate\Database\Eloquent\Collection|Item[] $children
 * @property-read int|null $children_count
 * @property-read string $droppable
 * @property-read \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\UrlGenerator|mixed|string $link
 * @property-read string $link_icon
 * @property-read string $link_target
 * @property-read string $link_type
 * @property-read \Illuminate\Database\Eloquent\Collection|Item[] $parents
 * @property-read int|null $parents_count
 * @property-read \App\User|null $user
 * @method static \Database\Factories\ItemFactory factory(...$parameters)
 * @method static Builder|Item newModelQuery()
 * @method static Builder|Item newQuery()
 * @method static Builder|Item ofType($type)
 * @method static \Illuminate\Database\Query\Builder|Item onlyTrashed()
 * @method static Builder|Item pinned()
 * @method static Builder|Item query()
 * @method static Builder|Item whereAppdescription($value)
 * @method static Builder|Item whereAppid($value)
 * @method static Builder|Item whereClass($value)
 * @method static Builder|Item whereColour($value)
 * @method static Builder|Item whereCreatedAt($value)
 * @method static Builder|Item whereDeletedAt($value)
 * @method static Builder|Item whereDescription($value)
 * @method static Builder|Item whereIcon($value)
 * @method static Builder|Item whereId($value)
 * @method static Builder|Item whereOrder($value)
 * @method static Builder|Item wherePinned($value)
 * @method static Builder|Item whereTitle($value)
 * @method static Builder|Item whereType($value)
 * @method static Builder|Item whereUpdatedAt($value)
 * @method static Builder|Item whereUrl($value)
 * @method static Builder|Item whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Item withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Item withoutTrashed()
 * @mixin \Eloquent
 */
// @codingStandardsIgnoreEnd
class Item extends Model
{
    use SoftDeletes;

    use HasFactory;

    /**
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('user_id', function (Builder $builder) {
            $current_user = User::currentUser();
            if ($current_user) {
                $builder->where('user_id', $current_user->getId())->orWhere('user_id', 0);
            } else {
                $builder->where('user_id', 0);
            }
        });
    }

    protected $fillable = [
        'title',
        'url',
        'colour',
        'icon',
        'appdescription',
        'description',
        'pinned',
        'pinned_line',
        'order',
        'type',
        'class',
        'user_id',
        'tag_id',
        'appid',
    ];



    /**
     * Scope a query to only include pinned items.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePinned(Builder $query): Builder
    {
        return $query->where('pinned', 1);
    }

    public static function checkConfig($config)
    {
        // die(print_r($config));
        if (empty($config)) {
            $config = null;
        } else {
            $config = json_encode($config);
        }

        return $config;
    }

    /**
     * get all pin labels
     * @return Item[]|\Illuminate\Database\Eloquent\Collection
     */
    static public function getAllPinTags()
    {
        $tags = self::query()->where('pinned_line','=',1)->get();
        return $tags;
    }

    public function tags()
    {
        $id = $this->id;
        $tags = ItemTag::select('tag_id')->where('item_id', $id)->pluck('tag_id')->toArray();
        $tagdetails = self::select('id', 'title', 'url', 'pinned')->whereIn('id', $tags)->get();
        //print_r($tags);
        if (in_array(0, $tags)) {
            $details = new self([
                'id' => 0,
                'title' => __('app.dashboard'),
                'url' => '',
                'pinned' => 0,
            ]);
            $tagdetails->prepend($details);
        }

        return $tagdetails;
    }

    /**
     * @return string
     */
    public function getTagClass(): string
    {
        $tags = $this->tags();
        $slugs = [];

        foreach ($tags as $tag) {
            if ($tag->url) {
                $slugs[] = 'tag-'.$tag->url;
            }
        }

        return implode(' ', $slugs);
    }

    /**
     * @return BelongsToMany
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_tag', 'item_id', 'tag_id');
    }

    /**
     * @return BelongsToMany
     */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_tag', 'tag_id', 'item_id');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|UrlGenerator|mixed|string
     */
    public function getLinkAttribute()
    {
        if ((int) $this->type === 1) {
            return url('tag/'.$this->url);
        } else {
            return $this->url;
        }
    }

    /**
     * @return string
     */
    public function getDroppableAttribute(): string
    {
        if ((int) $this->type === 1) {
            return ' droppable';
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getLinkTargetAttribute(): string
    {
        $target = Setting::fetch('window_target');

        if ((int) $this->type === 1 || $target === 'current') {
            return '';
        } else {
            return ' target="'.$target.'"';
        }
    }

    /**
     * @return string
     */
    public function getLinkIconAttribute(): string
    {
        if ((int) $this->type === 1) {
            return 'fa-tag';
        } else {
            return 'fa-arrow-alt-to-right';
        }
    }

    /**
     * @return string
     */
    public function getLinkTypeAttribute(): string
    {
        if ((int) $this->type === 1) {
            return 'tags';
        } else {
            return 'items';
        }
    }

    /**
     * @param $class
     * @return false|mixed|string
     */
    public static function nameFromClass($class)
    {
        $explode = explode('\\', $class);
        $name = end($explode);

        return $name;
    }

    /**
     * @param $query
     * @param $type
     * @return mixed
     */
    public function scopeOfType($query, $type)
    {
        switch ($type) {
            case 'item':
                $typeid = 0;
                break;
            case 'tag':
                $typeid = 1;
                break;
        }

        return $query->where('type', $typeid);
    }

    /**
     * @return bool
     */
    public function enhanced(): bool
    {
        /*if(isset($this->class) && !empty($this->class)) {
            $app = new $this->class;
        } else {
            return false;
        }
        return (bool)($app instanceof \App\EnhancedApps);*/
        return $this->description !== null;
    }

    /**
     * @param $class
     * @return bool
     */
    public static function isEnhanced($class): bool
    {
        if (!class_exists($class, false) || $class === null || $class === 'null') {
            return false;
        }
        $app = new $class;

        return (bool) ($app instanceof EnhancedApps);
    }

    /**
     * @param $class
     * @return false|mixed
     */
    public static function isSearchProvider($class)
    {
        if (!class_exists($class, false) || $class === null || $class === 'null') {
            return false;
        }
        $app = new $class;

        return ((bool) ($app instanceof SearchInterface)) ? $app : false;
    }

    /**
     * @return bool
     */
    public function enabled(): bool
    {
        if ($this->enhanced()) {
            $config = $this->getconfig();
            if ($config) {
                return (bool) $config->enabled;
            }
        }

        return false;
    }

    /**
     * @return mixed|stdClass
     */
    public function getconfig()
    {
        // $explode = explode('\\', $this->class);

        if (! isset($this->description) || empty($this->description)) {
            $config = new stdClass;
            // $config->name = end($explode);
            $config->enabled = false;
            $config->override_url = null;
            $config->apikey = null;

            return $config;
        }

        $config = json_decode($this->description);

        // $config->name = end($explode);

        $config->url = $this->url;
        if (isset($config->override_url) && ! empty($config->override_url)) {
            $config->url = $config->override_url;
        } else {
            $config->override_url = null;
        }

        return $config;
    }

    /**
     * @param $class
     * @return Application|null
     */
    public static function applicationDetails($class): ?Application
    {
        if (! empty($class)) {
            $name = self::nameFromClass($class);
            $application = Application::where('name', $name)->first();
            if ($application) {
                return $application;
            }
        }

        return null;
    }

    /**
     * @param $class
     * @return string
     */
    public static function getApplicationDescription($class): string
    {
        $details = self::applicationDetails($class);
        if ($details !== null) {
            return $details->description.' - '.$details->license;
        }

        return '';
    }

    /**
     * Get the user that owns the item.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
