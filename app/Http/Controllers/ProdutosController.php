<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Home;
use App\Models\Product;
use Illuminate\Http\Request;

class ProdutosController extends Controller
{
    public function show($slug)
    {
        $idCategoria = Category::where('slug', $slug)->first()->id;
        $data['produtos'] = Product::where('category_id', $idCategoria)->get();

        $data['teste'] = 'Oi';
        $data['categoria'] = Category::where('slug', $slug)->first();
        $data['latestHome'] = Home::where('status', 1)->latest('created_at')->first();

        return view('pages.produtosFiltro', $data);
    }

    public function showProduct($slug, $id) {}
}
