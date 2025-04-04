<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;
    public ?string $support_phone = null;
    public ?string $support_email = null;
    public ?string $mail_from_name = null;
    public ?string $mail_from_address = null;

    public ?string $logo = null;
    public ?string $logo_alt = null;
    public ?string $favicon = null;



    public static function group(): string
    {
        return 'general';
    }
}
