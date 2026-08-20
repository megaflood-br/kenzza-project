<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
{
    \Illuminate\Support\Facades\View::composer(['layouts.store', 'ecommerce.*'], function ($view) {
        // Esta query garante que SÓ apareçam categorias que tenham produtos NÃO deletados
        $categories = \App\Models\Category::whereHas('products', function ($query) {
            $query->whereNull('deleted_at'); // Filtro rigoroso de ativos
        })->get();

        $view->with('categories', $categories);
    });
}
}
