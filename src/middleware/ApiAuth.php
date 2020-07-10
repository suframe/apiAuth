<?php

namespace suframe\apiAuth\middleware;

use suframe\apiAuth\exceptions\ApiException;
use suframe\apiAuth\Auth;
use think\Request;

class ApiAuth
{

    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, \Closure $next)
    {

        $auth = new Auth();
        if ($auth->guest() && !$this->shouldPassThrough($request)) {
            ApiException::throws(ApiException::$needLogin);
        }
        $uid = $auth->getUid();

        $get = $request->get();
        $get['__UID__'] = $uid;
        $request->withGet($get);
        return $next($request);
    }

    /**
     * 白名单
     * @param Request $request
     * @return bool
     */
    protected function shouldPassThrough(Request $request)
    {
        if (env('LOCAL_DEV')) {
            $excepts = [
                'api/auth/login',
                'api/auth/phoneCode',
                'api/auth/phoneCodeLogin',
                'api/auth/weixin',
                'api/auth/refreshToken',
            ];
        } else {
            $excepts = [
                'auth/login',
                'auth/phoneCode',
                'auth/phoneCodeLogin',
                'auth/weixin',
                'auth/refreshToken',
            ];
        }
        $pathInfo = $request->pathinfo();
        if($pathInfo == 'favicon.ico'){
            return true;
        }
        foreach ($excepts as $except) {
            if($pathInfo == $except){
                return true;
            }
        }
        return false;
    }
}
