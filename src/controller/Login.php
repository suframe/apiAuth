<?php
declare (strict_types = 1);

namespace suframe\apiAuth\controller;

use suframe\apiAuth\Auth;

class login extends Base
{

    /**
     * @return \think\response\Json
     * @throws \Exception
     */
    public function index()
    {
        $post = $this->request->post();
        $uid = 0;
        $auth = new Auth();
        $rs = $auth->login($uid);
        return json_return($rs);
    }

}
