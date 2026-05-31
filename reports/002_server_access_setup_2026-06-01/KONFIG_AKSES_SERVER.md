# Konfigurasi Akses Server Tersimpan

Tanggal: 2026-06-01  
Status: Tersimpan (siap dipakai ulang)

## 1) Akses MCP Hostinger

Server MCP Hostinger VPS sudah berhasil dijalankan:

```powershell
hostinger-vps-mcp --stdio
```

Output valid terakhir:
- `Initialized 62 tools`
- `MCP Server ... started successfully`

Catatan:
- Token OAuth/API disimpan oleh tooling MCP pada profile user lokal Windows (bukan di repo).
- Karena itu saya tidak menulis token rahasia ke file project.

## 2) Parameter Server yang Sudah Dikunci

- VPS Host: `187.77.116.167`
- VPS User: `root`
- SSH Port: `22`

## 3) Secret GitHub Repo ERP-Pesantren

Sudah ada:
- `VPS_HOST`
- `VPS_USER`
- `VPS_SSH_KEY`
- `HOSTINGER_HOST`
- `HOSTINGER_USER`
- `HOSTINGER_PORT`

Masih dibutuhkan agar deploy sukses penuh:
- `VPS_SSH_PASSPHRASE`
- `HOSTINGER_SSH_KEY`
- `HOSTINGER_SSH_PASSPHRASE` (jika key encrypted)

## 4) Perintah Operasional Cepat

### Cek MCP Hostinger
```powershell
hostinger-vps-mcp --stdio
```

### Cek workflow terakhir
```powershell
gh run list --limit 10
```

### Trigger deploy by path (non-destruktif)
```powershell
$ts=(Get-Date).ToString('yyyy-MM-dd HH:mm:ss')
Set-Content -LiteralPath asy-syifaa-erp/.ci-trigger -Value "trigger: $ts"
Set-Content -LiteralPath asy-syifaa-app/.ci-trigger -Value "trigger: $ts"
Set-Content -LiteralPath asy-syifaa-website/.ci-trigger -Value "trigger: $ts"
git add asy-syifaa-erp/.ci-trigger asy-syifaa-app/.ci-trigger asy-syifaa-website/.ci-trigger
git commit -m "chore(ci): retrigger deploy"
git push origin main
```
