<?php

namespace App\Http\Controllers;

use App\Models\MediaKit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MediaKitController extends Controller
{
    /**
     * View do Distribuidor (Cards com fotos)
     */
    public function index(Request $request)
    {
        $categoriasDisponiveis = MediaKit::distinct()->pluck('categoria')->sort();
        $query = MediaKit::query();

        if ($request->has('categoria') && $request->categoria != '') {
            $query->where('categoria', $request->categoria);
        }

        $medias = $query->get()->groupBy('categoria');

        return view('distributor.media-kit', compact('medias', 'categoriasDisponiveis'));
    }

    /**
     * View de Gerenciamento do Admin (Tabela de exclusão)
     */
    public function adminIndex()
    {
        abort_if(Auth::user()->role !== 'admin' && Auth::user()->role !== 'editor', 403);

        // CORREÇÃO: Agrupando também no Admin para o count() e o layout da View funcionarem
        $medias = MediaKit::orderBy('created_at', 'desc')->get()->groupBy('categoria');

        return view('media-kit.index', compact('medias'));
    }

    /**
     * Salvar novo arquivo
     */
    public function store(Request $request)
    {
        abort_if(Auth::user()->role !== 'admin' && Auth::user()->role !== 'editor', 403);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'categoria' => 'required|string',
            'arquivo' => 'required|file|max:20480',
        ]);

        if ($request->hasFile('arquivo')) {
            $path = $request->file('arquivo')->store('media-kit', 'public');

            MediaKit::create([
                'titulo' => $request->titulo,
                'categoria' => $request->categoria,
                'arquivo_path' => $path,
                'tipo' => $request->file('arquivo')->getClientOriginalExtension(),
            ]);
        }

        return back()->with('success', 'Arquivo enviado com sucesso!');
    }

    /**
     * Deletar arquivo
     */
    public function destroy(MediaKit $media)
    {
        abort_if(Auth::user()->role !== 'admin' && Auth::user()->role !== 'editor', 403);

        if ($media->arquivo_path) {
            Storage::disk('public')->delete($media->arquivo_path);
        }

        $media->delete();
        return back()->with('success', 'Arquivo removido!');
    }
}
