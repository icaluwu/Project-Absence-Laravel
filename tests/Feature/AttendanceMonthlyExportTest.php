<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function actingAsAdmin() {
    // Ensure roles exist
    foreach (['Admin','HR','Karyawan'] as $r) {
        Role::findOrCreate($r);
    }
    $admin = User::factory()->create([
        'email' => 'admin-test@example.com',
        'password' => bcrypt('password'),
    ]);
    $admin->assignRole('Admin');
    return $admin;
}

it('exports monthly XLSX successfully', function () {
    $admin = actingAsAdmin();
    $this->actingAs($admin);

    $resp = $this->get(route('attendance.export.monthly', ['month' => '2025-08', 'format' => 'xlsx']));

    $resp->assertOk();
    $resp->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    $content = $resp->streamedContent();
    expect(substr($content, 0, 2))->toBe('PK'); // XLSX (zip) signature
});

it('exports monthly CSV successfully', function () {
    $admin = actingAsAdmin();
    $this->actingAs($admin);

    $resp = $this->get(route('attendance.export.monthly', ['month' => '2025-08', 'format' => 'csv']));

    $resp->assertOk();
    expect(strtolower($resp->headers->get('content-type')))->toContain('text/csv');

    $content = $resp->streamedContent();
    // First header cell should be "No"
    expect(str_starts_with($content, 'No,'))->toBeTrue();
});

it('exports monthly PDF successfully', function () {
    $admin = actingAsAdmin();
    $this->actingAs($admin);

    $resp = $this->get(route('attendance.export.monthly', ['month' => '2025-08', 'format' => 'pdf']));

    $resp->assertOk();
    expect(strtolower($resp->headers->get('content-type')))->toContain('application/pdf');

    $content = $resp->getContent();
    // Many PDFs begin with %PDF, but DomPDF may prepend binary header; so accept either
    expect(substr($content, 0, 4) === '%PDF' || str_contains($content, '%PDF'))
        ->toBeTrue();
});

