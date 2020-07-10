<?php
declare (strict_types=1);

namespace suframe\apiAuth\controller;

use app\anding\exceptions\ApiException;
use app\anding\model\AndingUser;
use app\BaseController;
use think\Exception;

abstract class Base extends BaseController
{

    protected function getUid()
    {
        return $this->request->get('__UID__');
    }

    protected $user;

    /**
     * @return AndingUser|array|\think\Model
     * @throws \app\anding\exceptions\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function getUser()
    {
        if ($this->user) {
            return $this->user;
        }
        $uid = $this->getUid();
        if (!$uid) {
            ApiException::throws(ApiException::$needLogin);
        }
        $this->user = AndingUser::find($uid);
        if (!$this->user) {
            ApiException::throws(ApiException::$needLogin);
        }
        return $this->user;
    }

    /**
     * 结果监控
     * @param $rs
     * @param string $msg
     * @param string $errorMsg
     * @param int $code
     * @param array $data
     * @return \think\response\Json
     */
    protected function handleResponse($rs, $msg = '', $errorMsg = '', $code = 50000, $data = [])
    {
        if (is_bool($rs)) {
            return $rs ? json_success($msg, true) : json_error($errorMsg, $code, false);
        }
        return json_return($rs);
    }

    /**
     * 异常监控返回
     * @param \Exception $e
     * @return \think\response\Json
     */
    protected function handleException(\Exception $e)
    {
        if ($e instanceof Exception) {
            json_error($e->getMessage(), $e->getCode(), $e->getData());
        }
        return json_error($e->getMessage(), $e->getCode());
    }

    protected function getRequestPageNum()
    {
        $num = $this->request->get('num', 10);
        if ($num > 100) {
            $num = 100;
        }
        return $num;
    }

    /**
     * @param $keys
     * @return array
     * @throws \app\anding\exceptions\BaseException
     */
    protected function getPostFromRequest($keys)
    {
        $rs = $this->request->post($keys);
        if (!$rs) {
            ApiException::throws(ApiException::$requireArgs);
        }
        return $rs;
    }
}
