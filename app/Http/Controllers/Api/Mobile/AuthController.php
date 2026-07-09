<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'string', 'max:255'],
        ]);

        $student = Student::query()
            ->where('id_number', trim($validated['student_id']))
            ->first();

        if (! $student) {
            throw ValidationException::withMessages([
                'student_id' => ['The provided Student ID was not found.'],
            ]);
        }

        $token = $student->createToken('pantas-mobile')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'data' => [
                'token' => $token,
                'user' => $this->formatUser($student),
                'student' => $this->formatStudent($student),
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);

        if ($student instanceof JsonResponse) {
            return $student;
        }

        return response()->json([
            'message' => 'Authenticated user retrieved.',
            'data' => [
                'user' => $this->formatUser($student),
                'student' => $this->formatStudent($student),
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logout successful.',
            'data' => null,
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        if ($request->user() instanceof Student) {
            return response()->json([
                'message' => 'Password changes are not supported for student ID mobile login.',
                'data' => null,
            ], 409);
        }

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Password changed successfully. Please log in again.',
            'data' => null,
        ]);
    }

    private function resolveStudent(Request $request): Student|JsonResponse
    {
        $tokenable = $request->user();

        if ($tokenable instanceof Student) {
            return $tokenable;
        }

        if ($tokenable instanceof User) {
            if (in_array($tokenable->role, ['admin', 'staff'], true)) {
                return response()->json([
                    'message' => 'This account is not allowed to use the mobile app.',
                    'data' => null,
                ], 403);
            }

            $tokenable->loadMissing('student');

            if ($tokenable->student) {
                return $tokenable->student;
            }
        }

        return response()->json([
            'message' => 'No student profile is linked to this account.',
            'data' => null,
        ], 409);
    }

    private function formatUser(User|Student $user): array
    {
        if ($user instanceof Student) {
            return [
                'id' => $user->id,
                'name' => trim((string) $user->firstname.' '.(string) $user->lastname),
                'fname' => $user->firstname,
                'lname' => $user->lastname,
                'email' => null,
                'role' => 'student',
            ];
        }

        return [
            'id' => $user->id,
            'name' => trim((string) $user->fname.' '.(string) $user->lname),
            'fname' => $user->fname,
            'lname' => $user->lname,
            'email' => $user->email,
            'role' => $user->role,
        ];
    }

    private function formatStudent(?Student $student): ?array
    {
        if (! $student) {
            return null;
        }

        return [
            'id' => $student->id,
            'id_number' => $student->id_number,
            'lastname' => $student->lastname,
            'firstname' => $student->firstname,
            'middle_initial' => $student->middle_initial,
            'course' => $student->course,
            'year' => $student->year,
            'mobile_number' => $student->mobile_number,
            'address' => $student->address,
        ];
    }
}
