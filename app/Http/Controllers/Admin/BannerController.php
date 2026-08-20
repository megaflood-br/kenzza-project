<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('ordem')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'nullable|string|max:255',
            'link' => 'nullable|url',
            'ordem' => 'required|integer',
            'imagem_desktop' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'imagem_mobile' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['imagem_desktop'] = $request->file('imagem_desktop')->store('banners', 'public');
        $data['imagem_mobile'] = $request->file('imagem_mobile')->store('banners', 'public');

        Banner::create($data);
        return redirect()->route('banners.index')->with('success', 'Banner criado!');
    }

    // --- NOVA FUNÇÃO: EDITAR ---
    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    // --- NOVA FUNÇÃO: UPDATE ---
    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'titulo' => 'nullable|string|max:255',
            'link' => 'nullable|url',
            'ordem' => 'required|integer',
            'imagem_desktop' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'imagem_mobile' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('imagem_desktop')) {
            Storage::disk('public')->delete($banner->imagem_desktop);
            $data['imagem_desktop'] = $request->file('imagem_desktop')->store('banners', 'public');
        }

        if ($request->hasFile('imagem_mobile')) {
            Storage::disk('public')->delete($banner->imagem_mobile);
            $data['imagem_mobile'] = $request->file('imagem_mobile')->store('banners', 'public');
        }

        $banner->update($data);
        return redirect()->route('banners.index')->with('success', 'Banner atualizado!');
    }

    public function destroy(Banner $banner)
    {
        Storage::disk('public')->delete([$banner->imagem_desktop, $banner->imagem_mobile]);
        $banner->delete();
        return redirect()->route('banners.index')->with('success', 'Banner removido!');
    }
}
