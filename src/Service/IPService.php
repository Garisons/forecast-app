<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

class IPService
{
    public function get(Request $request = null): string
    {
        if (!$request) {
            $request = Request::createFromGlobals();
        }
        return $request->getClientIp();
    }
}
