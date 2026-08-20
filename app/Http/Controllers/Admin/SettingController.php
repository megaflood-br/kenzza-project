<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        $margins = DB::table('settings')->whereIn('key', ['margin_salon', 'margin_consumer'])->pluck('value', 'key');
        return view('admin.settings.margins', compact('margins'));
    }

    public function updateMargins(Request $request)
    {
        DB::table('settings')->where('key', 'margin_salon')->update(['value' => $request->margin_salon]);
        DB::table('settings')->where('key', 'margin_consumer')->update(['value' => $request->margin_consumer]);

        return back()->with('success', 'Margens de lucro atualizadas!');
    }
}
