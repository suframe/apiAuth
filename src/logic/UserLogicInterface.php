<?php
namespace suframe\apiAuth\logic;

interface UserLogicInterface
{
    public function getModel();

    public function login(array $post);
}