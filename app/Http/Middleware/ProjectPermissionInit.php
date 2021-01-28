<?php

namespace App\Http\Middleware;

use App\Elibs\Debug;
use App\Elibs\Helper;
use App\Http\Models\Member;
use App\Http\Models\Project;
use App\Http\Models\ProjectPermission;
use Closure;
use Illuminate\Support\Facades\Route;

class ProjectPermissionInit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = NULL)
    {
        //yêu cầu dự án chạy cần có tồn tại 1 project
        ProjectPermission::getAllPermissionOfStaff(Member::$currentMember);

        return $next($request);
    }
}
