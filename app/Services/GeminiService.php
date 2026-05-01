<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class GeminiService
{
    private string $apiKey;

    private const MODEL = 'gemini-2.5-flash';

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? config('services.gemini.key') ?? env('GEMINI_API_KEY') ?? '';
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function analyzeResume(string $fileContent, string $mimeType, ?string $targetCategory = null): array
    {
        if (empty($this->apiKey)) {
            throw new Exception('A API key do Gemini não está configurada (GEMINI_API_KEY).');
        }

        $base64Content = base64_encode($fileContent);

        $prompt = "Você é um recrutador de RH especialista em análise de currículos.\n";
        $prompt .= "Extraia e analise as informações do currículo fornecido no formato JSON estrito a seguir:\n";
        $prompt .= "{\n";
        $prompt .= "    \"candidate_name\": \"Nome completo do candidato\",\n";
        $prompt .= "    \"candidate_email\": \"Melhor email para contato\",\n";
        $prompt .= "    \"candidate_phone\": \"Telefone com DDD\",\n";
        $prompt .= "    \"technical_score\": 0 a 100 indicando a aderência técnica,\n";
        $prompt .= "    \"match_score\": 0 a 100 indicando o fit para a vaga,\n";
        $prompt .= "    \"summary\": \"Um resumo de no máximo 3 frases sobre o perfil\",\n";
        $prompt .= "    \"skills\": [\"habilidade 1\", \"habilidade 2\"]\n";
        $prompt .= "}\n\n";

        if (! empty($targetCategory)) {
            $prompt .= "Avalie as pontuações e o perfil considerando ESPECIFICAMENTE a seguinte vaga: \"{$targetCategory}\".\n\n";
        }

        $prompt .= 'ATENÇÃO: Retorne APENAS o JSON válido. Não inclua Markdown (como ```json) ou qualquer outro texto antes ou depois do JSON.';

        $response = Http::withoutVerifying()
            ->timeout(60)
            ->post('https://generativelanguage.googleapis.com/v1beta/models/'.self::MODEL.':generateContent?key='.$this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'inlineData' => [
                                    'mimeType' => $mimeType,
                                    'data' => $base64Content,
                                ],
                            ],
                            [
                                'text' => $prompt,
                            ],
                        ],
                    ],
                ],
            ]);

        if (! $response->successful()) {
            throw new Exception('Erro de comunicação com a API do Gemini: '.$response->body());
        }

        $result = $response->json();
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

        $text = str_replace(['```json', '```'], '', trim($text));

        $data = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! $data) {
            throw new Exception('Não foi possível fazer o parse da resposta do Gemini: '.json_last_error_msg());
        }

        $technicalScore = (int) ($data['technical_score'] ?? 0);
        $matchScore = (int) ($data['match_score'] ?? 0);

        return [
            'candidate_name' => $data['candidate_name'] ?? 'Desconhecido',
            'candidate_email' => $data['candidate_email'] ?? null,
            'candidate_phone' => $data['candidate_phone'] ?? null,
            'technical_score' => $technicalScore,
            'match_score' => $matchScore,
            'score' => (int) round(($technicalScore + $matchScore) / 2),
            'summary' => $data['summary'] ?? null,
            'skills' => $data['skills'] ?? [],
            'category' => $targetCategory,
        ];
    }
}
