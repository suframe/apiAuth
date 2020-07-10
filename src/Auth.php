<?php

namespace suframe\apiAuth;

use app\anding\exceptions\ApiException;
use app\anding\logic\UserLogic;
use app\anding\model\AndingUser;
use app\third\model\ThirdBind;
use app\third\model\ThirdPhoneCode;
use suframe\thinkAdmin\traits\SingleInstance;

class Auth
{
    use SingleInstance;

    protected $driver;

    protected $uid;

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
    public function login($phone, $password)
    {
        /** @var AndingUser $user */
        $user = AndingUser::where('phone', $phone)->find();
        if (!$user) {
            ApiException::throws(ApiException::$loginUserNotFound);
        }
        $bind = ThirdBind::where('type', 'phone')
            ->where('account', $phone)->find();
        if (!$bind) {
            ApiException::throws(ApiException::$loginUserNotFound);
        }
        //最大登录失败错误次数
        $max_fail = 100;
        if ($bind->login_fail >= $max_fail) {
            ApiException::throws(ApiException::$userLocked);
        }
        $passwordHash = $this->hashPassword($password);
        if ($bind->password !== $passwordHash) {
            $bind->login_fail += 1;
            $bind->save();
            ApiException::throws(ApiException::$passwordError);
        }
        $bind->login_fail = 0;
        $bind->save();
        $this->setUser($user->id);
        return $this->getDriver()->login($user->id);
    }

    /**
     * @param $code
     * @return mixed
     * @throws \Exception
     */
    public function loginWx($code)
    {
        //对接微信登录
        $rs = [];
        if (!$rs || !isset($rs['openid'])) {
            ApiException::throws(ApiException::$wxAuthError);
        }
        $openId = $rs['openid'];
        //code微信接口获取openid
        $bind = ThirdBind::where('type', 'weixin')
            ->where('account', $openId)->find();
        if (!$bind) {
            //增加
            $model = new ThirdBind([
                'type' => 'weixin',
                'account' => $openId,
                'context' => $rs,
            ]);
            $rs = $model->save();
            if (!$rs) {
                ApiException::throws(ApiException::$loginFail);
            }
            return $this->getDriver()->login('bind_' . $model->id);
        }

        if (!$bind->uid) {
            return $this->getDriver()->login('bind_' . $bind->uid);
        }

        $user = AndingUser::where('enable', 1)->find($bind->uid);
        if (!$user) {
            ApiException::throws(ApiException::$loginFail);
        }
        return $this->getDriver()->login($bind->uid);
    }

    /**
     * @param $refreshToken
     * @return bool
     * @throws \app\anding\exceptions\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function refreshToken($refreshToken)
    {
        if (!$refreshToken) {
            ApiException::throws(ApiException::$needLogin);
        }
        /** @var AndingUser $andingUser */
        $andingUser = AndingUser::where('enable', 1)
            ->where('refresh_token', $refreshToken)->find();

        if (!$andingUser || !$andingUser->refreshTokenValid()) {
            ApiException::throws(ApiException::$refreshTokenExpire);
        }
        return $this->getDriver()->login($andingUser->id);
    }

    /**
     * 短信密码登录
     * @param $phone
     * @param $code
     * @return mixed
     * @throws \Exception
     */
    public function loginPhoneCode($phone, $code)
    {
        if (!$code || !$phone) {
            ApiException::throws(ApiException::$loginNeedPhone);
        }
        /** @var AndingUser $user */
        $user = AndingUser::where('phone', $phone)->find();
        /*$phoneCode = ThirdPhoneCode::where('phone', $phone)
            ->where('type', 'login')
            ->where('status', 1);
        if (!$phoneCode) {
            ApiException::throws(ApiException::$loginNeedCode);
        }
        if (!$phoneCode->checkCode($code)) {
            ApiException::throws(ApiException::$loginCodeError);
        }*/
        if (!$user) {
            //自动注册
            $user = UserLogic::getInstance()->create([
                'phone' => $phone,
            ]);
//            ApiException::throws(ApiException::$loginUserNotFound);
        }
        return $this->getDriver()->login($user->id);
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
        return md5(md5($password . '__ANDING_USR__'));
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

}