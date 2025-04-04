<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'Ordre National des Pharmaciens de Guinée');
        $this->migrator->add('general.support_phone', null);
        $this->migrator->add('general.support_email', null);
        $this->migrator->add('general.mail_from_name', 'nepasrepondre@onpg.com');
        $this->migrator->add('general.mail_from_address', 'Inscription - ONPG');
        $this->migrator->add('general.logo', null);
        $this->migrator->add('general.logo_alt', null);
        $this->migrator->add('general.favicon', null);
    }
};
