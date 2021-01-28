<?php

namespace App\Http\Middleware;

use App\Elibs\Debug;
use App\Elibs\Helper;
use App\Elibs\eView;
use App\Http\Models\Member;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthenticate
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
    public function handle($request, Closure $next, $guard = null)
    {
        // bảo trì thì mỏ cái này
        //return eView::getInstance()->setViewMaintenance();
        //dùng cái này => con lv5 lỗi session
        $refBeforeLogin  =str_replace(Request::capture()->root(), '', Request::capture()->fullUrl());
        $refBeforeLogin =urlencode($refBeforeLogin);
        if (!Helper::getSession(Member::SESSION_KEY_FOR_CUR_MEMBER)) {
            //todo: tạm thời remember mãi mãi cho thành viên (18/5/2016)
            //tránh việc bi logout tự động
            $cookie = Helper::getCookie(Member::COOKIE_KEY_FOR_CUR_MEMBER);


            if ($cookie) {

                $salt = explode(':', $cookie);
                if (isset($salt[1]) && sha1($salt[0] . 'ngannv') == $salt[1]) {
                    $member = Member::getMemberByAccount($salt[0]);
                    if($member && 1==2) {
                        Member::setLogin($member);
                        Member::setCurent(Helper::getSession(Member::SESSION_KEY_FOR_CUR_MEMBER));
                        return $next($request);
                    }
                }
            }

            #endregion

            if ($request->ajax() || $request->wantsJson()) {
                return response('Phiên đăng nhập của bạn đã hết. Bạn cần đăng nhập lại hệ thống để thực hiện chức năng.(hãy mở tab khác để đăng nhập nhé)', 401);
            } else {

                return redirect()->guest('auth/login'.'?href='.$refBeforeLogin);
            }
        }
        Member::setCurent(Helper::getSession(Member::SESSION_KEY_FOR_CUR_MEMBER));
        //Debug::show(Member::$curentMember);
        if(!Member::isContentEditor()){
            return redirect()->guest('auth/login'.'?href='.$refBeforeLogin);
        }
        return $next($request);
    }

    public function __handle($request, Closure $next, $guard = null)
    {


        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Bạn cần đăng nhập để có thể mua hàng mua hàng.', 401);
            } else {
                return redirect()->guest('auth/login');
            }
        }
        return $next($request);
    }
}
