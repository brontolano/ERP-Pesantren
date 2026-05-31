# ERP Pesantren Asy-Syifaa — Architecture

## Tech Stack
- **Backend:** Laravel 13 + PHP 8.3
- **Admin Panel:** Filament 5.6
- **RBAC:** Spatie Laravel Permission 6.25
- **API Auth:** Laravel Sanctum
- **Database:** PostgreSQL
- **Notification:** N8N + WAHA (WhatsApp) + Laravel Database Notifications

## System Architecture

```
asy-syifaa.com (Website)
    |
    | POST /api/v1/spmb/register
    v
erp.asy-syifaa.com (ERP - Laravel/Filament)
    |                           |
    | Event dispatch            | HTTP POST webhook
    v                           v
Laravel Event System       N8N + WAHA (VPS)
    |                           |
    v                           v
Database Notification      WhatsApp Notification
(in-app bell icon)         (ke nomor HP calon santri)
```

## Database Schema

### Auth
- `erp_accounts` — user accounts (guard: erp, 18 roles via Spatie)

### SPMB
- `ppdb_registrations` — data pendaftaran (linked to erp_account)
- `ppdb_documents` — dokumen upload (8 tipe wajib, versioned, approve/reject workflow)
- `ppdb_selection_stages` — tahap seleksi (admin_check, interview, quran_test, health_check)

### Keuangan
- `billing_types` — jenis biaya (SPP, Daftar, Gedung, dll)
- `billing_periods` — periode akademik
- `invoices` — tagihan (auto-recalculate)
- `invoice_items` — item tagihan
- `payments` — pembayaran
- `payment_proofs` — bukti transfer upload (pending/approved/rejected)

### System
- `webhook_logs` — log semua webhook ke N8N
- `notifications` — Laravel database notifications (in-app)

### Exam (Wave 2 - schema only)
- `exam_schedules`, `exam_questions`, `exam_attempts`, `exam_answers`

## Event Architecture

```
SpmbRegistered ──→ SendRegistrationWebhook (WA credential)
DocumentVerified ──→ UpdateDocumentStatus + SendDocumentRejectedNotification
AllDocumentsVerified ──→ SendAllDocsVerifiedNotification
SelectionDecided ──→ HandleSelectionDecision (generate invoice jika lulus)
DaftarUlangPaid ──→ ConvertPendaftarToSantri (role Pendaftar → Santri)
PaymentProofApproved ──→ CreatePayment + SendPaymentConfirmation
```

## Service Layer
- `SpmbService` — orchestrator SPMB (register, verify documents, selection, convert)
- `WebhookNotificationService` — HTTP client ke N8N webhook

## API Endpoints
- `POST /api/v1/spmb/register` — registrasi dari website
- `GET /api/v1/spmb/{regNumber}/status` — cek status
- `GET /api/v1/spmb/{regNumber}/documents` — cek dokumen

## Akun Terpusat & Lifecycle SPMB -> Wali App
- **Single Source of Truth:** seluruh identitas akun berada di `erp_accounts` (DB ERP terpusat).
- **Saat daftar SPMB:**
  - sistem membuat/menautkan `erp_account`,
  - role awal `Pendaftar`,
  - pendaftaran disimpan di `ppdb_registrations` dengan relasi ke akun.
- **Saat lulus + daftar ulang lunas:**
  - event `DaftarUlangPaid` memicu `convertToSantri`,
  - calon santri dikonversi ke `students.status=aktif`,
  - status pendaftaran menjadi `enrolled`,
  - role akun diganti dari `Pendaftar` ke `Wali Santri`.
- **Akses aplikasi wali:**
  - login API tetap memakai akun yang sama dari `erp_accounts`,
  - bila role termasuk `Wali Santri`, redirect diarahkan ke `https://app.asy-syifaa.com`.
- **Guard operasional (sudah diterapkan):**
  - idempotensi konversi pendaftar -> santri (hindari student ganda saat replay event),
  - anti-duplikasi invoice daftar ulang per `ppdb_registration_id`,
  - standarisasi status seleksi publik: `lulus`, `cadangan`, `rejected`, `enrolled`.

## Role-Based Access

| Role | Akses |
|---|---|
| Superadmin / Admin | Semua modul |
| Staf SPMB | PendaftarResource, DokumenVerifikasi, Broadcast, Seleksi |
| Bendahara | Keuangan (Invoice, Payment, BillingType) |
| Pendaftar | ProfilSaya, DokumenSaya, TagihanSaya, Notifikasi |
| Santri | (Wave 2 — Kesantrian, Akademik) |

## Webhook Integration (N8N)
- URL: `SPMB_WEBHOOK_URL` (env)
- Flow: `Asy-Syifaa PPDB to WAHA (Live).json`
- Events: spmb.registered, spmb.document.rejected, spmb.documents.complete, spmb.selection.decided, spmb.payment.confirmed
