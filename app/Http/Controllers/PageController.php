<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('frontend.pages.about');
    }

    public function howTo()
    {
        return view('frontend.pages.cara-nitip');
    }

    public function contact()
    {
        return view('frontend.pages.contact');
    }

    public function terms()
    {
        return view('frontend.pages.syarat-ketentuan');
    }

    public function privacy()
    {
        return view('frontend.pages.kebijakan-privasi');
    }
}