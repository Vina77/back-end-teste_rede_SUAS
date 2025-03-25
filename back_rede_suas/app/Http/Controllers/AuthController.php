<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AuthController extends Controller
{
    private $arquivoUsuarios;

    public function __construct()
    {
        $this->arquivoUsuarios = storage_path('app/usuarios.json');
    }

    private function lerUsuarios()
    {
        // Criar arquivo se não existir
        if (!File::exists($this->arquivoUsuarios)) {
            File::put($this->arquivoUsuarios, json_encode([]));
        }

        // Ler conteúdo do arquivo
        $conteudo = File::get($this->arquivoUsuarios);
        return json_decode($conteudo, true) ?: [];
    }

    private function salvarUsuarios($usuarios)
    {
        File::put($this->arquivoUsuarios, json_encode($usuarios, JSON_PRETTY_PRINT));
    }

    // Endpoint para registrar usuário (/registrar)
    public function registrar(Request $request)
    {
        // Validação básica
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'dt_nascimento' => 'required|date',
            'senha' => 'required|min:5'
        ]);

        // Verificar se a validação falhou
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 400);
        }

        $dataNascimento = Carbon::parse($request->input('dt_nascimento'));
        $idade = $dataNascimento->age;

        // Verificar se é maior de 18 anos
        if ($idade < 18) {
            return response()->json(['error' => 'Usuário deve ter pelo menos 18 anos'], 400);
        }

        $usuarios = $this->lerUsuarios();

        // Verificar se usuário já existe
        $emailExistente = false;
        foreach ($usuarios as $usuario) {
            if ($usuario['email'] === $request->input('email')) {
                $emailExistente = true;
                break;
            }
        }

        if ($emailExistente) {
            return response()->json(['error' => 'E-mail já cadastrado'], 400);
        }

        $novoUsuario = [
            'id' => count($usuarios) + 1,
            'email' => $request->input('email'),
            'senha' => password_hash($request->input('senha'), PASSWORD_DEFAULT),
            'dt_nascimento' => $request->input('dt_nascimento')
        ];
        
        $usuarios[] = $novoUsuario;

        // Salvar usuários no arquivo JSON
        $this->salvarUsuarios($usuarios);

        return response()->json([
            'message' => 'Usuário registrado com sucesso', 
            'usuario' => $novoUsuario
        ], 201);
    }

    // Endpoint para login (/acessar)
    public function acessar(Request $request)
    {
        $email = $request->input('email');
        $senha = $request->input('senha');

        $usuarios = $this->lerUsuarios();

        // Buscar usuário
        $usuarioEncontrado = null;
        foreach ($usuarios as $usuario) {
            if ($usuario['email'] === $email && 
                password_verify($senha, $usuario['senha'])) {
                $usuarioEncontrado = $usuario;
                break;
            }
        }

        // Verificar se usuário foi encontrado
        if ($usuarioEncontrado) {
            return response()->json([
                'message' => 'Autenticação bem-sucedida', 
                'usuario' => $usuarioEncontrado
            ]);
        }

        return response()->json(['error' => 'Usuário ou senha inválidos'], 401);
    }

    // Endpoint para listar usuários (/listagem-usuarios)
    public function listagemUsuarios()
    {
        $usuarios = $this->lerUsuarios();
        
        $usuariosSemSenha = array_map(function($usuario) {
            unset($usuario['senha']);
            return $usuario;
        }, $usuarios);

        return response()->json($usuariosSemSenha);
    }
}