<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Permission extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'access_type',
        'file_id',
        'user_id'
    ];
    use HasFactory;

    public static function files(int $id)
    {
        return Permission::where('file_id', $id)->get();
    }

    public static function createAuthor(int $user_id, int $file_id): void
    {
        Permission::create(['user_id' => $user_id, 'file_id' => $file_id, 'access_type' => 'author']);
    }
}
