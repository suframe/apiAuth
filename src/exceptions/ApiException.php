<?php

namespace suframe\apiAuth\exceptions;

class ApiException extends BaseException
{
    static $validError = [40001, '验证错误'];
    static $requireArgs = [40002, '参数错误'];
    static $infoNotFound = [40004, '信息不存在'];

    static $needLogin = [50001, '请登录'];
    static $loginUserNotFound = [50002, '用户不存在'];
    static $loginNeedPhone = [50003, '请先发送验证码'];
    static $loginNeedCode = [50004, '请先发送验证码'];
    static $loginCodeError = [50004, '验证码错误，请重新输入'];
    static $loginFail = [50005, '登录失败'];
    static $userLocked = [50006, '账户锁定，请联系管理员'];
    static $passwordError = [50007, '密码错误'];
    static $refreshTokenExpire = [50008, '长时间未登录，请重新登录'];
    static $phoneRepeat = [50009, '手机号已被注册'];

    static $wxAuthError = [50010, '微信授权错误'];


    static $smsRepeat = [50020, '短信发送中，请勿重复发送'];
    static $smsSendError = [50021, '短信发送失败'];

    static $taskNoRights = [50030, '无任务权限'];

}