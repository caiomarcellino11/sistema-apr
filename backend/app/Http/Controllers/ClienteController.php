<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
   public function index(Request $request)
{
    $clientes = $request->user()
        ->clientes()
        ->orderBy('nome')
        ->get();

    return response()->json($clientes);
}

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome'            => 'required|string|max:255',
            'endereco'        => 'required|string|max:255',
            'numero'          => 'required|string|max:50',
            'complemento'     => 'nullable|string|max:255',
            'bairro'          => 'required|string|max:255',
            'cidade'          => 'required|string|max:255',
            'estado'          => 'required|string|max:2',
            'tel_residencial' => 'nullable|string|max:30',
            'tel_celular'     => 'required|string|max:30',
            'email'           => 'required|email|max:255',
        ]);

       $cliente = $request->user()
        ->clientes()
        ->create($dados);

        return response()->json($cliente, 201);
    }

  public function show(Request $request, string $id)
{
    $cliente = $request->user()
        ->clientes()
        ->findOrFail($id);

    return response()->json($cliente);
}

    public function update(Request $request, string $id)
    {
       $cliente = $request->user()
         ->clientes()
         ->findOrFail($id);

        $dados = $request->validate([
            'nome'            => 'sometimes|required|string|max:255',
            'endereco'        => 'sometimes|required|string|max:255',
            'numero'          => 'sometimes|required|string|max:50',
            'complemento'     => 'nullable|string|max:255',
            'bairro'          => 'sometimes|required|string|max:255',
            'cidade'          => 'sometimes|required|string|max:255',
            'estado'          => 'sometimes|required|string|max:2',
            'tel_residencial' => 'nullable|string|max:30',
            'tel_celular'     => 'sometimes|required|string|max:30',
            'email'           => 'sometimes|required|email|max:255',
        ]);

        $cliente->update($dados);

        return response()->json($cliente);
    }

  public function destroy(Request $request, string $id)
{
    $cliente = $request->user()
        ->clientes()
        ->findOrFail($id);

    $cliente->delete();

    return response()->json([
        'mensagem' => 'Cliente excluído com sucesso.'
    ]);
}
}