<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $guarded = [];

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    public static function generateFor($table_name)
    {
        self::query()->firstOrCreate(['key' => 'browse_'.$table_name, 'table_name' => $table_name]);
        self::query()->firstOrCreate(['key' => 'read_'.$table_name, 'table_name' => $table_name]);
        self::query()->firstOrCreate(['key' => 'edit_'.$table_name, 'table_name' => $table_name]);
        self::query()->firstOrCreate(['key' => 'add_'.$table_name, 'table_name' => $table_name]);
        self::query()->firstOrCreate(['key' => 'delete_'.$table_name, 'table_name' => $table_name]);
    }

    public static function removeFrom($table_name)
    {
        self::query()->where('table_name', $table_name)->delete();
    }
}
