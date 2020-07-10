<?php
declare (strict_types=1);

namespace suframe\apiAuth\controller;

use app\BaseController;

abstract class Base extends BaseController
{

    protected function getUid()
    {
        return $this->request->get('__UID__');
    }

    protected $user;

}
