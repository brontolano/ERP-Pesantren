# Project Instructions

## Struktur Folder Project

```
C:\Users\maula\Downloads\ABDM\_Agent Manager\_ERP\
├── asy-syifaa-website/   → Landing page www.asy-syifaa.com (PHP static)
├── asy-syifaa-app/       → PWA Wali app.asy-syifaa.com (Vue 3 + Vite)
├── asy-syifaa-erp/       → ERP + API backend erp.asy-syifaa.com (Laravel + Filament)
├── _branding/            → Logo & icon assets
├── skills/               → Claude Code skills
├── arsip_project/        → File lama / backup (JANGAN DISENTUH)
├── CLAUDE.md             → File ini
└── README.md
```

**Catatan penamaan:**
- `asy-syifaa-erp` = Filament admin panel + REST API (monolith Laravel)
- Jika folder masih bernama `erp-pesantren` atau `asy-syifaa-platform`, jalankan `RENAME_FOLDERS.bat`

## Auto Skill Selection

Ketika user memberi perintah, **otomatis baca dan terapkan skill yang paling relevan** dari folder `skills/`. Jangan tunggu user menyebut nama skill — analisis konteks perintah lalu pilih sendiri.

### Mapping Konteks → Skill

| Konteks Perintah | Skill yang Digunakan |
|---|---|
| Desain sistem, arsitektur, diagram, scaling | `skills/senior-architect/SKILL.md` |
| Frontend, React, Next.js, UI, CSS, Tailwind | `skills/senior-frontend/SKILL.md` |
| Backend, API, database, server, Express, GraphQL | `skills/senior-backend/SKILL.md` |
| Fullstack, end-to-end, project setup | `skills/senior-fullstack/SKILL.md` |
| Testing, QA, automation test, unit test | `skills/senior-qa/SKILL.md` |
| CI/CD, Docker, Kubernetes, deployment, infra | `skills/senior-devops/SKILL.md` |
| Security ops, compliance, monitoring keamanan | `skills/senior-secops/SKILL.md` |
| Security architecture, pentest, vulnerability | `skills/senior-security/SKILL.md` |
| Code review, refactor, code quality | `skills/code-reviewer/SKILL.md` |
| Data science, statistik, analytics, experiment | `skills/senior-data-scientist/SKILL.md` |
| Data pipeline, ETL, data infra, Spark, Airflow | `skills/senior-data-engineer/SKILL.md` |
| ML, model deployment, MLOps, training | `skills/senior-ml-engineer/SKILL.md` |
| Prompt engineering, LLM, RAG, agents | `skills/senior-prompt-engineer/SKILL.md` |
| Computer vision, image, video, object detection | `skills/senior-computer-vision/SKILL.md` |
| AWS, cloud architecture, S3, Lambda, EC2 | `skills/aws-solution-architect/SKILL.md` |
| Microsoft 365, tenant, Azure AD, SharePoint | `skills/ms365-tenant-manager/SKILL.md` |
| TDD, test-driven development | `skills/tdd-guide/SKILL.md` |
| Evaluasi tech stack, perbandingan teknologi | `skills/tech-stack-evaluator/SKILL.md` |

### Aturan

1. **Baca SKILL.md** dari skill terpilih sebelum menjawab — gunakan pola, standar, dan best practice dari skill tersebut.
2. **Referensi tambahan**: baca juga file di `skills/<nama>/references/` jika perlu detail lebih dalam.
3. **Multi-skill**: jika perintah mencakup >1 domain (misal fullstack + DevOps), gabungkan beberapa skill.
4. **Sebutkan skill**: di awal jawaban, sebutkan singkat skill apa yang diterapkan, misal: `[Skill: senior-backend, senior-devops]`.
5. Jika tidak ada skill yang cocok, jawab seperti biasa tanpa memaksakan skill.
