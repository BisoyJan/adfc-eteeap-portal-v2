<?php

use App\Models\User;

test('qa officer can view the enhancement plan overview', function () {
    $qaOfficer = User::factory()->withRole('qa_officer')->create();
    User::factory()->withRole('student')->create();

    $response = $this->actingAs($qaOfficer)->get(route('qa-officer.enhancement-plan.overview'));

    $response->assertOk();
});
