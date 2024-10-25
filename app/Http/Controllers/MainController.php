<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Home;
use App\Models\Partner;
use App\Models\Plan;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        $data['latestHome'] = Home::where('status', 1)->latest('created_at')->first();
        $data['categorias'] = Category::all();
        $data['planos'] = Plan::all();
        $data['parceiros'] = Partner::all();
        $data['cursos'] = Advertisement::where('location', 'C')->get();
        $data['publicidades'] = Advertisement::where('location', 'P')->get();

        return view('pages.home', $data);
    }
}
