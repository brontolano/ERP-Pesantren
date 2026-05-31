#!/usr/bin/env python3
"""
Generator ikon/branding Asy-Syifaa dari master .ico.
Menghasilkan seluruh aset untuk: PWA, Play Store, ERP, dan Website.
Sumber: asy-syifaa-platform/AsySyifaa.ico (500x500 RGBA, logo bundar transparan).
"""
import os
from PIL import Image

ROOT = os.path.dirname(os.path.abspath(__file__))
ERP = os.path.normpath(os.path.join(ROOT, ".."))
SRC_ICO = os.path.join(ERP, "asy-syifaa-platform", "AsySyifaa.ico")

GREEN = (31, 107, 67, 255)      # #1f6b43 brand theme
CREAM = (240, 253, 244, 255)    # #f0fdf4 background
WHITE = (255, 255, 255, 255)

def load_master():
    im = Image.open(SRC_ICO).convert("RGBA")
    # upscale master ke 1024 (LANCZOS) untuk hasil tajam di semua ukuran
    return im.resize((1024, 1024), Image.LANCZOS)

MASTER = load_master()

def ensure(d):
    os.makedirs(d, exist_ok=True)
    return d

def square_transparent(size):
    """Logo full-bleed, latar transparan (purpose: any)."""
    return MASTER.resize((size, size), Image.LANCZOS)

def square_on_bg(size, bg, scale=1.0):
    """Logo di atas latar solid (apple-touch / Play Store / favicon non-transparan)."""
    canvas = Image.new("RGBA", (size, size), bg)
    inner = int(size * scale)
    logo = MASTER.resize((inner, inner), Image.LANCZOS)
    off = (size - inner) // 2
    canvas.alpha_composite(logo, (off, off))
    return canvas

def maskable(size, bg=WHITE, safe=0.80):
    """Maskable: logo di safe-zone (~80%) + latar solid agar tak terpotong mask."""
    return square_on_bg(size, bg, scale=safe)

def save(img, path):
    ensure(os.path.dirname(path))
    img.save(path)
    print("  [ok]", os.path.relpath(path, ERP))

def save_ico(path, sizes=(16, 32, 48, 64, 128, 256)):
    ensure(os.path.dirname(path))
    base = MASTER.resize((256, 256), Image.LANCZOS)
    base.save(path, format="ICO", sizes=[(s, s) for s in sizes])
    print("  [ok]", os.path.relpath(path, ERP), "(multi-size ico)")

# ---------------------------------------------------------------------------
# 1) PWA  (asy-syifaa-app/pwa/public)
# ---------------------------------------------------------------------------
def gen_pwa():
    print("== PWA (asy-syifaa-app/pwa/public) ==")
    pub = os.path.join(ERP, "asy-syifaa-app", "pwa", "public")
    icons = ensure(os.path.join(pub, "icons"))
    for s in (72, 96, 128, 144, 152, 192, 384, 512):
        save(square_transparent(s), os.path.join(icons, f"icon-{s}x{s}.png"))
    # maskable terpisah (best practice) — latar putih, safe-zone 80%
    save(maskable(192), os.path.join(icons, "maskable-192.png"))
    save(maskable(512), os.path.join(icons, "maskable-512.png"))
    # aset includeAssets
    save_ico(os.path.join(pub, "favicon.ico"))
    save(square_on_bg(180, WHITE, 0.92), os.path.join(pub, "apple-touch-icon.png"))
    save(square_transparent(512), os.path.join(pub, "logo.png"))

# ---------------------------------------------------------------------------
# 2) Play Store assets (asy-syifaa-app/playstore-assets)
# ---------------------------------------------------------------------------
def gen_playstore():
    print("== Play Store assets ==")
    out = ensure(os.path.join(ERP, "asy-syifaa-app", "playstore-assets"))
    # Hi-res icon 512x512 — TANPA transparansi (wajib Play Console)
    save(square_on_bg(512, WHITE, 0.92), os.path.join(out, "playstore-icon-512.png"))
    # Feature graphic 1024x500
    fg = Image.new("RGBA", (1024, 500), GREEN)
    logo = MASTER.resize((420, 420), Image.LANCZOS)
    fg.alpha_composite(logo, (60, (500 - 420) // 2))
    save(fg.convert("RGB").convert("RGBA"), os.path.join(out, "feature-graphic-1024x500.png"))

# ---------------------------------------------------------------------------
# 3) ERP  (erp-pesantren/public)
# ---------------------------------------------------------------------------
def gen_erp():
    print("== ERP (erp-pesantren/public) ==")
    pub = os.path.join(ERP, "erp-pesantren", "public")
    if not os.path.isdir(pub):
        print("  ! lewati: public/ ERP tidak ditemukan")
        return
    save_ico(os.path.join(pub, "favicon.ico"))
    # Filament panel & brand-logo blade pakai folder public/images/
    images = ensure(os.path.join(pub, "images"))
    save(square_transparent(512), os.path.join(images, "logo.png"))
    save(square_transparent(192), os.path.join(images, "logo-192.png"))
    save(square_on_bg(180, WHITE, 0.92), os.path.join(images, "favicon.png"))
    # juga simpan ke public/img untuk kompatibilitas referensi lama
    img = ensure(os.path.join(pub, "img"))
    save(square_transparent(512), os.path.join(img, "logo.png"))
    save(square_transparent(192), os.path.join(img, "logo-192.png"))
    save(square_on_bg(180, WHITE, 0.92), os.path.join(pub, "apple-touch-icon.png"))

# ---------------------------------------------------------------------------
# 4) Website (asy-syifaa-platform)
# ---------------------------------------------------------------------------
def gen_website():
    print("== Website (asy-syifaa-platform) ==")
    base = os.path.join(ERP, "asy-syifaa-platform")
    save_ico(os.path.join(base, "favicon.ico"))
    save(square_on_bg(180, WHITE, 0.92), os.path.join(base, "apple-touch-icon.png"))
    assets_img = ensure(os.path.join(base, "assets", "img"))
    save(square_transparent(512), os.path.join(assets_img, "logo.png"))
    save(square_transparent(192), os.path.join(assets_img, "logo-192.png"))
    # Logo kanonik yang dipakai 27 halaman (navbar + favicon): assets/media/images/logo.png
    media_img = ensure(os.path.join(base, "assets", "media", "images"))
    save(square_transparent(512), os.path.join(media_img, "logo.png"))
    save(square_transparent(192), os.path.join(media_img, "logo-192.png"))

if __name__ == "__main__":
    print("Master:", MASTER.size, "from", os.path.relpath(SRC_ICO, ERP))
    gen_pwa()
    gen_playstore()
    gen_erp()
    gen_website()
    print("\nSelesai generate seluruh aset ikon/branding.")
