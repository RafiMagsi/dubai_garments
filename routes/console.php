<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:create {--name=} {--email=} {--password=}', function () {
    $name = (string) ($this->option('name') ?: $this->ask('Name'));
    $email = (string) ($this->option('email') ?: $this->ask('Email'));
    $password = (string) ($this->option('password') ?: $this->secret('Password (min 8 chars)'));
    $passwordConfirmation = (string) ($this->option('password') ?: $this->secret('Confirm password'));

    $validator = Validator::make([
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'password_confirmation' => $passwordConfirmation,
    ], [
        'name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:160', 'unique:users,email'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }

        return self::FAILURE;
    }

    $user = User::create([
        'name' => $name,
        'email' => $email,
        'role' => 'admin',
        'password' => Hash::make($password),
    ]);

    $this->info('Admin user created successfully.');
    $this->line('ID: '.$user->id);
    $this->line('Email: '.$user->email);
    $this->line('Role: '.$user->role);
    $this->line('Login URL: '.url('/admin/login'));

    return self::SUCCESS;
})->purpose('Create an admin user from CLI (safe first-login setup)');
