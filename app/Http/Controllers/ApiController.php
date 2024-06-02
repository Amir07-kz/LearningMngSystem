<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_YouTube;
use Illuminate\Http\Request;
use GuzzleHttp;
use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;
use Illuminate\Support\Facades\Log;

class ApiController
{
    private $gemeniClient;
    public function __construct()
    {
        $this->gemeniClient = new Client(env('API_KEY'));
    }

    public function sendRequest(Request $request)
    {
        $inputText = $request->input('text');

        try {
            $response = $this->gemeniClient->geminiPro()->generateContent(
                new TextPart($inputText)
            );

            return response()->json([
                'text' => $response->text()
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sendYouTube(Request $request)
    {
        $client = new Google_Client();
        $client->setDeveloperKey(env('API_KEY_YOU_TUBE'));

        $youtube = new Google_Service_YouTube($client);
        $query = $request->input('searchQuery');

        try {
            $response = $youtube->search->listSearch('id,snippet', [
                'q' => $query,
                'maxResults' => 1,
                'type' => 'video',
            ]);

            $videos = [];
            foreach ($response->getItems() as $item) {
                $videos[] = [
                    'title' => $item->getSnippet()->getTitle(),
                    'videoId' => $item->getId()->getVideoId(),
                    'videoUrl' => "https://www.youtube.com/watch?v=" . $item->getId()->getVideoId()
                ];
            }

            if (empty($videos)) {
                return response()->json(['error' => '����� �� �������'], 404);
            }

            return response()->json(['videos' => $videos]);
        } catch (\Exception $e) {
            return response()->json(['error' => '��������� ������: ' . $e->getMessage()], 500);
        }
    }

    public function generateTests(Request $request)
    {
        Log::info('Получены данные для генерации тестов:', $request->all());
        $questions = $request->input('questions');

        try {
            $generatedTests = [];

            foreach ($questions as $questionData) {
                $questionText = $questionData['question'];
                $userAnswers = implode(', ', $questionData['userAnswers']);

                Log::info('Обработка вопроса:', ['question' => $questionText, 'userAnswers' => $userAnswers]);

                // Формируем запрос для Gemini API
                $prompt = "В вопросе \"$questionText\".\n
                Пользователь ответил: \"$userAnswers\".\n
                На основании ошибки пользователя сгенерируй тестовое задание с измененным вопросом и вариантами ответа для работы над ошибками.\n
                Сгенерированный вопрос заключи в #? и в конце вопроса #?\n
                Каждый вариант ответа заключи в #$ и в конце ответа #$\n
                Выдели правильный ответ #+ и в конце #+\n";

                $textPart = new TextPart($prompt);

                Log::info('Сформированный запрос:', ['prompt' => $prompt]);

                $response = $this->gemeniClient->geminiPro()->generateContent($textPart);

                Log::info('Ответ от API:', ['response' => $response]);

                if (isset($response->error)) {
                    Log::error('Ошибка в ответе API:', ['error' => $response->error]);
                    return response()->json(['error' => $response->error], 500);
                }

                $generatedTests[] = json_decode(json_encode($response->text()));
            }

            Log::info('Сгенерированные тесты:', ['generated_tests' => $generatedTests]);

            return response()->json([
                'generated_tests' => $generatedTests
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при генерации тестов:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public static function generateStatistics(array $questionsAndAnswers)
    {
        $client = new Client(env('API_KEY'));

        try {
            $prompt = "Это ответы пользователя, на тестовые задания \"" . json_encode($questionsAndAnswers) . "\".\n
                Оцени компетенции сотрудника по данным из теста, просто порекомендуй материалы, дополнительные источники\n";

            $textPart = new TextPart($prompt);

            $response = $client->geminiPro()->generateContent($textPart);

            if (isset($response->error)) {
                Log::error('Ошибка в ответе API:', ['error' => $response->error]);
                return response()->json(['error' => $response->error], 500);
            }

            $generatedStatistics = json_decode(json_encode($response->text()));

            return response()->json([
                'generated_statistics' => $generatedStatistics
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при генерации статистики:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}