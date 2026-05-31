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
