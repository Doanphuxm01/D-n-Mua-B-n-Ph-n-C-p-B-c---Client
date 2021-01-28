<?php

namespace App\Http\Middleware;

use App\Elibs\Debug;
use App\Elibs\Helper;
use App\Http\Models\Project;
use Closure;
use Illuminate\Support\Facades\Route;

class ProjectRequire
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
        if (Route::current()->getPrefix()!=='/project') {
            if (!Helper::getSession(Project::SESSION_CURRENT_PROJECT)) {
                return redirect('project/show-switch');
            }
        }

        return $next($request);
    }
}
