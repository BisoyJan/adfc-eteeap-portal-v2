<?php

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\User;

test('vpaa can view reports index', function () {
    $vpaa = User::factory()->withRole('vpaa')->create();

    $response = $this->actingAs($vpaa)->get(route('vpaa.reports.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('vpaa/reports/index'));
});

test('vpaa can download a PDF report', function () {
    $vpaa = User::factory()->withRole('vpaa')->create();

    $response = $this->actingAs($vpaa)->get(route('vpaa.reports.show', 'pipeline'));

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('application/pdf');
});

test('vpaa can download an Excel report', function () {
    $vpaa = User::factory()->withRole('vpaa')->create();

    $response = $this->actingAs($vpaa)->get(
        route('vpaa.reports.show', ['type' => 'pipeline', 'format' => 'excel'])
    );

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('vnd.openxmlformats');
});

test('vpaa gets 404 for disallowed report type', function () {
    $vpaa = User::factory()->withRole('vpaa')->create();

    $response = $this->actingAs($vpaa)->get(route('vpaa.reports.show', 'adviser_load'));

    $response->assertNotFound();
});

test('non-vpaa cannot access vpaa reports', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('vpaa.reports.index'));

    $response->assertForbidden();
});
