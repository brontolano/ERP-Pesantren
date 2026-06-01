<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class SpmbRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalisasi payload sebelum validasi:
     * - guardian_phone -> parent_phone (jika kosong)
     * - nomor HP -> format 62 (siap kirim WAHA)
     * - gender "Laki-Laki"/"Perempuan" -> L/P
     * - academic_year default tahun ajaran berjalan
     * - participant_phone disisipkan ke notes agar tidak hilang
     */
    protected function prepareForValidation(): void
    {
        $parentPhone = $this->input('parent_phone') ?: $this->input('guardian_phone') ?: $this->input('participant_phone');
        // parent_name kolom NOT NULL -> fallback ke nama santri bila kosong
        $parentName = $this->input('parent_name')
            ?: $this->input('guardian_name')
            ?: $this->input('student_name');

        $year = (int) date('Y');
        $defaultAcademicYear = $year . '/' . ($year + 1);

        $notes = (string) ($this->input('notes') ?? '');
        $participant = $this->normalizePhone($this->input('participant_phone'));
        if ($participant && ! Str::contains($notes, 'wa_peserta')) {
            $notes = trim($notes . ' | wa_peserta:' . $participant, ' |');
        }

        $this->merge([
            'parent_phone'  => $this->normalizePhone($parentPhone),
            'parent_name'   => $parentName,
            'gender'        => $this->normalizeGender($this->input('gender')),
            'academic_year' => $this->input('academic_year') ?: $defaultAcademicYear,
            'origin_school' => $this->input('origin_school') ?: $this->input('previous_school'),
            'source'        => in_array($this->input('source'), ['website', 'manual', 'referral'], true)
                ? $this->input('source')
                : 'website',
            'notes'         => $notes ?: null,
            'ayah_alamat'   => $this->input('ayah_alamat') ?: $this->input('alamat_ayah_jalan'),
            'ibu_alamat'    => $this->input('ibu_alamat') ?: $this->input('alamat_ibu_jalan'),
            'ayah_province_code' => $this->input('ayah_province_code') ?: $this->input('alamat_ayah_province_code'),
            'ayah_city_code' => $this->input('ayah_city_code') ?: $this->input('alamat_ayah_city_code'),
            'ayah_district_code' => $this->input('ayah_district_code') ?: $this->input('alamat_ayah_district_code'),
            'ayah_village_code' => $this->input('ayah_village_code') ?: $this->input('alamat_ayah_village_code'),
            'ayah_kode_pos' => $this->input('ayah_kode_pos') ?: $this->input('alamat_ayah_kode_pos'),
            'ibu_province_code' => $this->input('ibu_province_code') ?: $this->input('alamat_ibu_province_code'),
            'ibu_city_code' => $this->input('ibu_city_code') ?: $this->input('alamat_ibu_city_code'),
            'ibu_district_code' => $this->input('ibu_district_code') ?: $this->input('alamat_ibu_district_code'),
            'ibu_village_code' => $this->input('ibu_village_code') ?: $this->input('alamat_ibu_village_code'),
            'ibu_kode_pos' => $this->input('ibu_kode_pos') ?: $this->input('alamat_ibu_kode_pos'),
        ]);
    }

    public function rules(): array
    {
        return [
            'student_name'  => ['required', 'string', 'max:255'],
            'nik'           => ['nullable', 'string', 'max:20'],
            'nisn'          => ['nullable', 'string', 'max:20'],
            'gender'        => ['required', 'in:L,P'],
            'birth_date'    => ['nullable', 'date'],
            'birth_place'   => ['nullable', 'string', 'max:100'],
            'address'       => ['nullable', 'string'],
            'village_code'  => ['nullable', 'string', 'size:13', 'exists:master_villages,code'],
            'origin_school' => ['nullable', 'string', 'max:255'],
            'parent_name'   => ['nullable', 'string', 'max:255'],
            'parent_phone'  => ['required', 'string', 'min:10', 'max:20', 'regex:/^628[1-9][0-9]{6,11}$/'],
            'parent_email'  => ['nullable', 'email', 'max:255'],
            'academic_year' => ['nullable', 'string', 'max:9'],
            'source'        => ['nullable', 'string', 'in:website,manual,referral'],
            'notes'         => ['nullable', 'string'],
            'ayah_alamat'   => ['nullable', 'string'],
            'ayah_province_code' => ['nullable', 'string', 'size:2', 'exists:master_provinces,code'],
            'ayah_city_code' => ['nullable', 'string', 'size:5', 'exists:master_cities,code'],
            'ayah_district_code' => ['nullable', 'string', 'size:8', 'exists:master_districts,code'],
            'ayah_village_code' => ['nullable', 'string', 'size:13', 'exists:master_villages,code'],
            'ayah_kode_pos' => ['nullable', 'string', 'max:10'],
            'ibu_alamat'    => ['nullable', 'string'],
            'ibu_province_code' => ['nullable', 'string', 'size:2', 'exists:master_provinces,code'],
            'ibu_city_code' => ['nullable', 'string', 'size:5', 'exists:master_cities,code'],
            'ibu_district_code' => ['nullable', 'string', 'size:8', 'exists:master_districts,code'],
            'ibu_village_code' => ['nullable', 'string', 'size:13', 'exists:master_villages,code'],
            'ibu_kode_pos' => ['nullable', 'string', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'parent_phone.required' => 'Nomor WhatsApp wali wajib diisi.',
            'parent_phone.regex'    => 'Nomor WhatsApp wali tidak valid. Gunakan format 08xxxxxxxxxx.',
            'gender.required'       => 'Jenis kelamin wajib dipilih.',
        ];
    }

    /**
     * Ubah nomor HP Indonesia ke format 62xxxxxxxxxx (tanpa + / spasi).
     */
    protected function normalizePhone(?string $value): ?string
    {
        $raw = preg_replace('/[^\d+]/', '', (string) $value);
        if ($raw === '') {
            return null;
        }
        $raw = ltrim($raw, '+');
        $raw = preg_replace('/^0+(?=62)/', '', $raw);

        if (Str::startsWith($raw, '62')) {
            return $raw;
        }
        if (Str::startsWith($raw, '0')) {
            return '62' . substr($raw, 1);
        }
        if (Str::startsWith($raw, '8')) {
            return '62' . $raw;
        }
        return $raw;
    }

    protected function normalizeGender(?string $value): ?string
    {
        $v = strtoupper(trim((string) $value));
        if ($v === '') {
            return null;
        }
        if (Str::startsWith($v, 'P')) {  // Perempuan / P
            return 'P';
        }
        if (Str::startsWith($v, 'L') || Str::startsWith($v, 'M')) { // Laki-laki / L / Male
            return 'L';
        }
        return in_array($v, ['L', 'P'], true) ? $v : null;
    }
}
