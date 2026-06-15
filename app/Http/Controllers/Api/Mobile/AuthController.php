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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->where('email', $credentials['email'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (in_array($user->role, ['admin', 'staff'], true)) {
            throw ValidationException::withMessages([
                'email' => ['This account is not allowed to use the mobile app.'],
            ]);
        }

        $user->loadMissing('student');

        $token = $user->createToken('pantas-mobile')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'data' => [
                'token' => $token,
                'user' => $this->formatUser($user),
                'student' => $this->formatStudent($user->student),
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing('student');

        return response()->json([
            'message' => 'Authenticated user retrieved.',
            'data' => [
                'user' => $this->formatUser($user),
                'student' => $this->formatStudent($user->student),
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

    private function formatUser(User $user): array
    {
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
