<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserPreferenceController extends Controller
{
    /**
     * Save the authenticated user's theme preference.
     *
     * Validates the theme key against the configured allowlist,
     * persists it to the database, and returns the saved value.
     */
    public function store(Request $request): JsonResponse
    {
        $themes = config('themes.themes', []);

        $validator = Validator::make($request->all(), [
            'theme' => ['required', 'string', 'max:64', function ($attribute, $value, $fail) use ($themes) {
                if (! array_key_exists($value, $themes)) {
                    $fail("The selected theme \"{$value}\" is not allowed.");
                }
            }],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $theme = $request->input('theme');
        $themesArray = config('themes.themes');

        /** @var \App\Models\User $user */
        $user = $request->user();
        $user->theme_preference = $theme;
        $user->save();

        return response()->json([
            'message' => 'Theme preference saved.',
            'theme' => $theme,
            'label' => $themesArray[$theme]['label'] ?? $theme,
        ]);
    }
}
