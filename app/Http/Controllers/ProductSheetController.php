<?php

namespace App\Http\Controllers;

use App\Models\ProductSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // Importação da biblioteca de QR Code

class ProductSheetController extends Controller
{
    public function index()
    {
        $sheets = ProductSheet::orderBy('nome', 'asc')->get();

        // Mantendo a lógica de views separadas por cargo
        if (Auth::user()->role === 'distributor') {
            return view('distributor.sheets', compact('sheets'));
        }

        return view('sheets.index', compact('sheets'));
    }

    public function create()
    {
        return view('sheets.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'              => 'required|string|max:255',
            'descricao'         => 'required|string',
            'ativos_tecnologia' => 'required|string',
            'funcoes'           => 'required|string',
            'diferenciais'      => 'required|string', // <-- CORREÇÃO: Adicionado
            'modo_usar'         => 'required|string',
            'dados_analiticos'  => 'required|string',
            'seguranca'         => 'required|string',
            'foto'              => 'required|image|mimes:jpg,jpeg,png|max:10240',
            'pdf'               => 'required|mimes:pdf|max:20480',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('products/images', 'public');
        }

        if ($request->hasFile('pdf')) {
            $data['pdf'] = $request->file('pdf')->store('products/pdfs', 'public');
        }

        ProductSheet::create($data);

        return redirect()->route('sheets.index')->with('success', 'Ficha técnica criada com sucesso!');
    }

    public function edit($id)
    {
        $sheet = ProductSheet::findOrFail($id);
        return view('sheets.edit', compact('sheet'));
    }

    public function update(Request $request, $id)
    {
        $sheet = ProductSheet::findOrFail($id);

        $data = $request->validate([
            'nome'              => 'required|string|max:255',
            'descricao'         => 'required|string',
            'ativos_tecnologia' => 'required|string',
            'funcoes'           => 'required|string',
            'diferenciais'      => 'required|string', // <-- CORREÇÃO: Adicionado
            'modo_usar'         => 'required|string',
            'dados_analiticos'  => 'required|string',
            'seguranca'         => 'required|string',
            'foto'              => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'pdf'               => 'nullable|mimes:pdf|max:20480',
        ]);

        if ($request->hasFile('foto')) {
            if ($sheet->foto) { Storage::disk('public')->delete($sheet->foto); }
            $data['foto'] = $request->file('foto')->store('products/images', 'public');
        }

        if ($request->hasFile('pdf')) {
            if ($sheet->pdf) { Storage::disk('public')->delete($sheet->pdf); }
            $data['pdf'] = $request->file('pdf')->store('products/pdfs', 'public');
        }

        $sheet->update($data);

        return redirect()->route('sheets.index')->with('success', 'Ficha técnica atualizada!');
    }

    /**
     * EXIBE A FICHA COM QR CODE DINÂMICO
     */
    public function show($id)
    {
        $sheet = ProductSheet::findOrFail($id);

        // QR Code para o RÓTULO (Aponta para a rota pública)
        $qrCode = QrCode::size(180)
            ->color(184, 134, 11) // Cor #B8860B
            ->margin(1)
            ->generate(route('sheets.show.public', $id));

        return view('sheets.show', compact('sheet', 'qrCode'));
    }

    public function destroy($id)
    {
        $sheet = ProductSheet::findOrFail($id);

        if ($sheet->foto) { Storage::disk('public')->delete($sheet->foto); }
        if ($sheet->pdf) { Storage::disk('public')->delete($sheet->pdf); }

        $sheet->delete();

        return redirect()->route('sheets.index')->with('success', 'Ficha técnica excluída!');
    }

    public function showPublic($id)
    {
        $sheet = ProductSheet::findOrFail($id);

        // Gera o QR Code apontando para esta própria rota pública
        $qrCode = QrCode::size(150)
            ->color(184, 134, 11)
            ->margin(1)
            ->generate(route('sheets.show.public', $id));

        return view('sheets.show_public', compact('sheet', 'qrCode'));
    }
}
