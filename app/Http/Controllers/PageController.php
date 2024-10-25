<?php

namespace App\Http\Controllers;

use App\Models\Home;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug)
    {
        // Busca a página com base no slug
        $data['page'] = Page::where('slug', $slug)->firstOrFail();
        $data['latestHome'] = Home::where('status', 1)->latest('created_at')->first();

        // Retorna a view com os dados da página
        return view('pages.dinamica', $data);
    }
}
