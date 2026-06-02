<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemData extends Model
{
    protected $fillable = [
        'key',
        'value'
    ];

    public $timestamps = false;

    public static function get($key, $default = null)
    {
        $data = self::where('key', $key)->first();
        return $data ? $data->value : $default;
    }

    public static function set($key, $value)
    {
        $data = self::firstOrNew(['key' => $key]);
        $data->value = $value;
        $data->save();
    }

    public static function getCron()
    {
        $sync_frequency = self::where('key', 'sync_frequency')->first()->value;
        $sync_time = self::where('key', 'sync_time')->first()->value;
        $sync_hour = intval(explode(':', $sync_time)[0]);
        $sync_minute = intval(explode(':', $sync_time)[1]);
        $cron = $sync_minute . ' ' . $sync_hour . ' * * ';
        $cron .= match($sync_frequency) {
            'daily' => '*',
            '2-days' => '2,4',
            '3-days' => '1,3,5',
            'weekly' => '1',
            default => '*',
        };
        return $cron;
    }
}
