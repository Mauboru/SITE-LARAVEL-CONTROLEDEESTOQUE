<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Categoria;
use App\Models\Unidade;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProdutosExport;

class ProdutoController extends Controller {
    public function index(Request $request)
    {
        $produtos = Produto::all();
        $categorias = Categoria::all();
        $unidades = Unidade::all();
        return view('produtos.index', compact('produtos', 'categorias', 'unidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'categoria_id' => 'required|exists:categorias,id',
            'unidade_de_medida_id' => 'required|exists:unidades,id',
            'imagem' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'quantidade' => 'required|integer',
            'estoque' => 'required|integer',
            'descricao' => 'required|string',
            'valor_unitario' => 'required|numeric',
        ]);

        $produto = new Produto();
        $produto->nome = $request->nome;
        $produto->categoria_id = $request->categoria_id;
        $produto->unidade_de_medida_id = $request->unidade_de_medida_id;
        $produto->quantidade = $request->quantidade;
        $produto->estoque = $request->estoque;
        $produto->descricao = $request->descricao;
        $produto->valor_unitario = $request->valor_unitario;

        $produto->caminho = 'produtos/' . uniqid() . '.jpg';

        if ($request->hasFile('imagem')) {
            $produto->salvarImagem($request->file('imagem'));
        }

        $produto->save();

        return redirect()->route('produtos.index')->with('success', 'Produto cadastrado com sucesso.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'categoria_id' => 'required|exists:categorias,id',
            'unidade_de_medida_id' => 'required|exists:unidades,id',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'quantidade' => 'required|integer',
            'estoque' => 'required|integer',
            'descricao' => 'required|string',
            'valor_unitario' => 'required|numeric',
        ]);

        $produto = Produto::findOrFail($id);
        $produto->nome = $request->nome;
        $produto->categoria_id = $request->categoria_id;
        $produto->unidade_de_medida_id = $request->unidade_de_medida_id;
        $produto->quantidade = $request->quantidade;
        $produto->estoque = $request->estoque;
        $produto->descricao = $request->descricao;
        $produto->valor_unitario = $request->valor_unitario;

        if ($request->hasFile('imagem')) {
            $produto->salvarImagem($request->file('imagem'));
        }

        $produto->save();

        return redirect()->route('produtos.index')->with('success', 'Produto atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $produto = Produto::findOrFail($id);
        $produto->delete();

        return redirect()->route('produtos.index')->with('success', 'Produto excluído com sucesso.');
    }
}
