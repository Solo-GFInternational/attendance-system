<?php

namespace App\Http\Controllers;

use App\Services\AttendanceAIQueryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceAIController extends Controller
{
    public function ask(
        Request $request,
        AttendanceAIQueryService $ai
    ): JsonResponse {
        $validated = $request->validate([
            'question' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        $answer = $ai->ask(
            trim($validated['question'])
        );

        return response()->json([
            'success' => true,
            'answer' => $answer,
        ]);
    }
}