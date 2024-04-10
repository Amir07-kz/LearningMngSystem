<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp;
use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

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
}