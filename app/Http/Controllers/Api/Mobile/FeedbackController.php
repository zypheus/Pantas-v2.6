<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'comments' => ['required', 'string', 'max:5000'],
        ]);

        $user = $request->user();
        $user->loadMissing('student');

        $student = $user->student;
        $name = trim((string) $user->fname.' '.(string) $user->lname);

        if ($name === '' && $student) {
            $name = trim((string) $student->firstname.' '.(string) $student->lastname);
        }

        $feedback = Feedback::query()->create([
            'name' => $name !== '' ? $name : null,
            'email' => $user->email,
            'comments' => $validated['comments'],
        ]);

        return response()->json([
            'message' => 'Feedback submitted successfully.',
            'data' => [
                'id' => $feedback->id,
                'name' => $feedback->name,
                'email' => $feedback->email,
                'comments' => $feedback->comments,
                'created_at' => $feedback->created_at?->toDateTimeString(),
            ],
        ], 201);
    }
}
