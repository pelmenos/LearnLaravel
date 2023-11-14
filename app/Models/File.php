<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Exception;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'file_id',
        'user_id'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'permissions', 'file_id','user_id');
    }
}
