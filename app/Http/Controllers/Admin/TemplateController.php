<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    // MENAMPILKAN HALAMAN ADMIN TEMPLATE
    public function index()
    {
        $templates = Template::latest()->get();
        return view('admin.templates.index', compact('templates'));
    }

    // MENYIMPAN DATA BARU
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'kategori' => 'required',
            'link_drive' => 'required|url', // Wajib format URL
        ]);

        Template::create($request->all());

        return redirect()->back()->with('success', 'Template berhasil ditambahkan!');
    }

    // MENGUPDATE DATA (Ganti Link/Judul)
    public function update(Request $request, $id)
    {
        $template = Template::findOrFail($id);
        $template->update($request->all());

        return redirect()->back()->with('success', 'Data berhasil diperbarui!');
    }

    // MENGHAPUS DATA
    public function destroy($id)
    {
        Template::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Template dihapus!');
    }
}