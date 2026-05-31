# Database Architecture & Standardization

**Status:** Foundation Document (v1.0)  
**Last Updated:** 2026-06-01  
**Applicable to:** All modules (ERP, API, Website, App)

## 1. Database Overview

### Centralized PostgreSQL Architecture
```
VPS (Hostinger: 187.77.116.167)
    ↓
PostgreSQL (port 32768)
    ├── asy_syifaa (main database)
    └── asy_syifaa_backup (periodic backups)
    
All modules → API Gateway (asy-syifaa-api) → PostgreSQL
                    ↓
              ┌─────┴────────────────────┐
              ↓          ↓         ↓      ↓
            ERP      Website     App   (n8n)
```

### Key Principles
1. **Single Source of Truth**: PostgreSQL is the only persistent data store
2. **Centralized API**: All CRUD operations go through `asy-syifaa-api` (Node.js + Express + Prisma)
3. **No Direct DB Access**: ERP (Laravel), Website (PHP), App (Vue) do NOT connect directly to PostgreSQL
4. **Event-Driven**: Changes propagate via API webhooks/events
5. **Versioned Migrations**: All schema changes tracked in Prisma migrations

---

## 2. Module Configuration

### 2.1 asy-syifaa-api (Data Master Backend)
**Framework**: Node.js + Express + Prisma ORM  
**Role**: Central API gateway for all CRUD operations

**Database Connection**:
```env
DATABASE_URL=postgresql://postgres:postgres@localhost:5432/asy_syifaa
PORT=4000
CORS_ORIGIN=https://erp.asy-syifaa.com,https://app.asy-syifaa.com
```

**Schema Management**:
- Location: `prisma/schema.prisma`
- Migrations: `prisma/migrations/`
- ORM: Prisma Client (type-safe queries)

**Key Tables** (from schema):
- `User` - Authentication & authorization
- `Santri` - Student master data
- `Kamar` - Room/dormitory management
- `Tagihan` - Billing/invoices
- `Pembayaran` - Payment transactions
- `Dokumen` - Document uploads
- `Wilayah` - Regional master (provinces, cities, districts)

**CRUD Endpoints**: `/api/v1/[resource]`
- `GET /api/v1/santri` - List all students
- `POST /api/v1/santri` - Create new student
- `PUT /api/v1/santri/{id}` - Update student
- `DELETE /api/v1/santri/{id}` - Delete student

---

### 2.2 asy-syifaa-erp (Laravel Admin Panel)
**Framework**: Laravel 13 + Filament 5 + PHP 8.4  
**Role**: Admin management interface (Filament)

**Connection Strategy**:
- ❌ NO direct PostgreSQL connection
- ✅ Uses `asy-syifaa-api` via HTTP/REST
- ✅ Caches API responses locally

**Configuration**:
```env
# .env
APP_NAME=Asy-Syifaa ERP
DATABASE_URL=... # (deprecated - for cache only)
API_BASE_URL=https://api.asy-syifaa.com/api/v1
API_SECRET_KEY=... # for service-to-service auth
```

**Data Flow**:
```
Filament Admin UI
    ↓ (HTTP request)
Laravel Controller
    ↓ (HTTP client)
asy-syifaa-api/REST
    ↓ (SQL query)
PostgreSQL
```

---

### 2.3 asy-syifaa-website (PHP Static)
**Framework**: PHP native (static hosting on Hostinger)  
**Role**: Public landing page + PPDB registration form

**Connection Strategy**:
- ❌ NO database connection (static files)
- ✅ PPDB form → API call to `asy-syifaa-api`
- ✅ CMS data loaded via API

**Configuration**:
```env
API_BASE_URL=https://api.asy-syifaa.com/api/v1
PPDB_WA_GATEWAY=https://waha.devlike.pro/api/sendText
```

---

### 2.4 asy-syifaa-app (Vue 3 PWA)
**Framework**: Vue 3 + Vite + PWA  
**Role**: Parent/Guardian dashboard app

**Connection Strategy**:
- ❌ NO database connection
- ✅ REST API calls to `asy-syifaa-api`
- ✅ Offline-first with IndexedDB sync

**Configuration**:
```env
VITE_API_URL=https://api.asy-syifaa.com/api/v1
VITE_WS_URL=wss://api.asy-syifaa.com/ws
```

---

## 3. Data Standardization

### 3.1 ID Strategy (UUID)
All primary keys MUST be UUID v4 (not auto-increment):

```prisma
model User {
  id    String  @id @default(uuid()) @db.Uuid
  // ...
}
```

**Benefits**:
- Globally unique across distributed systems
- Safe for horizontal scaling
- Privacy (cannot guess sequential IDs)

---

### 3.2 Timestamps (Mandatory)
Every entity MUST have audit timestamps:

```prisma
model Entity {
  id        String    @id @default(uuid()) @db.Uuid
  createdAt DateTime  @default(now())
  updatedAt DateTime  @updatedAt
  
  // Optional: soft delete
  deletedAt DateTime?
}
```

---

### 3.3 Foreign Key Relationships
Always use explicit relations with cascade policies:

```prisma
model Santri {
  id     String  @id @default(uuid()) @db.Uuid
  userId String  @unique @db.Uuid
  user   User    @relation(fields: [userId], references: [id], onDelete: Restrict, onUpdate: Cascade)
  
  kamarId String? @db.Uuid
  kamar   Kamar?  @relation(fields: [kamarId], references: [id], onDelete: SetNull, onUpdate: Cascade)
}
```

**Cascade Policies**:
- `Restrict`: Prevent delete if referenced (default for critical data)
- `SetNull`: Set foreign key to NULL if parent deleted (for optional relations)
- `Cascade`: Delete child if parent deleted (use carefully)

---

### 3.4 Indexing Strategy
Add indexes on frequently queried columns:

```prisma
model Santri {
  id            String   @id @default(uuid()) @db.Uuid
  namaLengkap   String
  statusAktif   Boolean  @default(true)
  kamarId       String?  @db.Uuid

  @@index([namaLengkap])  // Search queries
  @@index([statusAktif])   // Filter queries
  @@index([kamarId])      // Join queries
}
```

---

## 4. Migration Strategy

### 4.1 Prisma Migrations (Single Source of Truth)
All schema changes MUST go through Prisma:

```bash
# Create new migration
npx prisma migrate dev --name add_student_phone

# Review migration file: prisma/migrations/[timestamp]_add_student_phone/migration.sql

# Apply to production
npx prisma migrate deploy
```

**Migration File Location**: `prisma/migrations/`

### 4.2 Version Control
- ✅ Commit migration files to git
- ✅ Tag releases with schema version
- ❌ Never modify committed migrations
- ❌ Never run raw SQL outside Prisma

### 4.3 Backup Strategy
Before any migration on production:
```bash
# Backup PostgreSQL
pg_dump -h 187.77.116.167 -U postgres -d asy_syifaa > backup_$(date +%Y%m%d_%H%M%S).sql

# Test migration on staging first
npx prisma migrate deploy --preview-feature

# Apply to production
npx prisma migrate deploy
```

---

## 5. CRUD Best Practices

### 5.1 API Response Format (Standardized)
```json
{
  "success": true,
  "statusCode": 200,
  "message": "Data retrieved successfully",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "namaLengkap": "Ahmad Santri",
    "createdAt": "2026-01-15T10:30:00Z",
    "updatedAt": "2026-01-15T10:30:00Z"
  },
  "meta": {
    "page": 1,
    "limit": 10,
    "total": 150,
    "totalPages": 15
  }
}
```

### 5.2 Error Handling
```json
{
  "success": false,
  "statusCode": 400,
  "message": "Validation failed",
  "errors": [
    {
      "field": "namaLengkap",
      "message": "Name is required"
    }
  ]
}
```

### 5.3 Authentication
All API calls require JWT token:
```
Authorization: Bearer eyJhbGciOiJIUzI1NiIs...
```

---

## 6. Development Workflow

### 6.1 Local Development
```bash
# 1. Setup environment
cd asy-syifaa-api
cp .env.example .env
npm install

# 2. Setup local PostgreSQL
docker run -d \
  --name postgres \
  -e POSTGRES_PASSWORD=postgres \
  -e POSTGRES_DB=asy_syifaa \
  -p 5432:5432 \
  postgres:16

# 3. Apply migrations
npx prisma migrate dev

# 4. Seed test data
npx prisma db seed

# 5. Start development server
npm run dev
```

### 6.2 Adding New Entity
```bash
# 1. Define in Prisma schema
# Edit: prisma/schema.prisma
model Tagihan {
  id        String   @id @default(uuid()) @db.Uuid
  santriId  String   @db.Uuid
  santri    Santri   @relation(fields: [santriId], references: [id], onDelete: Cascade)
  jumlah    Int
  createdAt DateTime @default(now())
  updatedAt DateTime @updatedAt
}

# 2. Create migration
npx prisma migrate dev --name add_tagihan

# 3. Generate Prisma client
npx prisma generate

# 4. Create API routes (Express)
# src/routes/tagihan.ts
router.get('/tagihan', getTagihan);
router.post('/tagihan', createTagihan);
router.put('/tagihan/:id', updateTagihan);
router.delete('/tagihan/:id', deleteTagihan);

# 5. Test locally
npm test

# 6. Commit & push
git add .
git commit -m "feat: Add tagihan (billing) module"
git push origin main
```

---

## 7. Testing Database Changes

### 7.1 Unit Tests (Jest)
```javascript
describe('Santri API', () => {
  it('should create santri', async () => {
    const response = await request(app)
      .post('/api/v1/santri')
      .send({
        userId: '550e8400-e29b-41d4-a716-446655440000',
        nis: 'NIS001',
        namaLengkap: 'Ahmad',
        tanggalLahir: '2010-01-01'
      });
    
    expect(response.status).toBe(201);
    expect(response.body.data.id).toBeDefined();
  });
});
```

### 7.2 Integration Tests
- Test database constraints
- Test foreign key cascades
- Test migration rollback

### 7.3 Performance Tests
- Index verification
- Query execution time
- Connection pooling

---

## 8. Monitoring & Maintenance

### 8.1 Database Health Checks
```sql
-- Check table sizes
SELECT schemaname, tablename, pg_size_pretty(pg_total_relation_size(schemaname||'.'||tablename)) AS size
FROM pg_tables
WHERE schemaname NOT IN ('pg_catalog', 'information_schema')
ORDER BY pg_total_relation_size(schemaname||'.'||tablename) DESC;

-- Check slow queries
SELECT query, mean_exec_time, stddev_exec_time, calls
FROM pg_stat_statements
ORDER BY mean_exec_time DESC
LIMIT 10;
```

### 8.2 Backup Schedule
- Daily incremental backups (3:00 AM server time)
- Weekly full backups (Sundays)
- Monthly archive (30-day retention)

### 8.3 Monitoring Queries
- Connection count
- Query performance
- Replication lag (if using read replicas)

---

## 9. Breaking Changes Policy

### 9.1 Backward Compatibility
Schema changes must maintain backward compatibility:

**❌ Breaking Change**:
```prisma
// BEFORE
model Santri {
  id String @id @default(uuid()) @db.Uuid
  nis String
}

// AFTER (removes nis field)
model Santri {
  id String @id @default(uuid()) @db.Uuid
}
```

**✅ Non-Breaking Change**:
```prisma
// BEFORE
model Santri {
  id String @id @default(uuid()) @db.Uuid
}

// AFTER (adds optional field)
model Santri {
  id String @id @default(uuid()) @db.Uuid
  nomorTelepon String?
}
```

### 9.2 Deprecation Process
1. Add new field alongside old field
2. Update API to populate both
3. Mark old field as deprecated in documentation
4. Wait 2 release cycles
5. Remove deprecated field

---

## 10. Troubleshooting

### Connection Issues
```bash
# Test connection
psql -h 187.77.116.167 -U postgres -d asy_syifaa

# Check connection limit
SELECT datname, usename, count(*) FROM pg_stat_activity GROUP BY datname, usename;

# Kill idle connections
SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE state = 'idle';
```

### Migration Conflicts
```bash
# Reset migrations (dev only)
npx prisma migrate reset

# Manually mark migration as applied
npx prisma migrate resolve --rolled-back "[timestamp]_migration_name"
```

### Performance Issues
```bash
# Analyze query plan
EXPLAIN ANALYZE SELECT * FROM santri WHERE namaLengkap LIKE '%Ahmad%';

# Add missing indexes
CREATE INDEX idx_santri_nama ON santri(namaLengkap);
```

---

## 11. Rollout Checklist

- [ ] Prisma schema updated
- [ ] Migration file created and tested
- [ ] API endpoints updated (if needed)
- [ ] Unit tests written & passing
- [ ] Integration tests passing
- [ ] Code reviewed
- [ ] Staging deployment successful
- [ ] Production migration backup created
- [ ] Production deployment confirmed
- [ ] Monitoring alerts active

---

## Next Steps

1. **✅ Phase 1** (Current): Document foundation & audit existing setup
2. **Phase 2**: Migrate ERP (Laravel) to API-only architecture
3. **Phase 3**: Implement database versioning & semantic release tagging
4. **Phase 4**: Setup automated testing & performance benchmarks
5. **Phase 5**: Implement read-replica for scaling

---

**Document Version**: v1.0  
**Last Modified**: 2026-06-01  
**Maintained By**: Architecture Team

---

## 12. Data Ownership Matrix

| Entity/Domain | Source of Truth | CRUD Path | Consumer Modules |
|---|---|---|---|
| Users/Auth Profile | PostgreSQL (`asy_syifaa`) | `asy-syifaa-api` | ERP, App, Website |
| Santri Master Data | PostgreSQL (`asy_syifaa`) | `asy-syifaa-api` | ERP, App, Website |
| PPDB/SPMB Registrations | PostgreSQL (`asy_syifaa`) | `asy-syifaa-api` | Website, ERP |
| Tagihan/Pembayaran | PostgreSQL (`asy_syifaa`) | `asy-syifaa-api` | ERP, App |
| Presensi/Perizinan | PostgreSQL (`asy_syifaa`) | `asy-syifaa-api` | ERP, App |
| Konten Publik (pengumuman/galeri/event) | PostgreSQL (`asy_syifaa`) | `asy-syifaa-api` | Website, App |

Aturan baku:
- `asy-syifaa-api` adalah satu-satunya jalur CRUD domain.
- `asy-syifaa-erp`, `asy-syifaa-website`, `asy-syifaa-app` dilarang melakukan CRUD domain langsung ke DB.
- DB lokal non-API hanya untuk kebutuhan teknis non-domain (cache/session/queue/log).

## 13. Exception Registry (Direct DB Sementara)

Pengecualian direct DB hanya boleh jika semua syarat ini terpenuhi:
- Hanya read-only pada fase awal.
- Menggunakan DB role paling minimal (least privilege).
- Ada tiket/approval dan masa berlaku exception.
- Wajib dicatat di tabel berikut.

| Module | Scope | Reason | Access Type | Owner | Expiry | Back-to-API Plan |
|---|---|---|---|---|---|---|
| *(kosong saat ini)* | - | - | - | - | - | - |

## 14. CI/CD Deployment Matrix

| Module | Workflow | Trigger Branch | Trigger Path | Target |
|---|---|---|---|---|
| ERP Laravel | `.github/workflows/deploy-erp.yml` | `main` | `asy-syifaa-erp/**` | VPS `/opt/asy-syifaa/erp/src` |
| Website PHP | `.github/workflows/deploy-website.yml` | `main` | `asy-syifaa-website/**` | Hostinger `~/public_html` |
| PWA App | `.github/workflows/deploy-app.yml` | `main` | `asy-syifaa-app/**` | VPS `/opt/asy-syifaa/app/` |
| API Node/Prisma | `.github/workflows/deploy-api.yml` | `main` | `asy-syifaa-api/**` | VPS `/opt/asy-syifaa/api` |

## 15. Operational Guardrails

- Production wajib memakai PostgreSQL (`DATABASE_URL` valid), memory fallback dilarang.
- Memory fallback hanya boleh di dev/test dan harus eksplisit (`ALLOW_MEMORY_DB_FALLBACK=true`).
- Semua perubahan schema wajib via Prisma migration dan commit ke repository.
- Sebelum migration production: backup DB, deploy migration, lakukan healthcheck.
- Rollback minimum:
  - aplikasi: rollback commit/deploy terakhir per modul,
  - database: restore backup terakhir bila migration bermasalah.
