<?php

namespace App\Traits;

use Illuminate\Support\Facades\Session;

trait FlashTrait
{
    public function forgetFlash(string $type)
    {
        Session::forget($type);
    }
}
