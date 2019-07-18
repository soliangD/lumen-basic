<?php

namespace Api\Controllers\User;

use Api\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;

class UserController extends BaseController
{
    public function info()
    {
        return $this->resultSuccess(Auth::user());
    }
}
