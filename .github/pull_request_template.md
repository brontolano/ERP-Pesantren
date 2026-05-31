## Ringkasan Perubahan
- [ ] Perubahan mengikuti arsitektur API-first (CRUD domain via `asy-syifaa-api`)
- [ ] Tidak ada direct CRUD domain ke DB dari `asy-syifaa-erp`, `asy-syifaa-website`, `asy-syifaa-app`

## Checklist Database & Integrasi
- [ ] Jika menyentuh schema, perubahan melalui Prisma migration
- [ ] Tidak ada query domain langsung ke PostgreSQL di modul non-API
- [ ] Endpoint API untuk data master terdokumentasi/terpakai
- [ ] Jika ada exception direct DB, sudah dicatat di `DATABASE-ARCHITECTURE.md` (Exception Registry)

## Checklist Deploy
- [ ] Perubahan memicu workflow modul yang tepat berdasarkan `paths`
- [ ] Healthcheck layanan lulus setelah deploy
- [ ] Tidak mengganggu modul lain yang tidak berubah
