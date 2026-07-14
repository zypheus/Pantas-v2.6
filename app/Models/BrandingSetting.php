<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class BrandingSetting extends Model
{
    protected $fillable = [
        'banner_path',
        'opac_banner_path',
        'opac_logo_path',
        'opac_default_book_cover_path',
        'sidebar_logo_path',
        'login_modal_logo_path',
        'sidebar_brand_name',
        'sidebar_brand_subtitle',
        'login_modal_welcome_label',
        'login_modal_portal_name',
        'login_modal_description',
        'login_modal_sign_in_heading',
        'login_modal_email_placeholder',
        'login_modal_password_placeholder',
        'sidebar_brand_text_color',
        'primary_color',
        'secondary_color',
        'accent_color',
        'sidebar_background_color',
        'sidebar_text_color',
        'sidebar_active_color',
        'sidebar_hover_background_color',
        'sidebar_hover_text_color',
        'button_color',
        'sidebar_footer_background_color',
        'table_header_color',
        'table_header_text_color',
        'table_border_color',
        'table_hover_color',
        'login_modal_left_background_color',
        'login_modal_background_color',
        'login_modal_text_color',
        'login_modal_button_color',
        'updated_by',
    ];

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
