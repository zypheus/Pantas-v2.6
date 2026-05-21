<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public const KEY_LOGOUT_FEEDBACK = 'logout_feedback_enabled';

    protected $fillable = ['key', 'value'];

    public static function logoutFeedbackEnabled(): bool
    {
        $value = static::where('key', self::KEY_LOGOUT_FEEDBACK)->value('value');

        if ($value === null) {
            return true;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }

    public static function setLogoutFeedbackEnabled(bool $enabled): void
    {
        static::updateOrCreate(
            ['key' => self::KEY_LOGOUT_FEEDBACK],
            ['value' => $enabled ? '1' : '0']
        );
    }
}
