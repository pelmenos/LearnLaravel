<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\File;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PermissionController extends Controller
{
    public function add(PermissionRequest $request, File $file)
    {
        $user_id = User::where('email', $request->email)->first()->id;

        if (!$file->accesses->contains('id', $user_id)) {
            $file->accesses()->attach($user_id, ['access_type' => 'co-author']);
        }

        return PermissionResource::collection($file->accesses()->get());
    }

    public function delete(PermissionRequest $request, File $file)
    {
        $user = User::where('email', $request->email)->first();
        if ($user->email === Auth::user()->email) {
            throw new AuthorizationException;
        }
        $file->accesses()->detach($user->id);

        return PermissionResource::collection($file->accesses()->get());
    }

}
