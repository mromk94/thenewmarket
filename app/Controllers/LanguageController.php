<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Lang;

class LanguageController
{
    public function switch(): void
    {
        $code = Request::input('code', '');
        Lang::set($code);
        $to = Request::input('to', '/');
        Response::redirect($to);
    }
}
