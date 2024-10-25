<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Home;
use App\Models\Plan;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        $data['latestHome'] = Home::where('status', 1)->latest('created_at')->first();
        $data['categorias'] = Category::all();
        $data['planos'] = Plan::all();

        return view('pages.home', $data);
        // return view('layouts.bannerPrincipal', $data);
        // return view('layouts.informacoes', $data);
        // return view('layouts.informacoes', $data);
    }
}
