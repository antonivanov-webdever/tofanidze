<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            TechnologySeeder::class,
            SkillSeeder::class,
            ServiceSeeder::class,
            ExperienceSeeder::class,
            ProjectSeeder::class,
            PostSeeder::class,
        ]);

        $this->createAdmin();
    }

    protected function createAdmin(): void
    {
        $email = env('ADMIN_EMAIL', config('site.email'));

        if (User::where('email', $email)->exists()) {
            $this->command?->info("Admin user already exists: {$email}");

            return;
        }

        $password = env('ADMIN_PASSWORD') ?: Str::password(16, symbols: false);

        User::create([
            'name' => config('site.name'),
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $this->command?->newLine();
        $this->command?->info('Admin account created:');
        $this->command?->line("  email:    {$email}");
        $this->command?->line("  password: {$password}");
        $this->command?->comment('  Store it now — the password is not shown again.');
        $this->command?->newLine();
    }
}
