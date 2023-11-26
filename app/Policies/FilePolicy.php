<?php

namespace App\Policies;

use App\Models\File;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FilePolicy
{
    public function view(User $user, File $file): bool
    {
        return $file->IsOwner($user) || $file->isAuthor($user);
    }

    public function update(User $user, File $file): bool
    {
        return $file->IsOwner($user);
    }

    public function delete(User $user, File $file): bool
    {
        return $file->IsOwner($user);
    }

    public function accessesAdd(User $user, File $file): bool
    {
        return $file->IsOwner($user);
    }

    public function accessesDelete(User $user, File $file): bool
    {
        return $file->IsOwner($user);
    }
}
