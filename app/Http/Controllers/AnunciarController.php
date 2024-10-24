<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnunciarController extends Controller
{
    public function index()
    {
        return view('pages.anunciar');
    }
}
