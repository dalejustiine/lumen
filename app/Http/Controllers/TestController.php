<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class TestController extends BaseController
{
    public function hello() {
        $result = 'hello';

        return $this->response->array($result);
    }
}
