<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Home;
use App\Models\Partner;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    public function index()
    {

        return view('pages.home');
    }

    public function teste()
    {
        $user = Auth::user();
        dd($user);
    }

}
