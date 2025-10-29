<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="SystemSetting",
 *     type="object",
 *     title="SystemSetting",
 *     @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *     @OA\Property(property="key", type="string", example="site_name"),
 *     @OA\Property(property="value", type="string", example="My Awesome Site"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true"),
 * )
 */
class SystemSetting extends Model
{

    protected $table = "settings";

    protected $fillable = [
        'key',
        'value',
    ];
}
