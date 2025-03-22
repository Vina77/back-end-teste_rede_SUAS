<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Endpoint para login (/acessar)
    public function acessar(Request $request)
    {
        // Simulando um banco de dados fictício
        $usuarios = [
            ['email' => 'usuario@example.com', 'senha' => '123456'],
            ['email' => 'admin@example.com', 'senha' => 'admin123']
        ];

        $email = $request->input('email');
        $senha = $request->input('senha');

        // Verificando se o usuário existe
        foreach ($usuarios as $usuario) {
            if ($usuario['email'] === $email && $usuario['senha'] === $senha) {
                return response()->json(['message' => 'Autenticação bem-sucedida', 'email' => $email]);
            }
        }

        return response()->json(['error' => 'Usuário ou senha inválidos'], 401);
    }

    // Endpoint para registrar usuário (/registrar)
    public function registrar(Request $request)
    {
        // Simulando um banco fictício com um array
        $usuariosExistentes = ['usuario@example.com', 'admin@example.com'];

        $email = $request->input('email');
        $dt_nascimento = $request->input('dt_nascimento');
        $senha = $request->input('senha');

        // Validando idade
        $dataNascimento = new \DateTime($dt_nascimento);
        $hoje = new \DateTime();
        $idade = $hoje->diff($dataNascimento)->y;

        if ($idade < 18) {
            return response()->json(['error' => 'Usuário deve ter pelo menos 18 anos'], 400);
        }

        // Verificando se o e-mail já está registrado
        if (in_array($email, $usuariosExistentes)) {
            return response()->json(['error' => 'E-mail já cadastrado'], 400);
        }

        // Simulando inserção no banco de dados fictício
        return response()->json(['message' => 'Usuário registrado com sucesso'], 201);
    }

    // Endpoint para listar usuários (/listagem-usuarios)
    public function listagemUsuarios()
    {
        // Simulando um banco de dados fictício
        $usuarios = [
            ['id' => 1, 'email' => 'usuario@example.com', 'dt_nascimento' => '2000-01-01'],
            ['id' => 2, 'email' => 'admin@example.com', 'dt_nascimento' => '1995-06-15']
        ];

        return response()->json($usuarios);
    }
}