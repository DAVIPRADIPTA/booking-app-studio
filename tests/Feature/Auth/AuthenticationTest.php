<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Volt as LivewireVolt;
use Livewire\Volt\Volt;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    // Pastikan user sesuai kebijakan akses
    $user = User::firstOrCreate(
        ['email' => 'peace@gmail.com'],
        [
            'name' => 'Peace User',
            'password' => Hash::make('12345678'),
        ]
    );

    Volt::test('auth.login')
        ->set('email', 'peace@gmail.com')
        ->set('password', '12345678')
        ->call('login')
        ->assertRedirect('/dashboard'); // atau route() yg kamu set
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = LivewireVolt::test('auth.login')
        ->set('email', $user->email)
        ->set('password', 'wrong-password')
        ->call('login');

    $response->assertHasErrors('email');

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $response->assertRedirect('/');

    $this->assertGuest();
});