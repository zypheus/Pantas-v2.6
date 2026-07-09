<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PublicRegistrationController extends Controller
{
    public function choose(): View
    {
        return view('pending.choose');
    }
}
