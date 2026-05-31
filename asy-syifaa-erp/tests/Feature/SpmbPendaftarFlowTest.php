<?php

namespace Tests\Feature;

use App\Models\PpdbRegistration;
use Tests\TestCase;

class SpmbPendaftarFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite extension is not available in this environment.');
        }
    }

    public function test_spmb_register_normalizes_phone_and_creates_pending_registration(): void
    {
        $payload = [
            'student_name' => 'Ahmad Santri',
            'gender' => 'Laki-Laki',
            'parent_name' => 'Bapak Ahmad',
            'parent_phone' => '081234567890',
            'academic_year' => '2026/2027',
            'source' => 'website',
        ];

        $response = $this->postJson('/api/v1/spmb/register', $payload);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('ppdb_registrations', [
            'student_name' => 'Ahmad Santri',
            'status' => 'pending',
            'document_status' => 'incomplete',
            'parent_phone' => '6281234567890',
        ]);
    }

    public function test_spmb_documents_endpoint_returns_mandatory_checklist(): void
    {
        $registration = PpdbRegistration::create([
            'student_name' => 'Zaid',
            'gender' => 'L',
            'parent_name' => 'Wali Zaid',
            'parent_phone' => '6281234567000',
            'academic_year' => '2026/2027',
            'status' => 'pending',
            'document_status' => 'incomplete',
            'source' => 'website',
        ]);

        $response = $this->getJson('/api/v1/spmb/' . $registration->registration_number . '/documents');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(count(config('spmb.mandatory_documents')), 'data');
    }

    public function test_public_selection_results_uses_standardized_statuses(): void
    {
        PpdbRegistration::create([
            'student_name' => 'Lulus Satu',
            'gender' => 'L',
            'parent_name' => 'Wali Lulus',
            'parent_phone' => '6281234567111',
            'academic_year' => '2026/2027',
            'status' => 'lulus',
            'document_status' => 'complete',
            'source' => 'website',
        ]);

        PpdbRegistration::create([
            'student_name' => 'Cadangan Dua',
            'gender' => 'P',
            'parent_name' => 'Wali Cadangan',
            'parent_phone' => '6281234567222',
            'academic_year' => '2026/2027',
            'status' => 'cadangan',
            'document_status' => 'complete',
            'source' => 'website',
        ]);

        $response = $this->getJson('/api/v1/ppdb/selection-results');

        $response->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('meta.total', 2);
    }
}
