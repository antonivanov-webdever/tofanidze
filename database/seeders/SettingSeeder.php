<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Seed the editable settings from config/site.php defaults.
     * Existing values are never overwritten — this is safe to re-run.
     */
    public function run(): void
    {
        $groups = [
            'profile' => ['name', 'headline', 'tagline', 'role', 'intro', 'summary', 'years_experience', 'projects_delivered'],
            'contact' => ['email', 'phone', 'location', 'timezone_label', 'contact_recipient', 'available_for_work', 'availability_note'],
            'social' => ['github', 'linkedin', 'telegram'],
            'seo' => ['meta_title', 'meta_description'],
        ];

        foreach ($groups as $group => $keys) {
            foreach ($keys as $key) {
                if (Setting::where('key', $key)->exists()) {
                    continue;
                }

                $value = config("site.{$key}");

                Setting::put($key, $value ?? '', match (true) {
                    is_bool($value) => 'boolean',
                    is_int($value) => 'integer',
                    default => 'string',
                }, $group);
            }
        }

        Setting::flush();
    }
}
