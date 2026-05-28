<?php

namespace App\Http\Controllers;

use App\Services\TermooService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controller = "porteiro" da API.
 *
 * Recebe as requisições HTTP do front (site do professor ou Postman),
 * valida os dados enviados e repassa a lógica do jogo para o TermooService.
 *
 * Não contém a regra do jogo em si — só organiza entrada e saída (JSON + código HTTP).
 */
class TermooController extends Controller
{
    /**
     * Injeta o TermooService automaticamente (Laravel cria a instância).
     */
    public function __construct(private TermooService $termooService)
    {
    }

    /**
     * POST /api/iniciar-jogo
     *
     * Chamado quando o jogador começa uma partida nova.
     * Sorteia a palavra secreta, salva o jogo e devolve o idJogo para as próximas tentativas.
     */
    public function iniciarJogo(Request $request): JsonResponse
    {
        $jogo = $this->termooService->iniciarJogo();

        // 200 = sucesso; corpo em JSON
        return response()->json($jogo, 200);
    }

    /**
     * POST /api/validar-tentativa
     *
     * Body esperado: { "idJogo": "...", "palavra": "carro" }
     *
     * Compara a palavra chutada com a secreta e retorna correta/presente/ausente por letra.
     * Este endpoint segue o enunciado da disciplina (/api/...).
     */
    public function validarTentativa(Request $request): JsonResponse
    {
        // Valida se os campos obrigatórios vieram no JSON
        $validator = Validator::make($request->all(), [
            'idJogo'  => 'required|string',
            'palavra' => 'required|string',
        ], [
            'idJogo.required'  => 'Informe o id do jogo.',
            'idJogo.string'    => 'O id do jogo deve ser um texto.',
            'palavra.required' => 'Informe a palavra.',
            'palavra.string'   => 'A palavra deve ser um texto.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'erro' => 'Requisição inválida.',
                'detalhes' => $validator->errors()->all(),
            ], 400); // 400 = erro do cliente (dados faltando)
        }

        $idJogo  = $request->input('idJogo');
        $palavra = $request->input('palavra');

        // Carrega a partida salva no servidor (arquivo JSON)
        $jogo = $this->termooService->buscarJogo($idJogo);
        if (! $jogo) {
            return response()->json([
                'erro' => 'Jogo não encontrado.',
            ], 404); // 404 = idJogo não existe
        }

        $tamanho = $jogo['tamanhoPalavra'];
        // Minúsculas e sem acento para comparar (ex: "ÇÃO" vira "cao")
        $palavraNormalizada = $this->termooService->normalizarPalavra($palavra);

        if (mb_strlen($palavraNormalizada) !== $tamanho) {
            return response()->json([
                'erro'          => "A palavra deve ter {$tamanho} letras.",
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

        // Palavra precisa existir no dicionário (regra do Termoo)
        if (! $this->termooService->palavraExiste($palavraNormalizada)) {
            // Enunciado pede 200 com palavraValida: false (não gasta tentativa)
            return response()->json([
                'resultado'           => [],
                'venceu'              => false,
                'tentativasRestantes' => $jogo['tentativasRestantes'],
                'palavraValida'       => false,
            ], 200);
        }

        // Tudo certo: processa o chute e pinta as letras
        $resultado = $this->termooService->validarTentativa($idJogo, $palavraNormalizada);

        return response()->json($resultado, 200);
    }

    /**
     * POST /jogos/{idJogo}/tentativas
     *
     * Mesma lógica do validar-tentativa, mas no formato que o site do professor usa:
     * - idJogo vem na URL, não no body
     * - palavra inválida no dicionário retorna 400 (não 200)
     */
    public function validarTentativaProfessor(Request $request, string $idJogo): JsonResponse
    {
        $request->merge(['idJogo' => $idJogo]);

        $validator = Validator::make($request->all(), [
            'palavra' => 'required|string',
        ], [
            'palavra.required' => 'Informe a palavra.',
            'palavra.string'   => 'A palavra deve ser um texto.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'erro' => 'Requisição inválida.',
                'detalhes' => $validator->errors()->all(),
            ], 400);
        }

        $jogo = $this->termooService->buscarJogo($idJogo);
        if (! $jogo) {
            return response()->json(['erro' => 'Jogo não encontrado.'], 404);
        }

        if ($jogo['venceu'] || $jogo['tentativasRestantes'] <= 0) {
            return response()->json(['erro' => 'Este jogo já foi finalizado.'], 409);
        }

        $palavra = $this->termooService->normalizarPalavra($request->input('palavra'));

        if (mb_strlen($palavra) !== $jogo['tamanhoPalavra']) {
            return response()->json(['erro' => 'A palavra deve ter 5 letras.'], 400);
        }

        if (! $this->termooService->palavraExiste($palavra)) {
            return response()->json(['erro' => 'Palavra não encontrada no dicionário.'], 400);
        }

        $resultado = $this->termooService->validarTentativa($idJogo, $palavra);

        return response()->json($resultado, 200);
    }
}
