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

    public function index(Request $request)
    {
        $data['latestHome'] = Home::where('status', 1)->latest('created_at')->first();
        $data['types'] = ['I' => 'Imóveis', 'A' => 'Automóveis', 'O' => 'Outros'];
        $data['categorias'] = Category::all();
        $data['filters'] = $request->only([
            'type',
            'category',
            'description',
        ]);

        $validated = $request->validate([
            'type' => 'nullable|in:I,A,O',
            'category' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:255',
        ]);

        $query = Product::with('category');

        if ($request->filled('type')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('type', $request->type);
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('description')) {
            $searchTerm = $request->description;

            $query->where(function ($q) use ($searchTerm) {
                $fieldsToSearch = [
                    'id',
                    'title',
                    'price',
                    'make',
                    'model',
                    'year',
                    'mileage',
                    'fuel_type',
                    'number_of_doors',
                    'property_type',
                    'number_of_rooms',
                    'number_of_bathrooms',
                    'area',
                    'build_area',
                    'location',
                    'number_of_floors',
                    'breed',
                    'age',
                    'gender',
                    'weight',
                    'unit_price',
                    'quality',
                    'city',
                    'state',
                ];

                foreach ($fieldsToSearch as $field) {
                    if (in_array($field, ['id', 'price', 'year', 'mileage', 'number_of_doors', 'number_of_rooms', 'number_of_bathrooms', 'area', 'build_area', 'number_of_floors', 'age', 'weight', 'unit_price'])) {
                        // Campos numéricos: usar '=' ou considerar conversão
                        if (is_numeric($searchTerm)) {
                            $q->orWhere($field, $searchTerm);
                        }
                    } else {
                        // Campos de texto: usar 'LIKE'
                        $q->orWhere($field, 'like', '%' . $searchTerm . '%');
                    }
                }
            });
        }

        $data['produtos'] = $query->get();

        return view('pages.produtos', $data);
    }

    public function showProduct($slug, $id) {}
}
