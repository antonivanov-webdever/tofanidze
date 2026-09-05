<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Read-through access to site settings: database first, config/site.php as the fallback.
 */
class Site
{
    protected ?array $overrides = null;

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->overrides()[$key] ?? null;

        if (filled($value) || is_bool($value)) {
            return $value;
        }

        return config("site.{$key}", $default);
    }

    public function all(): array
    {
        return array_merge(config('site'), array_filter(
            $this->overrides(),
            fn ($value) => filled($value) || is_bool($value)
        ));
    }

    public function name(): string
    {
        return (string) $this->get('name');
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name())) ?: [];

        return strtoupper(implode('', array_map(
            fn ($part) => mb_substr($part, 0, 1),
            array_slice($parts, 0, 2)
        )));
    }

    public function contactRecipient(): string
    {
        return (string) ($this->get('contact_recipient') ?: $this->get('email'));
    }

    public function socialLinks(): array
    {
        return array_filter([
            'github' => $this->get('github'),
            'linkedin' => $this->get('linkedin'),
            'telegram' => $this->get('telegram'),
        ]);
    }

    public function forget(): void
    {
        $this->overrides = null;
        Setting::flush();
    }

    protected function overrides(): array
    {
        if ($this->overrides !== null) {
            return $this->overrides;
        }

        try {
            $this->overrides = Schema::hasTable('settings') ? Setting::map() : [];
        } catch (Throwable) {
            // Database not reachable yet (install, migrations, artisan on a cold boot).
            $this->overrides = [];
        }

        return $this->overrides;
    }
}
