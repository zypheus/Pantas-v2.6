<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'comments' => ['required', 'string', 'max:5000'],
        ]);

        [$name, $email] = $this->feedbackIdentity($request);

        $feedback = Feedback::query()->create([
            'name' => $name !== '' ? $name : null,
            'email' => $email,
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

    private function feedbackIdentity(Request $request): array
    {
        $tokenable = $request->user();

        if ($tokenable instanceof Student) {
            return [
                trim((string) $tokenable->firstname.' '.(string) $tokenable->lastname),
                null,
            ];
        }

        if ($tokenable instanceof User) {
            $tokenable->loadMissing('student');

            $student = $tokenable->student;
            $name = trim((string) $tokenable->fname.' '.(string) $tokenable->lname);

            if ($name === '' && $student) {
                $name = trim((string) $student->firstname.' '.(string) $student->lastname);
            }

            return [$name, $tokenable->email];
        }

        return ['', null];
    }
}
