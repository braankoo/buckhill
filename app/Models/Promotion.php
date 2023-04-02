<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Promotion
 *
 * @property int $id
 * @property string $uuid
 * @property string $title
 * @property string $content
 * @property mixed $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\PromotionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion query()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereUuid($value)
 * @mixin \Eloquent
 */
final class Promotion extends Model
{
    use HasFactory;
}
