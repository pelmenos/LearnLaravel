<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PermissionController extends Controller
{
    public function add(Request $request, File $file)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);
        $user = User::where('email', $request->email)->first();
        Permission::createCoAuthor($user->id, $file->id);
        $perm = Permission::files($file->id);
        $response = [];
        foreach($perm as $key=>$value){
            $user = User::find($value->user_id);
            $response[] = [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'type' => $value->access_type,
                'code' => 200
            ];
        }
        return response()->json($response);
    }

    public function delete(Request $request, File $file)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);
        $user = User::where('email', $request->email)->first();
        Permission::where(['user_id' => $user->id, 'file_id' => $file->id])->firstOr(function (){
            throw new NotFoundHttpException();
        })->delete();
        $perm = Permission::files($file->id);
        $response = [];
        foreach($perm as $key=>$value){
            $user = User::find($value->user_id);
            $response[] = [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'type' => $value->access_type,
                'code' => 200
            ];
        }
        return response()->json($response);
    }

    public function userFiles(Request $request)
    {
        $files = File::where('user_id', $request->user()->id)->get();
        $response = [];
        foreach($files as $key=>$file){
            $access = [];
            $perm = Permission::files($file->id);
            foreach($perm as $index=>$value){
                $user = User::find($value->user_id);
                $access[] = [
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'type' => $value->access_type,
                ];
            }
            $response[] = [
                'file_id' => $file->file_id,
                'name' => $file->name,
                'code' => 200,
                'url' => $_SERVER['HTTP_HOST'] . '/files/' . $file->file_id,
                'access' => $access
            ];
        }
        return response()->json($response);
    }

    public function userAccessFiles(Request $request)
    {
        $perm = Permission::where('user_id', $request->user()->id)->get();
        $response = [];
        foreach ($perm as $key=>$value){
            $file = File::find($value->file_id);
            $response[] = [
                'code' => 200,
                'name' => $file->name,
                'file_id' => $file->file_id,
                'url' => $_SERVER['HTTP_HOST'] . '/files/' . $file->file_id
            ];
        }

        return response()->json($response);
    }
}
