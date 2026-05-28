<?php

namespace App\Http\Controllers;

use App\Services\TermooService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TermooController extends Controller
{
    public function __construct(private TermooService $termooService)
    {
    }

    /**
     * POST /api/iniciar-jogo
     * Inicia uma nova partida do Termoo e retorna o ID do jogo.
     */
    public function iniciarJogo(Request $request): JsonResponse
    {
        $jogo = $this->termooService->iniciarJogo();

        return response()->json($jogo, 200);
    }

    /**
     * POST /api/validar-tentativa
     * Valida a tentativa do jogador e retorna o resultado letra a letra.
     */
    public function validarTentativa(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'idJogo'  => 'required|string',
            'palavra' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'erro' => 'Requisição inválida.',
                'detalhes' => $validator->errors(),
            ], 400);
        }

        $idJogo  = $request->input('idJogo');
        $palavra = $request->input('palavra');

        // Verifica se o jogo existe
        $jogo = $this->termooService->buscarJogo($idJogo);
        if (! $jogo) {
            return response()->json([
                'erro' => 'Jogo não encontrado.',
            ], 404);
        }

        // Validações de negócio — 400 Bad Request
        $tamanho = $jogo['tamanhoPalavra'];
        $palavraNormalizada = $this->termooService->normalizarPalavra($palavra);

        if (mb_strlen($palavraNormalizada) !== $tamanho) {
            return response()->json([
                'erro'         => "A palavra deve ter {$tamanho} letras.",
                'palavraValida' => false,
            ], 400);
        }

        if ($jogo['tentativasRestantes'] <= 0) {
            return response()->json([
                'erro' => 'Número máximo de tentativas atingido.',
            ], 400);
        }

        if ($jogo['venceu']) {
            return response()->json([
                'erro' => 'Este jogo já foi concluído com sucesso.',
            ], 400);
        }

        // Verifica se a palavra está no dicionário
        if (! $this->termooService->palavraExiste($palavraNormalizada)) {
            return response()->json([
                'resultado'          => [],
                'venceu'             => false,
                'tentativasRestantes' => $jogo['tentativasRestantes'],
                'palavraValida'       => false,
            ], 200);
        }

        // Processa a tentativa
        $resultado = $this->termooService->validarTentativa($idJogo, $palavraNormalizada);

        return response()->json($resultado, 200);
    }
}
