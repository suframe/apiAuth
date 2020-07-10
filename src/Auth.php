<?php

namespace suframe\apiAuth;

use app\anding\model\ApiUser;
use suframe\apiAuth\driver\RedisDriver;
use suframe\apiAuth\logic\UserLogicInterface;
use suframe\apiAuth\exceptions\ApiException;
use suframe\apiAuth\traits\SingleInstance;

class Auth
{
    use SingleInstance;

    protected $driver;

    protected $uid;

    protected $logic;

    public function setUserLogic(UserLogicInterface $logic)
    {
        $this->logic = $logic;
    }

    public function setUser($uid)
    {
        $this->uid = $uid;
    }

    /**
     * 登录
     * @param $phone
     * @param $password
     * @return mixed
     * @throws \Exception
     */
    public function login($uid)
    {
        return $this->getDriver()->login($uid);
    }

    /**
     * @param $refreshToken
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function refreshToken($refreshToken)
    {
        if (!$refreshToken) {
            ApiException::throws(ApiException::$needLogin);
        }
        /** @var ApiUser $andingUser */
        $andingUser = ApiUser::where('enable', 1)
            ->where('refresh_token', $refreshToken)->find();

        if (!$andingUser || !$andingUser->refreshTokenValid()) {
            ApiException::throws(ApiException::$refreshTokenExpire);
        }
        return $this->getDriver()->login($andingUser->id);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function logout()
    {
        if (!$this->getUid()) {
            return false;
        }
        $rs = $this->getDriver()->logout();
        $this->setUser(null);
        return $rs;
    }

    /**
     * 初始化用户
     * @return mixed
     * @throws \Exception
     */
    public function getUid()
    {
        if ($this->uid) {
            return $this->uid;
        }
        return $this->uid = $this->getDriver()->initUser();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function guest()
    {
        return !$this->getUid();
    }

    public function hashPassword($password)
    {
        return md5(md5($password . '__USR__'));
    }

    /**
     * 认证驱动
     * @return RedisDriver
     */
    public function getDriver()
    {
        if ($this->driver) {
            return $this->driver;
        }
        return $this->driver = new RedisDriver();
    }

    public function setDriver($driver)
    {
        return $this->driver = $driver;
    }

}