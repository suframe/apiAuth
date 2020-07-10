<?php

namespace suframe\apiAuth\driver;

use app\anding\model\ApiUser;
use Psr\SimpleCache\InvalidArgumentException;
use think\facade\Cache;

class RedisDriver
{

    private $tokenKey = 'APP-TOKEN';

    /**
     * @return \think\cache\Driver
     */
    protected function getStore()
    {
        return Cache::store('redis');
    }

    private function getToken()
    {
        $token = app()->request->header($this->tokenKey);
        return $token;
    }

    /**
     * @return string|null
     */
    public function initUser()
    {
        try {
            $token = $this->getToken();
            if (!$token) {
                return null;
            }
            $rs = $this->getStore()->get('api_' . $token);
            if (!$rs) {
                return null;
            }
            return $rs;
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * 登录
     * @param int $uid
     * @return mixed
     */
    public function login($uid)
    {
        /** @var ApiUser $user */
        $user = ApiUser::where('enable', 1)
            ->find($uid);
        if(!$user){
            return false;
        }
        $token = $this->genToken();
        try {
            $time = time();
            //$tokenValidTime = 86400;//一天
            $tokenValidTime = 8640000;//一天
            $this->getStore()->set('api_' . $token, $uid, $tokenValidTime);
            $user->refresh_token = md5($token . 'refresh_token');
            $user->refresh_token_time = $time;
            $user->save();
            return [
                'uid' => $uid,
                'token' => $token,
                'expire_time' => $time + $tokenValidTime,
                'refresh_token' => $user->refresh_token,
            ];
        } catch (InvalidArgumentException $e) {
        }
        return null;
    }

    /**
     * 退出
     * @return bool
     */
    public function logout()
    {
        $token = $this->getToken();
        try {
            return $this->getStore()->delete('api_' . $token);
        } catch (InvalidArgumentException $e) {
        }
        return false;
    }

    /**
     * 生成token
     * @return string
     */
    protected function genToken()
    {
        $salt = '__anding_user_salt__';
        return md5(md5(session_create_id() . uniqid()) . $salt);
    }

}