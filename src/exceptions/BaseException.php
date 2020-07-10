<?php

namespace suframe\apiAuth\exceptions;


use think\Exception;

abstract class BaseException extends Exception
{
    /**
     * @param array $info
     * @param null $label
     * @param array $data
     * @throws static
     */
    public static function throws(array $info, array $data = [], string $label = 'errors'): void
    {
        $e = new static($info[1], $info[0]);
        if($label && $data){
            $e->setData($label, $data);
        }
        throw $e;
    }

}