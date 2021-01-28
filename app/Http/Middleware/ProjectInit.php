<?php

namespace App\Http\Middleware;

use App\Elibs\Debug;
use App\Elibs\Helper;
use App\Http\Models\Member;
use App\Http\Models\Project;
use App\Http\Models\ProjectPermission;
use App\Http\Models\Role;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class ProjectInit
{
    public function handle($request, Closure $next, $guard = NULL)
    {

        Project::getCurentProject();
        //Debug::show(Project::$curentProject);
        return $next($request);
    }
}
