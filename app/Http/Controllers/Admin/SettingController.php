<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Site;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * key => [label, type, group, hint]
     */
    public const FIELDS = [
        'name' => ['Full name', 'text', 'profile', null],
        'headline' => ['Headline', 'text', 'profile', 'Shown under your name in the header and hero.'],
        'tagline' => ['Tagline', 'text', 'profile', 'One line on your specialisation.'],
        'role' => ['Résumé role title', 'text', 'profile', 'Used at the top of the résumé and PDF.'],
        'intro' => ['Hero intro', 'textarea', 'profile', 'The paragraph under the hero heading.'],
        'summary' => ['Professional summary', 'textarea', 'profile', 'Used on the about page and in the PDF résumé.'],
        'years_experience' => ['Years of experience', 'number', 'profile', null],
        'projects_delivered' => ['Projects delivered', 'number', 'profile', null],

        'email' => ['Public email', 'email', 'contact', null],
        'contact_recipient' => ['Notification email', 'email', 'contact', 'Where contact form submissions are sent. Defaults to the public email.'],
        'phone' => ['Phone', 'text', 'contact', null],
        'location' => ['Location', 'text', 'contact', null],
        'timezone_label' => ['Time zone label', 'text', 'contact', 'Example: CET / UTC+1.'],
        'available_for_work' => ['Available for work', 'boolean', 'contact', 'Shows the green availability badge.'],
        'availability_note' => ['Availability note', 'text', 'contact', null],

        'github' => ['GitHub URL', 'url', 'social', null],
        'linkedin' => ['LinkedIn URL', 'url', 'social', null],
        'telegram' => ['Telegram URL', 'url', 'social', null],

        'meta_title' => ['Default meta title', 'text', 'seo', 'Used on the home page and as a fallback.'],
        'meta_description' => ['Default meta description', 'textarea', 'seo', 'Aim for 150–160 characters.'],
    ];

    public function edit(Site $site): View
    {
        return view('admin.settings', [
            'fields' => collect(self::FIELDS)->map(fn ($field, $key) => [
                'key' => $key,
                'label' => $field[0],
                'type' => $field[1],
                'group' => $field[2],
                'hint' => $field[3],
                'value' => $site->get($key),
            ])->groupBy('group'),
        ]);
    }

    public function update(Request $request, Site $site): RedirectResponse
    {
        $rules = [];

        foreach (self::FIELDS as $key => [$label, $type, $group, $hint]) {
            $rules["settings.{$key}"] = match ($type) {
                'email' => ['nullable', 'email', 'max:190'],
                'url' => ['nullable', 'url', 'max:255'],
                'number' => ['nullable', 'integer', 'min:0', 'max:99'],
                'boolean' => ['nullable', 'boolean'],
                'textarea' => ['nullable', 'string', 'max:2000'],
                default => ['nullable', 'string', 'max:255'],
            };
        }

        $validated = $request->validate($rules)['settings'] ?? [];

        foreach (self::FIELDS as $key => [$label, $type, $group, $hint]) {
            $value = $validated[$key] ?? null;

            Setting::put($key, match ($type) {
                'boolean' => $request->boolean("settings.{$key}"),
                'number' => (int) $value,
                default => (string) $value,
            }, match ($type) {
                'boolean' => 'boolean',
                'number' => 'integer',
                default => 'string',
            }, $group);
        }

        $site->forget();

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'Settings saved.');
    }
}
