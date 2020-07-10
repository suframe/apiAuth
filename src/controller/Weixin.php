<?php
declare (strict_types = 1);

namespace app\anding\controller\api\auth;

use app\anding\controller\api\Base;
use app\anding\middleware\auth\Auth;

class Weixin extends Base
{

    /**
     * @return \think\response\Json
     * @throws \Exception
     */
    public function index()
    {
        $code = $this->request->post('code');
        $auth = new Auth();
        $rs = $auth->loginWx($code);
        return json_return($rs);
    }

}
