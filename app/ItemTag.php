<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\ItemTag
 *
 * @property int $item_id
 * @property int $tag_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTag whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTag whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ItemTag extends Pivot
{
    use HasFactory;
}
