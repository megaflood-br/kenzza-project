<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['nome' => 'required|unique:categories,nome']);

        Category::create([
            'nome' => $request->nome,
            'slug' => Str::slug($request->nome)
        ]);

        return back()->with('success', 'Categoria criada com sucesso!');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Não é possível excluir uma categoria que possui produtos vinculados.');
        }

        $category->delete();
        return back()->with('success', 'Categoria excluída!');
    }
}
