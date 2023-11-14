<?php

namespace App\Http\Middleware;

use App\Exceptions\ForbiddenException;
use App\Models\Permission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAuthor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $file = $request->route('file');
        $perm = Permission::where(['file_id' => $file->id, 'user_id' => $request->user()->id])->first();
        if (!$perm){
            throw new ForbiddenException();
        }
        return $next($request);
    }
}
