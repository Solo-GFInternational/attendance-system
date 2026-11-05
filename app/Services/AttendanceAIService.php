<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class AttendanceAIService
{
    protected Client $client;

    protected string $ollamaUrl = 'http://127.0.0.1:11434';

    protected string $model = 'qwen2.5:3b';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->ollamaUrl,
            'timeout' => 120,
        ]);
    }

    public function ask(string $question): string
    {
        try {
            $response = $this->client->post('/api/generate', [
                'json' => [
                    'model' => $this->model,
                    'prompt' => $question,
                    'stream' => false,
                ],
            ]);

            $data = json_decode(
                $response->getBody()->getContents(),
                true
            );

            return $data['response'] ?? 'The AI did not return a response.';
        } catch (GuzzleException $e) {
            return 'Unable to connect to the local AI service.';
        }
    }
}