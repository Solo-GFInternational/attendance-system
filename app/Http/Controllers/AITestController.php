<?php

namespace App\Http\Controllers;

use App\Services\AttendanceAIService;
use Illuminate\Http\JsonResponse;

class AITestController extends Controller
{
    public function test(AttendanceAIService $ai): JsonResponse
    {
        $answer = $ai->ask(
            'Explain in one short sentence what an employee attendance system does.'
        );

        return response()->json([
            'success' => true,
            'answer' => $answer,
        ]);
    }
}