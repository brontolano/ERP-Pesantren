# -*- coding: utf-8 -*-
import io, sys
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

CSS = """
*{margin:0;padding:0;box-sizing:border-box;-webkit-font-smoothing:antialiased;}
html,body{width:1080px;height:1920px;overflow:hidden;font-family:'Segoe UI',system-ui,sans-serif;}
.stage{position:relative;width:1080px;height:1920px;overflow:hidden;background:%(bg)s;}
.blob{position:absolute;border-radius:50%%;}
.b1{width:560px;height:560px;right:-170px;top:-130px;background:radial-gradient(circle at 30%% 30%%,#34d399,#0d8a5a);opacity:.55;}
.b2{width:320px;height:320px;left:-110px;top:%(b2top)spx;background:radial-gradient(circle at 40%% 40%%,#fcd34d,#f59e0b);opacity:.9;}
.b3{width:200px;height:200px;right:90px;top:%(b3top)spx;background:radial-gradient(circle at 40%% 40%%,#fb923c,#ea580c);opacity:.92;}
.b4{width:280px;height:280px;left:40px;bottom:-90px;background:radial-gradient(circle,#a3e635,#65a30d);opacity:.5;}
.ring{position:absolute;border-radius:50%%;border:3px dashed rgba(255,255,255,.22);width:440px;height:440px;right:-130px;top:560px;}
.content{position:relative;z-index:3;padding:84px 80px 0;}
.brandrow{display:flex;align-items:center;gap:24px;}
.logo{width:108px;height:108px;border-radius:27px;box-shadow:0 12px 30px rgba(0,0,0,.3);background:#fff;padding:9px;}
.brandname{color:#fff;font-size:42px;font-weight:800;letter-spacing:.5px;line-height:1.05;}
.brandname small{display:block;font-size:20px;font-weight:600;color:#d1fae5;letter-spacing:3px;margin-top:3px;}
.headline{color:#fff;font-size:%(hsize)spx;line-height:1.03;font-weight:900;margin-top:54px;letter-spacing:-1px;text-shadow:0 4px 18px rgba(0,0,0,.18);}
.headline .hl{color:#fde047;}
.sub{margin-top:30px;font-size:36px;font-weight:600;color:#ecfdf5;max-width:760px;}
.chips{margin-top:28px;display:flex;flex-wrap:wrap;gap:15px;max-width:840px;}
.chip{background:rgba(255,255,255,.16);border:1.5px solid rgba(255,255,255,.35);color:#fff;font-size:27px;font-weight:700;padding:13px 26px;border-radius:999px;}
.phone-wrap{position:absolute;left:50%%;transform:translateX(-50%%) rotate(%(rot)sdeg);bottom:%(pbottom)spx;z-index:4;}
.phone{width:586px;height:1190px;background:#0b1f16;border-radius:70px;padding:20px;box-shadow:0 46px 100px rgba(0,0,0,.5),0 0 0 7px #08160f;}
.screen{width:100%%;height:100%%;border-radius:52px;overflow:hidden;background:#f0fdf4;position:relative;}
.screen iframe{width:546px;height:1183px;border:0;margin-top:%(ifoff)spx;transform:scale(1.0);transform-origin:top left;}
.notch{position:absolute;top:16px;left:50%%;transform:translateX(-50%%);width:168px;height:34px;background:#0b1f16;border-radius:0 0 20px 20px;z-index:5;}
.glare{position:absolute;inset:0;background:linear-gradient(120deg,rgba(255,255,255,.10) 0%%,rgba(255,255,255,0) 30%%);z-index:6;pointer-events:none;}
.callout{position:absolute;z-index:7;background:#fff;border-radius:18px;padding:18px 24px;box-shadow:0 16px 40px rgba(0,0,0,.28);display:flex;align-items:center;gap:16px;}
.callout .ci{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:30px;background:#ecfdf3;}
.callout b{display:block;font-size:27px;color:#0f1f17;font-weight:800;}
.callout small{font-size:20px;color:#6b7280;}
.badge{position:absolute;left:80px;bottom:80px;z-index:8;display:flex;align-items:center;gap:18px;background:#000;border-radius:18px;padding:18px 30px;box-shadow:0 14px 30px rgba(0,0,0,.4);}
.badge svg{width:48px;height:48px;}
.badge .t small{display:block;color:#cbd5e1;font-size:20px;font-weight:500;}
.badge .t b{color:#fff;font-size:34px;font-weight:700;}
.pageno{position:absolute;right:60px;bottom:90px;z-index:8;color:rgba(255,255,255,.55);font-size:26px;font-weight:700;}
"""

PLAY_BADGE = ('<svg viewBox="0 0 512 512"><path fill="#00d4ff" d="M47 24 295 256 47 488c-9 9-24 2-24-11V35c0-13 15-20 24-11z"/>'
 '<path fill="#00f076" d="M47 24c5-3 11-3 17 1l210 120-58 58L47 24z"/>'
 '<path fill="#fee000" d="M403 196l-87-50-58 58 58 58 87-50c20-12 20-44 0-56z"/>'
 '<path fill="#f53d50" d="M216 254l58 58-210 120c-6 4-12 4-17 1l169-179z"/></svg>')

def badge():
    return ('<div class="badge">%s<div class="t"><small>Tersedia di</small><b>Google Play</b></div></div>' % PLAY_BADGE)

def slide(fname, style, headline, sub, chips, callouts, pageno):
    chip_html = "".join('<div class="chip">%s</div>' % c for c in chips)
    call_html = "".join(
        '<div class="callout" style="%s"><div class="ci">%s</div><div><b>%s</b><small>%s</small></div></div>'
        % (pos, ic, b, s) for (pos, ic, b, s) in callouts)
    html = """<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><style>%s</style></head>
<body><div class="stage">
<div class="blob b1"></div><div class="blob b2"></div><div class="blob b3"></div><div class="blob b4"></div><div class="ring"></div>
<div class="content">
<div class="brandrow"><img class="logo" src="https://app.asy-syifaa.com/icons/icon-192x192.png"><div class="brandname">Asy-Syifaa<small>WAL MAHMUUDIYYAH</small></div></div>
<div class="headline">%s</div>
<div class="sub">%s</div>
<div class="chips">%s</div>
</div>
<div class="phone-wrap"><div class="phone"><div class="screen"><div class="notch"></div><iframe src="app-dashboard.html" scrolling="no"></iframe><div class="glare"></div></div></div></div>
%s
%s
<div class="pageno">%s</div>
</div></body></html>""" % (style, headline, sub, chip_html, call_html, badge(), pageno)
    with open(fname, "w", encoding="utf-8") as f:
        f.write(html)
    print("wrote", fname)

# common gradient
GRAD = "linear-gradient(155deg,#0f3a24 0%,#1f6b43 38%,#15a06a 72%,#0fb37a 100%)"

# SLIDE 1 — hero, phone shows header+santri card (offset 0)
slide("slide1.html",
      CSS % dict(bg=GRAD,b2top=380,b3top=560,hsize=92,rot=-3,pbottom=-120,ifoff=0),
      'Pantau Santri<br>Anda, <span class="hl">Kapan<br>Saja</span>',
      'Semua kabar santri Anda dalam satu aplikasi, real-time.',
      ['Kehadiran','Hafalan','Tagihan','Kesehatan'],
      [], '1 / 3')

# SLIDE 2 — features, phone shows santri card + prayer (offset -260), with callouts
slide("slide2.html",
      CSS % dict(bg="linear-gradient(155deg,#0f3a24 0%,#15784c 45%,#0e9f6e 100%)",b2top=900,b3top=300,hsize=80,rot=3,pbottom=-150,ifoff=-150),
      'Tagihan & <span class="hl">Hafalan</span><br>Tak Pernah<br>Terlewat',
      'Cek SPP, tunggakan, dan progres hafalan kapan saja.',
      ['Notifikasi real-time','Riwayat lengkap'],
      [('right:60px;top:840px','💳','Tagihan SPP','Pantau & ingat jatuh tempo'),
       ('left:60px;top:1180px','📖','Hafalan','Progres & nilai santri')],
      '2 / 3')

# SLIDE 3 — daily status, phone shows prayer+status+nav (offset -470)
slide("slide3.html",
      CSS % dict(bg="linear-gradient(155deg,#0f3a24 0%,#1f6b43 50%,#16a34a 100%)",b2top=420,b3top=980,hsize=82,rot=-3,pbottom=-150,ifoff=-470),
      'Jadwal Sholat &<br><span class="hl">Status Harian</span><br>Santri',
      'Kehadiran, kesehatan, dan jadwal sholat pesantren.',
      ['Jadwal sholat','Kehadiran','Kesehatan'],
      [('right:60px;top:980px','🕌','Jadwal Sholat','Sesuai lokasi pesantren'),
       ('left:60px;top:1300px','✅','Hadir & Sehat','Status santri hari ini')],
      '3 / 3')
