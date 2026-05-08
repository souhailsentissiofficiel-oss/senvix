<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Senvix — Advanced Password Security Platform</title>
  <meta name="description" content="Advanced password security platform designed to protect your digital identity with modern encryption and intelligent security tools.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=JetBrains+Mono:wght@300;400;500&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    /* ─── Reset & Root ─── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --neon:       #00FF87;
      --neon-dim:   #00E676;
      --neon-dark:  #00C853;
      --neon-glow:  rgba(0, 255, 135, 0.35);
      --neon-soft:  rgba(0, 255, 135, 0.12);
      --cyan:       #18FFFF;
      --cyan-glow:  rgba(24, 255, 255, 0.2);
      --bg-void:    #020A05;
      --bg-deep:    #040E08;
      --bg-card:    rgba(6, 20, 10, 0.75);
      --glass:      rgba(0, 255, 135, 0.04);
      --glass-b:    rgba(0, 255, 135, 0.12);
      --glass-hov:  rgba(0, 255, 135, 0.08);
      --text-1:     #F0FFF4;
      --text-2:     #A8D5B5;
      --text-3:     #5A8A6A;
      --ease:       cubic-bezier(0.16, 1, 0.3, 1);
    }

    html { scroll-behavior: smooth; }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--bg-void);
      color: var(--text-1);
      min-height: 100vh;
      overflow-x: hidden;
      -webkit-font-smoothing: antialiased;
    }

    /* ─── Background Layers ─── */
    .bg-scene {
      position: fixed;
      inset: 0;
      z-index: 0;
      pointer-events: none;
    }

    .bg-gradient {
      position: absolute;
      inset: 0;
      background:
        radial-gradient(ellipse 70% 55% at 15% 10%, rgba(0,255,135,0.13) 0%, transparent 60%),
        radial-gradient(ellipse 50% 40% at 85% 85%, rgba(0,200,83,0.09) 0%, transparent 55%),
        radial-gradient(ellipse 40% 30% at 50% 45%, rgba(24,255,255,0.05) 0%, transparent 50%),
        linear-gradient(160deg, #020A05 0%, #04100A 40%, #020A05 100%);
    }

    .bg-grid {
      position: absolute;
      inset: 0;
      background-image:
        linear-gradient(rgba(0,255,135,0.035) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,255,135,0.035) 1px, transparent 1px);
      background-size: 56px 56px;
      mask-image: radial-gradient(ellipse 90% 90% at 50% 50%, black 40%, transparent 100%);
    }

    /* Floating orbs */
    .orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(90px);
      animation: orbDrift 18s ease-in-out infinite;
    }
    .orb-1 {
      width: 600px; height: 600px;
      top: -250px; left: -200px;
      background: radial-gradient(circle, rgba(0,255,135,0.14) 0%, transparent 65%);
      animation-duration: 22s;
    }
    .orb-2 {
      width: 450px; height: 450px;
      bottom: -150px; right: -150px;
      background: radial-gradient(circle, rgba(0,200,83,0.10) 0%, transparent 65%);
      animation-duration: 28s;
      animation-delay: -10s;
    }
    .orb-3 {
      width: 300px; height: 300px;
      top: 45%; left: 55%;
      background: radial-gradient(circle, rgba(24,255,255,0.07) 0%, transparent 65%);
      animation-duration: 20s;
      animation-delay: -6s;
    }
    @keyframes orbDrift {
      0%,100% { transform: translate(0,0) scale(1); }
      33%      { transform: translate(40px,-55px) scale(1.06); }
      66%      { transform: translate(-30px,25px) scale(0.94); }
    }

    /* ─── Particles Canvas ─── */
    #particleCanvas {
      position: fixed;
      inset: 0;
      z-index: 1;
      pointer-events: none;
      opacity: 0.6;
    }

    /* ─── Navbar ─── */
    .navbar {
      position: fixed;
      top: 0; left: 0; right: 0;
      z-index: 100;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 48px;
      height: 68px;
      background: rgba(2, 10, 5, 0.7);
      border-bottom: 1px solid rgba(0,255,135,0.08);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
    }

    .nav-logo {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
    }

    .nav-logo-icon {
      width: 34px; height: 34px;
      background: linear-gradient(135deg, rgba(0,255,135,0.15), rgba(0,200,83,0.08));
      border: 1px solid rgba(0,255,135,0.3);
      border-radius: 9px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 15px;
      color: var(--neon);
      box-shadow: 0 0 16px rgba(0,255,135,0.2);
    }

    .nav-logo-text {
      font-family: 'Syne', sans-serif;
      font-size: 20px;
      font-weight: 800;
      letter-spacing: -0.5px;
      background: linear-gradient(135deg, #fff 30%, var(--neon) 80%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 32px;
      list-style: none;
    }

    .nav-links a {
      font-size: 13.5px;
      color: var(--text-2);
      text-decoration: none;
      font-weight: 400;
      letter-spacing: 0.2px;
      transition: color 0.25s;
    }

    .nav-links a:hover { color: var(--neon); }

    .nav-cta {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .btn-nav-login {
      padding: 8px 18px;
      background: transparent;
      border: 1px solid rgba(0,255,135,0.25);
      border-radius: 8px;
      color: var(--neon-dim);
      font-family: 'DM Sans', sans-serif;
      font-size: 13px;
      font-weight: 500;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.3s;
    }
    .btn-nav-login:hover {
      background: var(--glass);
      border-color: rgba(0,255,135,0.5);
      box-shadow: 0 0 16px rgba(0,255,135,0.15);
    }

    .btn-nav-signup {
      padding: 8px 20px;
      background: linear-gradient(135deg, #00C853, #00E676);
      border: none;
      border-radius: 8px;
      color: #020A05;
      font-family: 'Syne', sans-serif;
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.3s var(--ease);
      box-shadow: 0 4px 20px rgba(0,200,83,0.35);
    }
    .btn-nav-signup:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 28px rgba(0,200,83,0.5);
    }

    @media (max-width: 768px) {
      .navbar { padding: 0 20px; }
      .nav-links { display: none; }
    }

    /* ─── HERO ─── */
    .hero {
      position: relative;
      z-index: 10;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 100px 24px 60px;
      text-align: center;
    }

    .hero-inner {
      max-width: 820px;
      width: 100%;
    }

    /* Badge */
    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 16px;
      background: rgba(0,255,135,0.07);
      border: 1px solid rgba(0,255,135,0.18);
      border-radius: 40px;
      font-family: 'JetBrains Mono', monospace;
      font-size: 11.5px;
      color: var(--neon-dim);
      letter-spacing: 0.5px;
      margin-bottom: 36px;
      animation: fadeDown 0.7s var(--ease) 0.1s both;
    }

    .badge-dot {
      width: 6px; height: 6px;
      border-radius: 50%;
      background: var(--neon);
      box-shadow: 0 0 10px var(--neon);
      animation: blink 2s ease-in-out infinite;
    }

    @keyframes blink {
      0%,100% { opacity: 1; }
      50%      { opacity: 0.3; }
    }

    /* Shield Icon */
    .hero-icon-wrap {
      margin: 0 auto 36px;
      width: 120px; height: 120px;
      position: relative;
      animation: fadeDown 0.7s var(--ease) 0.2s both;
    }

    .hero-icon-ring {
      position: absolute;
      inset: -16px;
      border-radius: 50%;
      border: 1px solid rgba(0,255,135,0.15);
      animation: ringRotate 12s linear infinite;
    }

    .hero-icon-ring::before {
      content: '';
      position: absolute;
      top: -3px; left: 50%;
      width: 6px; height: 6px;
      border-radius: 50%;
      background: var(--neon);
      box-shadow: 0 0 14px var(--neon);
      transform: translateX(-50%);
    }

    .hero-icon-ring-2 {
      position: absolute;
      inset: -30px;
      border-radius: 50%;
      border: 1px solid rgba(0,255,135,0.06);
      animation: ringRotate 20s linear infinite reverse;
    }

    @keyframes ringRotate {
      to { transform: rotate(360deg); }
    }

    .hero-icon-core {
      width: 120px; height: 120px;
      background: linear-gradient(145deg, rgba(0,20,8,0.95), rgba(0,40,15,0.9));
      border: 1px solid rgba(0,255,135,0.25);
      border-radius: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow:
        0 0 60px rgba(0,255,135,0.2),
        0 0 120px rgba(0,255,135,0.08),
        inset 0 1px 0 rgba(0,255,135,0.1);
      position: relative;
      animation: iconPulse 3.5s ease-in-out infinite;
    }

    @keyframes iconPulse {
      0%,100% { box-shadow: 0 0 60px rgba(0,255,135,0.2), 0 0 120px rgba(0,255,135,0.08), inset 0 1px 0 rgba(0,255,135,0.1); }
      50%      { box-shadow: 0 0 80px rgba(0,255,135,0.35), 0 0 160px rgba(0,255,135,0.15), inset 0 1px 0 rgba(0,255,135,0.1); }
    }

    .hero-icon-core i {
      font-size: 48px;
      color: var(--neon);
      filter: drop-shadow(0 0 16px rgba(0,255,135,0.8));
    }

    /* Title */
    .hero-title {
      font-family: 'Syne', sans-serif;
      font-size: clamp(52px, 8vw, 92px);
      font-weight: 800;
      letter-spacing: -3px;
      line-height: 0.95;
      margin-bottom: 24px;
      animation: fadeDown 0.7s var(--ease) 0.3s both;
    }

    .title-white { color: #ffffff; }

    .title-neon {
      background: linear-gradient(135deg, var(--neon) 20%, var(--cyan) 80%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      filter: drop-shadow(0 0 30px rgba(0,255,135,0.4));
    }

    /* Subtitle */
    .hero-sub {
      font-size: clamp(15px, 2.2vw, 18px);
      color: var(--text-2);
      line-height: 1.7;
      max-width: 580px;
      margin: 0 auto 48px;
      font-weight: 300;
      animation: fadeDown 0.7s var(--ease) 0.4s both;
    }

    /* CTA Buttons */
    .hero-cta {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      flex-wrap: wrap;
      margin-bottom: 60px;
      animation: fadeDown 0.7s var(--ease) 0.5s both;
    }

    .btn-primary-hero {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 16px 36px;
      background: linear-gradient(135deg, #00C853, #00E676);
      border: none;
      border-radius: 14px;
      color: #020A05;
      font-family: 'Syne', sans-serif;
      font-size: 15px;
      font-weight: 700;
      letter-spacing: 0.2px;
      cursor: pointer;
      text-decoration: none;
      position: relative;
      overflow: hidden;
      transition: all 0.35s var(--ease);
      box-shadow:
        0 8px 32px rgba(0,200,83,0.45),
        0 2px 8px rgba(0,0,0,0.3),
        inset 0 1px 0 rgba(255,255,255,0.2);
    }

    .btn-primary-hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.18), transparent);
      transform: translateX(-100%);
      transition: transform 0.55s;
    }

    .btn-primary-hero:hover::before { transform: translateX(100%); }

    .btn-primary-hero:hover {
      transform: translateY(-3px);
      box-shadow: 0 14px 48px rgba(0,200,83,0.6), 0 4px 12px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.2);
    }

    .btn-primary-hero:active { transform: translateY(0); }

    .btn-secondary-hero {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 15px 34px;
      background: rgba(0,255,135,0.05);
      border: 1px solid rgba(0,255,135,0.2);
      border-radius: 14px;
      color: var(--neon-dim);
      font-family: 'DM Sans', sans-serif;
      font-size: 15px;
      font-weight: 500;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.35s var(--ease);
      backdrop-filter: blur(10px);
    }

    .btn-secondary-hero:hover {
      background: rgba(0,255,135,0.1);
      border-color: rgba(0,255,135,0.45);
      color: var(--neon);
      transform: translateY(-2px);
      box-shadow: 0 8px 30px rgba(0,255,135,0.15);
    }

    /* Stats strip */
    .hero-stats {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0;
      animation: fadeDown 0.7s var(--ease) 0.6s both;
    }

    .stat-pill {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 0 32px;
      border-right: 1px solid rgba(0,255,135,0.1);
    }

    .stat-pill:last-child { border-right: none; }

    .stat-num {
      font-family: 'Syne', sans-serif;
      font-size: 26px;
      font-weight: 800;
      color: var(--neon);
      filter: drop-shadow(0 0 12px rgba(0,255,135,0.5));
      line-height: 1;
      margin-bottom: 4px;
    }

    .stat-lbl {
      font-size: 11px;
      color: var(--text-3);
      text-transform: uppercase;
      letter-spacing: 1px;
      font-family: 'JetBrains Mono', monospace;
    }

    @media (max-width: 480px) {
      .stat-pill { padding: 0 16px; }
      .stat-num  { font-size: 20px; }
    }

    /* Animations */
    @keyframes fadeDown {
      from { opacity: 0; transform: translateY(-20px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ─── Glass Panel (mock screenshot) ─── */
    .hero-panel-wrap {
      position: relative;
      z-index: 10;
      max-width: 900px;
      margin: 70px auto 0;
      padding: 0 24px;
      animation: panelRise 1s var(--ease) 0.7s both;
    }

    @keyframes panelRise {
      from { opacity: 0; transform: translateY(40px) scale(0.97); }
      to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .glass-panel {
      background: rgba(5,16,9,0.82);
      border: 1px solid rgba(0,255,135,0.13);
      border-radius: 24px;
      overflow: hidden;
      box-shadow:
        0 0 0 1px rgba(255,255,255,0.02),
        0 24px 80px rgba(0,0,0,0.6),
        0 0 80px rgba(0,255,135,0.07);
      backdrop-filter: blur(24px);
    }

    .panel-bar {
      display: flex;
      align-items: center;
      padding: 14px 20px;
      border-bottom: 1px solid rgba(0,255,135,0.08);
      gap: 8px;
      background: rgba(0,255,135,0.02);
    }

    .panel-dot { width: 10px; height: 10px; border-radius: 50%; }
    .dot-red   { background: #FF5F57; }
    .dot-yel   { background: #FEBC2E; }
    .dot-grn   { background: #28C840; }

    .panel-url {
      flex: 1;
      margin: 0 12px;
      background: rgba(0,0,0,0.3);
      border: 1px solid rgba(0,255,135,0.08);
      border-radius: 6px;
      padding: 5px 12px;
      font-family: 'JetBrains Mono', monospace;
      font-size: 11px;
      color: var(--text-3);
      text-align: center;
    }

    .panel-url span { color: var(--neon-dim); }

    .panel-body {
      padding: 28px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    @media (max-width: 600px) {
      .panel-body { grid-template-columns: 1fr; }
    }

    /* Mini stat cards inside panel */
    .mini-card {
      background: rgba(0,255,135,0.03);
      border: 1px solid rgba(0,255,135,0.09);
      border-radius: 14px;
      padding: 18px 20px;
      position: relative;
      overflow: hidden;
      transition: all 0.3s;
    }

    .mini-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(0,255,135,0.3), transparent);
    }

    .mini-card:hover {
      border-color: rgba(0,255,135,0.2);
      background: rgba(0,255,135,0.06);
      transform: translateY(-2px);
    }

    .mini-card-label {
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 1.2px;
      color: var(--text-3);
      font-family: 'JetBrains Mono', monospace;
      margin-bottom: 10px;
    }

    .mini-card-value {
      font-family: 'Syne', sans-serif;
      font-size: 22px;
      font-weight: 700;
      color: #fff;
      margin-bottom: 4px;
    }

    .mini-card-sub {
      font-size: 11px;
      color: var(--text-3);
    }

    .mini-icon {
      position: absolute;
      top: 16px; right: 16px;
      width: 32px; height: 32px;
      background: rgba(0,255,135,0.08);
      border: 1px solid rgba(0,255,135,0.15);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--neon);
      font-size: 13px;
    }

    .tag-green {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 2px 8px;
      background: rgba(0,255,135,0.1);
      border: 1px solid rgba(0,255,135,0.2);
      border-radius: 20px;
      font-size: 10px;
      color: var(--neon);
      font-family: 'JetBrains Mono', monospace;
    }

    /* Vault bar preview */
    .vault-preview {
      grid-column: 1 / -1;
      background: rgba(0,255,135,0.03);
      border: 1px solid rgba(0,255,135,0.09);
      border-radius: 14px;
      overflow: hidden;
    }

    .vault-preview-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 14px 18px;
      border-bottom: 1px solid rgba(0,255,135,0.06);
      font-size: 11px;
      color: var(--text-3);
      font-family: 'JetBrains Mono', monospace;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    .vault-row {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 11px 18px;
      border-bottom: 1px solid rgba(255,255,255,0.02);
      transition: background 0.2s;
    }

    .vault-row:last-child { border-bottom: none; }
    .vault-row:hover { background: rgba(0,255,135,0.03); }

    .vault-row-icon {
      width: 28px; height: 28px;
      border-radius: 7px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      flex-shrink: 0;
    }
    .vi-blue { background: rgba(59,130,246,0.15); color: #60A5FA; }
    .vi-red  { background: rgba(239,68,68,0.15);  color: #F87171; }
    .vi-yel  { background: rgba(245,158,11,0.15); color: #FCD34D; }

    .vault-row-label {
      font-size: 12.5px;
      color: var(--text-1);
      font-weight: 500;
      flex: 1;
    }

    .vault-row-pw {
      font-family: 'JetBrains Mono', monospace;
      font-size: 11px;
      color: var(--text-3);
      letter-spacing: 3px;
    }

    .str-chip {
      padding: 2px 8px;
      border-radius: 10px;
      font-size: 9.5px;
      font-family: 'JetBrains Mono', monospace;
      font-weight: 600;
    }
    .str-strong { background: rgba(0,255,135,0.1); border: 1px solid rgba(0,255,135,0.2); color: var(--neon); }
    .str-fair   { background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2); color: #FCD34D; }

    /* ─── Features Section ─── */
    .features {
      position: relative;
      z-index: 10;
      padding: 120px 24px;
      max-width: 1100px;
      margin: 0 auto;
    }

    .section-label {
      text-align: center;
      font-family: 'JetBrains Mono', monospace;
      font-size: 11px;
      color: var(--neon-dim);
      letter-spacing: 2px;
      text-transform: uppercase;
      margin-bottom: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .section-label::before,
    .section-label::after {
      content: '';
      width: 40px; height: 1px;
      background: linear-gradient(90deg, transparent, rgba(0,255,135,0.4));
    }
    .section-label::after { transform: scaleX(-1); }

    .section-title {
      font-family: 'Syne', sans-serif;
      font-size: clamp(30px, 4vw, 46px);
      font-weight: 800;
      text-align: center;
      letter-spacing: -1.5px;
      color: var(--text-1);
      margin-bottom: 16px;
    }

    .section-sub {
      text-align: center;
      font-size: 15px;
      color: var(--text-2);
      max-width: 500px;
      margin: 0 auto 60px;
      line-height: 1.7;
      font-weight: 300;
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
    }

    .feat-card {
      background: rgba(5, 16, 9, 0.7);
      border: 1px solid rgba(0,255,135,0.09);
      border-radius: 20px;
      padding: 32px 28px;
      position: relative;
      overflow: hidden;
      transition: all 0.4s var(--ease);
      backdrop-filter: blur(12px);
    }

    .feat-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(0,255,135,0.25), transparent);
      opacity: 0;
      transition: opacity 0.3s;
    }

    .feat-card:hover {
      border-color: rgba(0,255,135,0.2);
      transform: translateY(-6px);
      box-shadow: 0 20px 60px rgba(0,0,0,0.4), 0 0 40px rgba(0,255,135,0.07);
    }

    .feat-card:hover::before { opacity: 1; }

    .feat-icon {
      width: 52px; height: 52px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      margin-bottom: 20px;
      position: relative;
    }

    .fi-1 { background: rgba(0,255,135,0.08); border: 1px solid rgba(0,255,135,0.2); color: var(--neon); }
    .fi-2 { background: rgba(24,255,255,0.07); border: 1px solid rgba(24,255,255,0.18); color: var(--cyan); }
    .fi-3 { background: rgba(139,92,246,0.08); border: 1px solid rgba(139,92,246,0.2); color: #A78BFA; }
    .fi-4 { background: rgba(245,158,11,0.07); border: 1px solid rgba(245,158,11,0.18); color: #FCD34D; }
    .fi-5 { background: rgba(59,130,246,0.07); border: 1px solid rgba(59,130,246,0.18); color: #60A5FA; }
    .fi-6 { background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2); color: #34D399; }

    .feat-title {
      font-family: 'Syne', sans-serif;
      font-size: 17px;
      font-weight: 700;
      color: var(--text-1);
      margin-bottom: 10px;
      letter-spacing: -0.3px;
    }

    .feat-body {
      font-size: 13.5px;
      color: var(--text-2);
      line-height: 1.7;
      font-weight: 300;
    }

    /* ─── CTA Strip ─── */
    .cta-strip {
      position: relative;
      z-index: 10;
      padding: 60px 24px 120px;
      text-align: center;
    }

    .cta-box {
      max-width: 700px;
      margin: 0 auto;
      background: rgba(0,255,135,0.04);
      border: 1px solid rgba(0,255,135,0.14);
      border-radius: 28px;
      padding: 60px 48px;
      position: relative;
      overflow: hidden;
      backdrop-filter: blur(20px);
    }

    .cta-box::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(0,255,135,0.5), transparent);
    }

    .cta-glow {
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      width: 400px; height: 200px;
      background: radial-gradient(ellipse, rgba(0,255,135,0.08) 0%, transparent 70%);
      pointer-events: none;
    }

    .cta-title {
      font-family: 'Syne', sans-serif;
      font-size: clamp(26px, 4vw, 40px);
      font-weight: 800;
      letter-spacing: -1px;
      color: var(--text-1);
      margin-bottom: 14px;
    }

    .cta-sub {
      font-size: 15px;
      color: var(--text-2);
      margin-bottom: 36px;
      font-weight: 300;
      line-height: 1.6;
    }

    .cta-buttons {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 14px;
      flex-wrap: wrap;
    }

    /* ─── Footer ─── */
    .footer {
      position: relative;
      z-index: 10;
      border-top: 1px solid rgba(0,255,135,0.07);
      padding: 28px 48px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 14px;
    }

    .footer-brand {
      font-family: 'Syne', sans-serif;
      font-size: 16px;
      font-weight: 700;
      background: linear-gradient(135deg, #fff, var(--neon));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .footer-text {
      font-size: 12px;
      color: var(--text-3);
      font-family: 'JetBrains Mono', monospace;
    }

    .footer-links {
      display: flex;
      gap: 20px;
    }

    .footer-links a {
      font-size: 12px;
      color: var(--text-3);
      text-decoration: none;
      transition: color 0.2s;
    }

    .footer-links a:hover { color: var(--neon-dim); }

    @media (max-width: 600px) {
      .footer { padding: 24px 20px; flex-direction: column; align-items: center; text-align: center; }
      .cta-box { padding: 40px 24px; }
    }

    /* ─── Scroll reveal ─── */
    .reveal {
      opacity: 0;
      transform: translateY(30px);
      transition: opacity 0.7s var(--ease), transform 0.7s var(--ease);
    }
    .reveal.visible {
      opacity: 1;
      transform: translateY(0);
    }
  </style>
</head>
<body>

  <!-- Background -->
  <div class="bg-scene">
    <div class="bg-gradient"></div>
    <div class="bg-grid"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
  </div>

  <!-- Particle Canvas -->
  <canvas id="particleCanvas"></canvas>

  <!-- Navbar -->
  <nav class="navbar">
    <a href="landing.html" class="nav-logo">
      <div class="nav-logo-icon"><i class="fas fa-shield-halved"></i></div>
      <span class="nav-logo-text">Senvix</span>
    </a>
    <ul class="nav-links">
      <li><a href="#features">Features</a></li>
      <li><a href="#security">Security</a></li>
      <li><a href="index.php">Dashboard</a></li>
    </ul>
    <div class="nav-cta">
      <a href="index.php" class="btn-nav-login">Sign In</a>
      <a href="register.php" class="btn-nav-signup">Get Started</a>
    </div>
  </nav>

  <!-- ═══ HERO ═══ -->
  <section class="hero">
    <div class="hero-inner">

      <!-- Badge -->
      <div class="hero-badge">
        <span class="badge-dot"></span>
        AES-256-CBC · BCRYPT · Zero-Knowledge
      </div>

      <!-- Shield Icon -->
      <div class="hero-icon-wrap">
        <div class="hero-icon-ring-2"></div>
        <div class="hero-icon-ring"></div>
        <div class="hero-icon-core">
          <i class="fas fa-shield-halved"></i>
        </div>
      </div>

      <!-- Title -->
      <h1 class="hero-title">
        <span class="title-white">Secure Your</span><br>
        <span class="title-neon">Digital World</span>
      </h1>

      <!-- Subtitle -->
      <p class="hero-sub">
        Advanced password security platform designed to protect your digital identity with modern encryption and intelligent security tools.
      </p>

      <!-- CTA Buttons -->
      <div class="hero-cta">
        <a href="index.php" class="btn-primary-hero">
          <i class="fas fa-rocket"></i>
          Get Started Free
        </a>
        <a href="#features" class="btn-secondary-hero">
          <i class="fas fa-play-circle"></i>
          Learn More
        </a>
      </div>

      <!-- Stats -->
      <div class="hero-stats">
        <div class="stat-pill">
          <div class="stat-num">256</div>
          <div class="stat-lbl">AES Bits</div>
        </div>
        <div class="stat-pill">
          <div class="stat-num">0</div>
          <div class="stat-lbl">Plaintext Stored</div>
        </div>
        <div class="stat-pill">
          <div class="stat-num">∞</div>
          <div class="stat-lbl">Vault Entries</div>
        </div>
        <div class="stat-pill">
          <div class="stat-num">100%</div>
          <div class="stat-lbl">Encrypted</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ═══ MOCK DASHBOARD PANEL ═══ -->
  <div class="hero-panel-wrap">
    <div class="glass-panel">
      <div class="panel-bar">
        <div class="panel-dot dot-red"></div>
        <div class="panel-dot dot-yel"></div>
        <div class="panel-dot dot-grn"></div>
        <div class="panel-url">🔒 <span>senvix.app/dashboard</span></div>
        <div style="width:60px"></div>
      </div>
      <div class="panel-body">
        <div class="mini-card">
          <div class="mini-icon"><i class="fas fa-shield-check"></i></div>
          <div class="mini-card-label">Vault Status</div>
          <div class="mini-card-value"><span class="tag-green">● Active</span></div>
          <div class="mini-card-sub">Authenticated &amp; encrypted</div>
        </div>
        <div class="mini-card">
          <div class="mini-icon"><i class="fas fa-key"></i></div>
          <div class="mini-card-label">Saved Passwords</div>
          <div class="mini-card-value" style="color:var(--neon);filter:drop-shadow(0 0 10px rgba(0,255,135,0.4))">24</div>
          <div class="mini-card-sub">AES-256-CBC encrypted</div>
        </div>
        <div class="vault-preview">
          <div class="vault-preview-header">
            <span>Password Vault</span>
            <span class="tag-green">● Encrypted</span>
          </div>
          <div class="vault-row">
            <div class="vault-row-icon vi-blue"><i class="fab fa-google"></i></div>
            <div class="vault-row-label">Google Account</div>
            <div class="vault-row-pw">••••••••••••</div>
            <div class="str-chip str-strong">Strong</div>
          </div>
          <div class="vault-row">
            <div class="vault-row-icon vi-red"><i class="fab fa-youtube"></i></div>
            <div class="vault-row-label">YouTube Studio</div>
            <div class="vault-row-pw">••••••••</div>
            <div class="str-chip str-fair">Fair</div>
          </div>
          <div class="vault-row">
            <div class="vault-row-icon vi-yel"><i class="fab fa-github"></i></div>
            <div class="vault-row-label">GitHub Enterprise</div>
            <div class="vault-row-pw">••••••••••••••</div>
            <div class="str-chip str-strong">Excellent</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══ FEATURES ═══ -->
  <section class="features" id="features">
    <div class="section-label">Why Senvix</div>
    <h2 class="section-title">Built for the modern threat landscape</h2>
    <p class="section-sub">Every feature engineered with enterprise-grade security principles and zero-compromise encryption.</p>

    <div class="features-grid">
      <div class="feat-card reveal">
        <div class="feat-icon fi-1"><i class="fas fa-lock"></i></div>
        <div class="feat-title">AES-256-CBC Encryption</div>
        <div class="feat-body">Military-grade symmetric encryption ensures your passwords are unreadable even if storage is compromised.</div>
      </div>
      <div class="feat-card reveal">
        <div class="feat-icon fi-2"><i class="fas fa-chart-line"></i></div>
        <div class="feat-title">Live Strength Analysis</div>
        <div class="feat-body">Real-time entropy calculation and pattern detection to score your passwords with actionable recommendations.</div>
      </div>
      <div class="feat-card reveal">
        <div class="feat-icon fi-3"><i class="fas fa-wand-magic-sparkles"></i></div>
        <div class="feat-title">Smart Password Generator</div>
        <div class="feat-body">Cryptographically secure random generation using Web Crypto API — never predictable, always unique.</div>
      </div>
      <div class="feat-card reveal">
        <div class="feat-icon fi-4"><i class="fas fa-bolt"></i></div>
        <div class="feat-title">Instant Vault Search</div>
        <div class="feat-body">Find any saved credential in milliseconds with full-text search across your entire encrypted vault.</div>
      </div>
      <div class="feat-card reveal">
        <div class="feat-icon fi-5"><i class="fas fa-shield-halved"></i></div>
        <div class="feat-title">Session Security</div>
        <div class="feat-body">HTTPOnly cookies, session fixation prevention, and automatic regeneration on every authenticated action.</div>
      </div>
      <div class="feat-card reveal">
        <div class="feat-icon fi-6"><i class="fas fa-database"></i></div>
        <div class="feat-title">Zero-Knowledge Storage</div>
        <div class="feat-body">Passwords are encrypted before reaching the database. Your plaintext never touches our storage layer.</div>
      </div>
    </div>
  </section>

  <!-- ═══ CTA STRIP ═══ -->
  <section class="cta-strip" id="security">
    <div class="cta-box reveal">
      <div class="cta-glow"></div>
      <h2 class="cta-title">Start securing your passwords today</h2>
      <p class="cta-sub">Join Senvix and protect your digital identity with enterprise-grade security — completely free.</p>
      <div class="cta-buttons">
        <a href="register.php" class="btn-primary-hero">
          <i class="fas fa-user-plus"></i>
          Create Free Account
        </a>
        <a href="index.php" class="btn-secondary-hero">
          <i class="fas fa-sign-in-alt"></i>
          Sign In
        </a>
      </div>
    </div>
  </section>

  <!-- ═══ FOOTER ═══ -->
  <footer class="footer">
    <div class="footer-brand">Senvix</div>
    <div class="footer-text">© 2025 Senvix · AES-256 · Zero-Knowledge</div>
    <div class="footer-links">
      <a href="#">Privacy</a>
      <a href="#">Terms</a>
      <a href="index.php">Login</a>
    </div>
  </footer>

  <script>
    /* ─── Particles ─── */
    const canvas = document.getElementById('particleCanvas');
    const ctx    = canvas.getContext('2d');
    let W, H, particles = [];

    function resize() {
      W = canvas.width  = window.innerWidth;
      H = canvas.height = window.innerHeight;
    }

    function Particle() {
      this.x  = Math.random() * W;
      this.y  = Math.random() * H;
      this.r  = Math.random() * 1.5 + 0.3;
      this.vx = (Math.random() - 0.5) * 0.25;
      this.vy = (Math.random() - 0.5) * 0.25;
      this.a  = Math.random() * 0.5 + 0.1;
      this.color = Math.random() > 0.65
        ? `rgba(0,255,135,${this.a})`
        : `rgba(24,255,255,${this.a * 0.5})`;
    }

    Particle.prototype.update = function() {
      this.x += this.vx;
      this.y += this.vy;
      if (this.x < 0) this.x = W;
      if (this.x > W) this.x = 0;
      if (this.y < 0) this.y = H;
      if (this.y > H) this.y = 0;
    };

    Particle.prototype.draw = function() {
      ctx.beginPath();
      ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2);
      ctx.fillStyle = this.color;
      ctx.fill();
    };

    function initParticles() {
      particles = [];
      const count = Math.min(Math.floor((W * H) / 12000), 80);
      for (let i = 0; i < count; i++) particles.push(new Particle());
    }

    function drawConnections() {
      for (let i = 0; i < particles.length; i++) {
        for (let j = i + 1; j < particles.length; j++) {
          const dx   = particles[i].x - particles[j].x;
          const dy   = particles[i].y - particles[j].y;
          const dist = Math.sqrt(dx * dx + dy * dy);
          if (dist < 110) {
            ctx.beginPath();
            ctx.moveTo(particles[i].x, particles[i].y);
            ctx.lineTo(particles[j].x, particles[j].y);
            ctx.strokeStyle = `rgba(0,255,135,${0.06 * (1 - dist / 110)})`;
            ctx.lineWidth = 0.5;
            ctx.stroke();
          }
        }
      }
    }

    function animate() {
      ctx.clearRect(0, 0, W, H);
      drawConnections();
      particles.forEach(p => { p.update(); p.draw(); });
      requestAnimationFrame(animate);
    }

    window.addEventListener('resize', () => { resize(); initParticles(); });
    resize(); initParticles(); animate();

    /* ─── Scroll Reveal ─── */
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry, i) => {
        if (entry.isIntersecting) {
          setTimeout(() => entry.target.classList.add('visible'), i * 90);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    /* ─── Smooth scroll for Learn More ─── */
    document.querySelectorAll('a[href^="#"]').forEach(a => {
      a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) {
          e.preventDefault();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });

    /* ─── Navbar scroll glass ─── */
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 40) {
        navbar.style.background = 'rgba(2, 10, 5, 0.92)';
        navbar.style.borderBottomColor = 'rgba(0,255,135,0.13)';
      } else {
        navbar.style.background = 'rgba(2, 10, 5, 0.7)';
        navbar.style.borderBottomColor = 'rgba(0,255,135,0.08)';
      }
    });
  </script>
</body>
</html>