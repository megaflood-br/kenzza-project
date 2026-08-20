<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Category;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::with('categories')->latest()->get();
        $categories = Category::all();

        return view('admin.coupons.index', compact('coupons', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|unique:coupons,codigo',
            'tipo' => 'required|in:percentual,frete_gratis',
            'valor' => 'nullable|numeric',
            'validade' => 'nullable|date',
            'limite_uso' => 'nullable|integer',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        $coupon = Coupon::create([
            'codigo' => strtoupper($request->codigo),
            'tipo' => $request->tipo,
            'valor' => $request->tipo == 'percentual' ? $request->valor : 0,
            'validade' => $request->validade,
            'limite_uso' => $request->limite_uso ?? 0,
            'vezes_usado' => 0,
            'ativo' => true
        ]);

        if ($request->has('category_ids')) {
            $coupon->categories()->sync($request->category_ids);
        }

        return redirect()->route('coupons.index')->with('success', 'Cupom criado!');
    }

    /**
     * NOVO MÉTODO: Processa a atualização do cupom e suas categorias vinculadas
     */
    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'codigo' => 'required|unique:coupons,codigo,' . $coupon->id,
            'tipo' => 'required|in:percentual,frete_gratis',
            'valor' => 'nullable|numeric',
            'validade' => 'nullable|date',
            'limite_uso' => 'nullable|integer',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        $coupon->update([
            'codigo' => strtoupper($request->codigo),
            'tipo' => $request->tipo,
            'valor' => $request->tipo == 'percentual' ? $request->valor : 0,
            'validade' => $request->validade,
            'limite_uso' => $request->limite_uso ?? 0,
        ]);

        // Sincroniza as categorias (se vier vazio, o sync limpa a tabela pivô deixando o cupom geral)
        $coupon->categories()->sync($request->input('category_ids', []));

        return redirect()->route('coupons.index')->with('success', 'Cupom atualizado com sucesso!');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->back()->with('success', 'Cupom removido!');
    }

    public function toggle(Coupon $coupon)
    {
        $coupon->update(['ativo' => !$coupon->ativo]);
        return redirect()->back();
    }
}
