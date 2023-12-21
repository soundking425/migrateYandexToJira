<?php

namespace App\Service;

interface TokenInterdace
{
    public function getToken();

    public function setToken($token);

    public function requestToken();

}