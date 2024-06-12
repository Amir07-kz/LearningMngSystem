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
    public function sendRequest(Request $request)
    {
        $inputText = $request->input('text');
        $client = new Client(env('API_KEY'));

        try {
            $response = $client->geminiPro()->generateContent(
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
        dd($request);
        Log::info('�������� ������ ��� ��������� ������:', $request->all());
        $questions = $request->input('questions');

        $client = new Client(env('API_KEY'));

        try {
            $generatedTests = [];

            foreach ($questions as $questionData) {
                $questionText = $questionData['question'];
                $userAnswers = implode(', ', $questionData['userAnswers']);

                Log::info('��������� �������:', ['question' => $questionText, 'userAnswers' => $userAnswers]);

                $textPart = new TextPart("������ ����� �������� ������ �� ������ ���������� �������: $questionText. ������ ������������: $userAnswers");

                $response = $client->geminiPro()->generateContent($textPart);
                $generatedTests[] = $response->text();
            }

            Log::info('��������������� �����:', ['generated_tests' => $generatedTests]);

            return response()->json([
                'generated_tests' => $generatedTests
            ]);

        } catch (\Exception $e) {
            Log::error('������ ��� ��������� ������:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}