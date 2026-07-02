<?php

use App\Models\Notification;
use App\Models\User;

test('guest is redirected to login when accessing notifications', function () {
    $this->get(route('notifications.index'))->assertRedirect(route('login'));
    $this->put(route('notifications.mark-read', 1))->assertRedirect(route('login'));
    $this->put(route('notifications.mark-all-read'))->assertRedirect(route('login'));
});

test('user can view their paginated notifications', function () {
    $user = User::factory()->withRole('student')->create();

    Notification::create([
        'user_id' => $user->id,
        'type' => 'test',
        'title' => 'First Notification',
        'body' => 'Body 1',
        'channel' => 'in_app',
    ]);

    Notification::create([
        'user_id' => $user->id,
        'type' => 'test',
        'title' => 'Second Notification',
        'body' => 'Body 2',
        'channel' => 'in_app',
        'read_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('notifications.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('notifications/index')
        ->has('notifications.data', 2)
    );
});

test('user sees only their own notifications', function () {
    $user = User::factory()->withRole('student')->create();
    $other = User::factory()->withRole('student')->create();

    Notification::create([
        'user_id' => $user->id,
        'type' => 'test',
        'title' => 'Mine',
        'channel' => 'in_app',
    ]);

    Notification::create([
        'user_id' => $other->id,
        'type' => 'test',
        'title' => 'Theirs',
        'channel' => 'in_app',
    ]);

    $response = $this->actingAs($user)->get(route('notifications.index'));

    $response->assertInertia(fn ($page) => $page
        ->has('notifications.data', 1)
        ->where('notifications.data.0.title', 'Mine')
    );
});

test('user can mark a notification as read', function () {
    $user = User::factory()->withRole('student')->create();
    $notification = Notification::create([
        'user_id' => $user->id,
        'type' => 'test',
        'title' => 'Unread',
        'channel' => 'in_app',
    ]);

    $response = $this->actingAs($user)->put(route('notifications.mark-read', $notification));

    $response->assertRedirect();
    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('user cannot mark another users notification as read', function () {
    $user = User::factory()->withRole('student')->create();
    $other = User::factory()->withRole('student')->create();
    $notification = Notification::create([
        'user_id' => $other->id,
        'type' => 'test',
        'title' => 'Not mine',
        'channel' => 'in_app',
    ]);

    $response = $this->actingAs($user)->put(route('notifications.mark-read', $notification));

    $response->assertForbidden();
});

test('user can mark all notifications as read', function () {
    $user = User::factory()->withRole('student')->create();

    Notification::create([
        'user_id' => $user->id,
        'type' => 'test',
        'title' => 'A',
        'channel' => 'in_app',
    ]);

    Notification::create([
        'user_id' => $user->id,
        'type' => 'test',
        'title' => 'B',
        'channel' => 'in_app',
    ]);

    $response = $this->actingAs($user)->put(route('notifications.mark-all-read'));

    $response->assertRedirect();
    expect(Notification::where('user_id', $user->id)->whereNull('read_at')->count())->toBe(0);
});
