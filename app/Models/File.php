<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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


    public function isAuthor(User $user): bool
    {
        return Permission::where(['file_id' => $this->id])->contains('id', $user->id);
    }

    public function isOwner(User $user): bool
    {
        return $this->user_id === $user->id;
    }
}
