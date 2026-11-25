<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class SocialController extends Controller
{
    public function index(): View { return view('social.index'); }
    public function discover(): View { return view('social.discover'); }
    public function explore(): View { return view('social.explore'); }
    public function friends(): View { return view('social.friends'); }
    public function requests(): View { return view('social.requests'); }
    public function profile(): View { return view('social.profile'); }
}
