<?php

namespace App\Http\Middleware;

use Closure;
use Session;
class AdminLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Session::get('session_id')) {

            if($request->ajax()){
                //ajax请求
                $message = array('code' =>0 ,'msg' => '请您先登录系统');
                header('Content-type: application/json;charset=utf-8');
                echo  json_encode($message);
                exit(0);
            }else {
                return redirect('auth/login');
            }
        }

        return $next($request);
    }
}
