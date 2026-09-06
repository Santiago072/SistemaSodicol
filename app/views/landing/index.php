<?php
/**
 * Landing Page Oficial e Institucional - SODICOL ZOMAC S.A.S
 * Sistema de Gestión de Diseño Arquitectónico, Estaciones Corporativas & Cotizaciones
 * 100% Autónomo e Independiente de Fotos Externas (Gráficos Vectoriales y Blueprint de Diseño)
 */
$baseUrl = defined('BASE_URL') ? BASE_URL : '/SistemaSodicol/';
$tokenCsrf = $csrf_token ?? '';
?>
<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SODICOL ZOMAC S.A.S | Soluciones Logísticas de Diseño & Arquitectura Corporativa</title>
    <meta name="description" content="Diseño técnico, ingeniería en aglomerados Pelikano RH, carpintería modular para oficinas de alto rendimiento y sistema automatizado de cotizaciones.">

    <!-- Tipografías Premium: Playfair Display + Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= $baseUrl ?>logo/favicon.png">
    <link rel="shortcut icon" href="<?= $baseUrl ?>logo/favicon.ico">

    <style>
        :root {
            --wood-dark: #1b1714;
            --wood-charcoal: #26201c;
            --wood-primary: #8b5a2b;
            --wood-accent: #c48b4e;
            --wood-gold: #d4a373;
            --wood-light: #faf8f5;
            --wood-card: #ffffff;
            --wood-border: #ede4d8;
            --wood-border-hover: #d5c3b0;
            --text-main: #231f1d;
            --text-muted: #665f57;
            --text-light: #948b81;
            --shadow-subtle: 0 4px 14px rgba(35, 31, 29, 0.04);
            --shadow-card: 0 16px 36px -6px rgba(35, 31, 29, 0.08);
            --shadow-wood: 0 16px 34px -4px rgba(139, 90, 43, 0.25);
            --transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--wood-light);
            color: var(--text-main);
            line-height: 1.6;
            overflow-x: hidden;
            background-image: 
                radial-gradient(at 100% 0%, rgba(212, 163, 115, 0.08) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(139, 90, 43, 0.06) 0px, transparent 50%);
            background-attachment: fixed;
        }

        h1, h2, h3, .font-serif {
            font-family: 'Playfair Display', serif;
        }

        /* Top Bar de Régimen ZOMAC */
        .top-announce-bar {
            background: linear-gradient(90deg, #1b1714 0%, #2c231e 50%, #1b1714 100%);
            color: #d4a373;
            font-size: 0.815rem;
            padding: 9px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(212, 163, 115, 0.22);
            letter-spacing: 0.4px;
        }

        .announce-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .announce-badge {
            background: rgba(212, 163, 115, 0.2);
            color: #f7e6d2;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 0.72rem;
            text-transform: uppercase;
            border: 1px solid rgba(212, 163, 115, 0.35);
        }

        /* Navbar Flotante */
        .navbar-luxury {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--wood-border);
            padding: 14px 0;
            transition: var(--transition);
        }

        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: var(--text-main);
        }

        .brand-logo-img {
            height: 50px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 5px rgba(0,0,0,0.08));
        }

        .brand-texts {
            display: flex;
            flex-direction: column;
        }

        .brand-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #1a1715;
            line-height: 1.15;
        }

        .brand-title span {
            color: var(--wood-accent);
        }

        .brand-subtitle {
            font-size: 0.72rem;
            color: var(--text-muted);
            letter-spacing: 1.2px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
            list-style: none;
        }

        .nav-link-item {
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link-item:hover {
            color: var(--wood-accent);
        }

        .btn-portal-login {
            background: linear-gradient(135deg, #26201c 0%, #1b1714 100%);
            color: #f7ede2;
            border: 1px solid rgba(212, 163, 115, 0.4);
            padding: 10px 22px;
            border-radius: 99px;
            font-size: 0.88rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(30, 27, 24, 0.15);
            transition: var(--transition);
            cursor: pointer;
        }

        .btn-portal-login:hover {
            background: linear-gradient(135deg, #8b5a2b 0%, #6d421a 100%);
            color: #ffffff;
            border-color: #d4a373;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(139, 90, 43, 0.28);
        }

        /* Hero Section */
        .hero-section {
            padding: 68px 0 80px 0;
            position: relative;
        }

        .hero-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 32px;
            display: grid;
            grid-template-columns: 1.12fr 0.88fr;
            gap: 56px;
            align-items: center;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            background: #f4ece2;
            color: #8b5a2b;
            padding: 6px 15px;
            border-radius: 99px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-bottom: 22px;
            border: 1px solid rgba(196, 139, 78, 0.35);
        }

        .hero-title {
            font-size: 3.35rem;
            line-height: 1.14;
            font-weight: 700;
            color: #1c1815;
            margin-bottom: 22px;
            letter-spacing: -0.5px;
        }

        .hero-title .highlight {
            color: var(--wood-primary);
            position: relative;
            display: inline-block;
        }

        .hero-title .highlight::after {
            content: '';
            position: absolute;
            bottom: 4px;
            left: 0;
            width: 100%;
            height: 8px;
            background: rgba(212, 163, 115, 0.3);
            z-index: -1;
            border-radius: 4px;
        }

        .hero-desc {
            font-size: 1.05rem;
            color: var(--text-muted);
            line-height: 1.75;
            margin-bottom: 34px;
            max-width: 580px;
        }

        .hero-cta-group {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 44px;
        }

        .btn-gold-action {
            background: linear-gradient(135deg, #c48b4e 0%, #9e6429 100%);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: none;
            box-shadow: var(--shadow-wood);
            transition: var(--transition);
            cursor: pointer;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .btn-gold-action:hover {
            background: linear-gradient(135deg, #d4a373 0%, #b07335 100%);
            transform: translateY(-2px);
            box-shadow: 0 18px 34px rgba(139, 90, 43, 0.3);
        }

        .btn-outline-wood {
            background: #ffffff;
            color: #26201c;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 14px 26px;
            border-radius: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid var(--wood-border);
            box-shadow: var(--shadow-subtle);
            transition: var(--transition);
        }

        .btn-outline-wood:hover {
            border-color: var(--wood-accent);
            background: #fdfaf6;
            color: var(--wood-primary);
            transform: translateY(-2px);
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding-top: 26px;
            border-top: 1px solid var(--wood-border);
        }

        .stat-card-mini h4 {
            font-family: 'Playfair Display', serif;
            font-size: 1.85rem;
            font-weight: 700;
            color: #1b1714;
            line-height: 1.1;
        }

        .stat-card-mini p {
            font-size: 0.76rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        /* ══ CONSOLA DERECHA DEL HERO: BLUEPRINT ARQUITECTÓNICO & PLANO TÉCNICO (CERO FOTOS) ══ */
        .showcase-stage {
            position: relative;
        }

        .showcase-stage::before {
            content: '';
            position: absolute;
            inset: -14px;
            background: linear-gradient(135deg, rgba(212, 163, 115, 0.22) 0%, rgba(139, 90, 43, 0.1) 100%);
            border-radius: 30px;
            filter: blur(24px);
            z-index: 1;
        }

        .blueprint-cockpit {
            position: relative;
            z-index: 2;
            background: #1e1916;
            border-radius: 26px;
            border: 1px solid rgba(212, 163, 115, 0.28);
            box-shadow: 0 24px 50px -10px rgba(27, 23, 20, 0.4);
            overflow: hidden;
            color: #f7ede2;
        }

        .blueprint-header {
            background: #14110e;
            padding: 16px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(212, 163, 115, 0.2);
        }

        .blueprint-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .blueprint-title-top {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            font-weight: 600;
            color: #d4a373;
            letter-spacing: 0.4px;
        }

        .blueprint-badge {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            border: 1px solid rgba(74, 222, 128, 0.4);
            padding: 3px 9px;
            border-radius: 99px;
            font-size: 0.7rem;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .blueprint-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #4ade80;
            box-shadow: 0 0 8px #4ade80;
        }

        .blueprint-body {
            padding: 22px;
            background: linear-gradient(180deg, #1e1916 0%, #171310 100%);
        }

        /* Plano Isométrico Vectorial SVG (No depende de fotos) */
        .blueprint-canvas-box {
            background: #100d0b;
            border: 1px solid rgba(212, 163, 115, 0.25);
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 18px;
            position: relative;
            background-image: 
                linear-gradient(to right, rgba(212, 163, 115, 0.08) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(212, 163, 115, 0.08) 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .blueprint-svg {
            width: 100%;
            height: 180px;
            display: block;
        }

        .blueprint-meta-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            color: #d4a373;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed rgba(212, 163, 115, 0.25);
        }

        /* Mini Monitor de Cotización Formal PDF */
        .blueprint-quote-box {
            background: #14110f;
            border-radius: 14px;
            border: 1px solid rgba(212, 163, 115, 0.2);
            padding: 16px;
        }

        .quote-header-mini {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px dashed rgba(212, 163, 115, 0.2);
            font-size: 0.78rem;
            font-weight: 700;
            color: #e5d5c5;
            text-transform: uppercase;
        }

        .quote-items-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 0.8rem;
        }

        .quote-item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #b3a497;
        }

        .quote-item-name {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #f7ede2;
        }

        .quote-item-name i {
            color: var(--wood-gold);
        }

        .quote-total-row {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid rgba(212, 163, 115, 0.25);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 700;
        }

        .quote-total-row span:first-child {
            font-size: 0.82rem;
            color: #e5d5c5;
        }

        .quote-total-val {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
            color: #d4a373;
        }

        /* ══ SECCIÓN DE LÍNEAS DE ARQUITECTURA (TARJETAS EDITORIALES SIN FOTOS ROTAS) ══ */
        .solutions-section {
            padding: 90px 0;
            background: #ffffff;
            border-top: 1px solid var(--wood-border);
            border-bottom: 1px solid var(--wood-border);
        }

        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 56px auto;
        }

        .section-tag {
            display: inline-block;
            color: var(--wood-accent);
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .section-title {
            font-size: 2.35rem;
            font-weight: 700;
            color: #1b1714;
            margin-bottom: 14px;
        }

        .section-desc {
            font-size: 0.98rem;
            color: var(--text-muted);
            line-height: 1.65;
        }

        .solutions-grid {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 32px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 32px;
        }

        .arch-card {
            background: var(--wood-light);
            border-radius: 22px;
            border: 1px solid var(--wood-border);
            padding: 34px 28px;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .arch-card:hover {
            transform: translateY(-6px);
            border-color: var(--wood-border-hover);
            box-shadow: var(--shadow-card);
            background: #ffffff;
        }

        .arch-card-icon-pill {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            background: #f4ece2;
            color: var(--wood-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 22px;
            border: 1px solid rgba(196, 139, 78, 0.25);
        }

        .arch-card h3 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1b1714;
            margin-bottom: 10px;
        }

        .arch-card p {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 22px;
            flex-grow: 1;
        }

        .arch-specs-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding-top: 18px;
            border-top: 1px solid var(--wood-border);
            font-size: 0.82rem;
            color: #3b322d;
        }

        .arch-specs-list li {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .arch-specs-list li i {
            color: var(--wood-accent);
            font-size: 0.95rem;
        }

        /* Sección de Capacidades del Sistema */
        .features-section {
            padding: 90px 0;
            background: #faf8f5;
        }

        .features-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 32px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        .feature-box {
            background: #ffffff;
            border: 1px solid var(--wood-border);
            border-radius: 18px;
            padding: 28px 24px;
            transition: var(--transition);
        }

        .feature-box:hover {
            transform: translateY(-4px);
            border-color: var(--wood-accent);
            box-shadow: var(--shadow-subtle);
        }

        .feature-icon-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: #f7efe4;
            color: var(--wood-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            margin-bottom: 18px;
            border: 1px solid rgba(196, 139, 78, 0.25);
        }

        .feature-box h4 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1b1714;
            margin-bottom: 8px;
        }

        .feature-box p {
            font-size: 0.84rem;
            color: var(--text-muted);
            line-height: 1.55;
        }

        /* Banner CTA */
        .cta-luxury-banner {
            max-width: 1280px;
            margin: 0 auto 90px auto;
            padding: 0 32px;
        }

        .cta-inner-card {
            background: linear-gradient(135deg, #221b18 0%, #342821 50%, #1b1512 100%);
            border-radius: 26px;
            padding: 60px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #ffffff;
            box-shadow: 0 24px 50px rgba(34, 27, 24, 0.25);
            border: 1px solid rgba(212, 163, 115, 0.3);
            position: relative;
            overflow: hidden;
        }

        .cta-content-left h2 {
            font-size: 2.3rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: #f7ede2;
        }

        .cta-content-left p {
            font-size: 1rem;
            color: #d8cbbf;
            max-width: 580px;
            line-height: 1.6;
        }

        /* Footer */
        .footer-luxury {
            background: #14110f;
            color: #a89e95;
            padding: 60px 0 30px 0;
            border-top: 1px solid #28211d;
        }

        .footer-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 32px;
        }

        .footer-top-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 48px;
        }

        .footer-brand h3 {
            font-family: 'Playfair Display', serif;
            color: #f7ede2;
            font-size: 1.35rem;
            margin-bottom: 12px;
        }

        .footer-brand p {
            font-size: 0.85rem;
            line-height: 1.65;
            max-width: 320px;
        }

        .footer-col h4 {
            color: #f7ede2;
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
            font-size: 0.84rem;
        }

        .footer-col ul a {
            color: #a89e95;
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-col ul a:hover {
            color: #d4a373;
            padding-left: 4px;
        }

        .footer-bottom {
            padding-top: 28px;
            border-top: 1px solid #231c18;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.78rem;
            color: #7b7169;
        }

        /* Modal Login */
        .login-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(18, 14, 12, 0.78);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .login-modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .login-modal-box {
            background: #ffffff;
            border-radius: 24px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.35);
            border: 1px solid var(--wood-border);
            overflow: hidden;
            transform: scale(0.94);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .login-modal-overlay.active .login-modal-box {
            transform: scale(1);
        }

        .modal-header-luxury {
            background: linear-gradient(135deg, #221b18 0%, #342821 100%);
            padding: 24px 28px;
            color: #ffffff;
            position: relative;
            text-align: center;
        }

        .modal-close-btn {
            position: absolute;
            top: 16px;
            right: 18px;
            background: transparent;
            border: none;
            color: #d4a373;
            font-size: 1.4rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .modal-close-btn:hover {
            color: #ffffff;
            transform: rotate(90deg);
        }

        .modal-logo-center {
            width: 58px;
            height: 58px;
            background: #ffffff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            padding: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .modal-logo-center img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .modal-body-form {
            padding: 28px;
        }

        .form-group-modal {
            margin-bottom: 18px;
        }

        .form-label-modal {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #3b322d;
            margin-bottom: 6px;
        }

        .form-input-modal {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid var(--wood-border);
            background: #faf8f5;
            font-size: 0.9rem;
            color: #231f1d;
            outline: none;
            transition: var(--transition);
            font-family: inherit;
        }

        .form-input-modal:focus {
            border-color: var(--wood-accent);
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(196, 139, 78, 0.15);
        }

        .btn-submit-modal {
            width: 100%;
            background: linear-gradient(135deg, #c48b4e 0%, #9e6429 100%);
            color: #ffffff;
            border: none;
            padding: 13px;
            border-radius: 10px;
            font-size: 0.94rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: var(--transition);
            box-shadow: var(--shadow-wood);
            margin-top: 24px;
        }

        .btn-submit-modal:hover {
            background: linear-gradient(135deg, #d4a373 0%, #b07335 100%);
            transform: translateY(-1px);
        }

        @media (max-width: 992px) {
            .hero-container, .solutions-grid, .cta-inner-card, .footer-top-grid {
                grid-template-columns: 1fr;
            }
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>

    <!-- Top Notice Bar -->
    <div class="top-announce-bar">
        <div class="announce-item">
            <span class="announce-badge">ZOMAC S.A.S</span>
            <span>Diseño, Fabricación y Montaje Corporativo Certificado</span>
        </div>
        <div class="announce-item">
            <i class="bi bi-shield-check"></i>
            <span>Ingeniería en Pelikano RH & Herrajes de Alta Resistencia</span>
        </div>
    </div>

    <!-- Barra de Navegación -->
    <nav class="navbar-luxury">
        <div class="nav-container">
            <a href="<?= $baseUrl ?>" class="brand-link">
                <img src="<?= $baseUrl ?>logo/logo.png" alt="Sodicol Logo" class="brand-logo-img">
                <div class="brand-texts">
                    <span class="brand-title">SODICOL <span>ZOMAC</span></span>
                    <span class="brand-subtitle">Soluciones Logísticas de Diseño</span>
                </div>
            </a>

            <ul class="nav-links">
                <li><a href="#arquitectura" class="nav-link-item"><i class="bi bi-building"></i> Arquitectura</a></li>
                <li><a href="#estaciones" class="nav-link-item"><i class="bi bi-sliders"></i> Estaciones</a></li>
                <li><a href="#cotizador" class="nav-link-item"><i class="bi bi-file-earmark-pdf"></i> Cotizaciones</a></li>
                <li><a href="#calidad" class="nav-link-item"><i class="bi bi-award"></i> Calidad RH</a></li>
            </ul>

            <div class="nav-actions">
                <button class="btn-portal-login" onclick="openLoginModal()">
                    <i class="bi bi-person-lock"></i>
                    <span>Portal de Gestión</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-container">
            
            <!-- Columna Izquierda: Información de Valor -->
            <div class="hero-content-left">
                <div class="hero-eyebrow">
                    <i class="bi bi-gem"></i>
                    Arquitectura Corporativa & Mobiliario
                </div>

                <h1 class="hero-title">
                    Diseño y ergonomía para <span class="highlight">espacios de alta productividad</span>
                </h1>

                <p class="hero-desc">
                    SODICOL ZOMAC S.A.S integra ingeniería en aglomerados PELIKANO RH, cantos termofundidos y herrajes de alta especificación para crear estaciones ejecutivas, divisiones modulares y mobiliario corporativo a la medida exacta de su organización.
                </p>

                <div class="hero-cta-group">
                    <button class="btn-gold-action" onclick="openLoginModal()">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Acceder al Sistema</span>
                    </button>
                    <a href="#estaciones" class="btn-outline-wood">
                        <i class="bi bi-layers"></i>
                        <span>Líneas de Diseño</span>
                    </a>
                </div>

                <div class="hero-stats">
                    <div class="stat-card-mini">
                        <h4>100%</h4>
                        <p>Pelikano RH Certificado</p>
                    </div>
                    <div class="stat-card-mini">
                        <h4>45 Kg</h4>
                        <p>Capacidad Rieles Zinc</p>
                    </div>
                    <div class="stat-card-mini">
                        <h4>PDF</h4>
                        <p>Cotizador Automático</p>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: BLUEPRINT TÉCNICO ISOMÉTRICO (Cero dependencia de fotos) -->
            <div class="showcase-stage">
                <div class="blueprint-cockpit">
                    
                    <div class="blueprint-header">
                        <div class="blueprint-header-left">
                            <i class="bi bi-rulers" style="color: #d4a373;"></i>
                            <span class="blueprint-title-top">PLAN_SPECS_ESTACION_2380.CAD</span>
                        </div>
                        <div class="blueprint-badge">PLANTA ACTIVA</div>
                    </div>

                    <div class="blueprint-body">
                        
                        <!-- Gráfico Vectorial Isométrico Elegante -->
                        <div class="blueprint-canvas-box">
                            <svg class="blueprint-svg" viewBox="0 0 500 240" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <!-- Líneas de Cota y Ejes Arquitectónicos -->
                                <line x1="40" y1="210" x2="460" y2="210" stroke="#d4a373" stroke-width="1" stroke-dasharray="4 4" opacity="0.4"/>
                                <line x1="40" y1="30" x2="40" y2="210" stroke="#d4a373" stroke-width="1" stroke-dasharray="4 4" opacity="0.4"/>
                                <text x="465" y="214" fill="#d4a373" font-family="JetBrains Mono" font-size="10" opacity="0.7">X: 2380 mm</text>
                                <text x="25" y="25" fill="#d4a373" font-family="JetBrains Mono" font-size="10" opacity="0.7">Y: 960 mm</text>

                                <!-- Tablero Superior Isométrico (Fresno RH) -->
                                <polygon points="120,70 360,50 430,95 190,120" fill="rgba(196, 139, 78, 0.25)" stroke="#d4a373" stroke-width="2"/>
                                <polygon points="120,70 190,120 190,132 120,82" fill="rgba(139, 90, 43, 0.4)" stroke="#d4a373" stroke-width="1.5"/>
                                <polygon points="190,120 430,95 430,107 190,132" fill="rgba(139, 90, 43, 0.55)" stroke="#d4a373" stroke-width="1.5"/>

                                <!-- Patas y Bastidores de Apoyo Estructural -->
                                <line x1="140" y1="82" x2="140" y2="190" stroke="#c48b4e" stroke-width="2.5"/>
                                <line x1="410" y1="107" x2="410" y2="185" stroke="#c48b4e" stroke-width="2.5"/>
                                <line x1="210" y1="132" x2="210" y2="198" stroke="#c48b4e" stroke-width="2.5"/>

                                <!-- Gavetero Modular y Rieles de Extensión -->
                                <polygon points="270,125 380,113 380,185 270,200" fill="rgba(212, 163, 115, 0.15)" stroke="#d4a373" stroke-width="1.5"/>
                                <line x1="270" y1="150" x2="380" y2="138" stroke="#d4a373" stroke-width="1"/>
                                <line x1="270" y1="175" x2="380" y2="163" stroke="#d4a373" stroke-width="1"/>
                                <circle cx="325" cy="144" r="2.5" fill="#f7ede2"/>
                                <circle cx="325" cy="169" r="2.5" fill="#f7ede2"/>

                                <!-- Nodos de Ensamble y Cotas -->
                                <circle cx="190" cy="120" r="4" fill="#06b6d4" stroke="#ffffff" stroke-width="1.5"/>
                                <circle cx="430" cy="95" r="4" fill="#06b6d4" stroke="#ffffff" stroke-width="1.5"/>
                                <circle cx="360" cy="50" r="4" fill="#06b6d4" stroke="#ffffff" stroke-width="1.5"/>
                            </svg>

                            <div class="blueprint-meta-bar">
                                <span><i class="bi bi-check2-circle"></i> Ensamble Canto Rígido 33mm</span>
                                <span>Tolerancia: &plusmn;0.5mm</span>
                            </div>
                        </div>

                        <!-- Mini Monitor de Cotización PDF Oficial -->
                        <div class="blueprint-quote-box">
                            <div class="quote-header-mini">
                                <span><i class="bi bi-file-earmark-pdf-fill" style="color: #d4a373;"></i> Desglose de Cotización Automática</span>
                                <span style="color: #d4a373; font-family: 'JetBrains Mono';">#2026-SOD</span>
                            </div>

                            <ul class="quote-items-list">
                                <li class="quote-item-row">
                                    <span class="quote-item-name"><i class="bi bi-check-circle-fill"></i> Estación Ejecutiva 2380 RH Fresno</span>
                                    <span style="font-weight: 600; color: #f7ede2;">$4.280.000</span>
                                </li>
                                <li class="quote-item-row">
                                    <span class="quote-item-name"><i class="bi bi-check-circle-fill"></i> Escritorio en L 1300 + Riel Zinc Pesado</span>
                                    <span style="font-weight: 600; color: #f7ede2;">$3.500.000</span>
                                </li>
                                <li class="quote-item-row">
                                    <span class="quote-item-name"><i class="bi bi-check-circle-fill"></i> Módulo Aéreo 800 Brazo Neumático</span>
                                    <span style="font-weight: 600; color: #f7ede2;">$1.420.000</span>
                                </li>
                            </ul>

                            <div class="quote-total-row">
                                <span>Total Liquidado (IVA 19% Incluido)</span>
                                <span class="quote-total-val">$9.200.000 COP</span>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- ══ SECCIÓN 1: ARQUITECTURA CORPORATIVA & PLANIMETRÍA CAD (id="arquitectura") ══ -->
    <section class="solutions-section" id="arquitectura" style="background: #ffffff;">
        <div class="section-header">
            <span class="section-tag"><i class="bi bi-compass"></i> Ingeniería Espacial</span>
            <h2 class="section-title">Arquitectura Corporativa & Diseño a Medida</h2>
            <p class="section-desc">Planificación milimétrica de espacios productivos. Transformamos requerimientos operativos en planos técnicos CAD y montajes modulares de alta durabilidad.</p>
        </div>

        <div class="solutions-grid">
            
            <div class="arch-card">
                <div class="arch-card-icon-pill" style="background: #fdf6ec; color: #b45309;">
                    <i class="bi bi-rulers"></i>
                </div>
                <h3>Levantamiento & Planos CAD</h3>
                <p>Modelado técnico previo con cálculo de circulación ergonómica, accesibilidad, canaletas de cableado estructurado y aprovechamiento óptimo de metros cuadrados.</p>
                <ul class="arch-specs-list">
                    <li><i class="bi bi-check2-circle"></i> Tolerancia milimétrica &plusmn;0.5mm</li>
                    <li><i class="bi bi-check2-circle"></i> Planos ejecutivos para aprobación de obra</li>
                    <li><i class="bi bi-check2-circle"></i> Integración con redes eléctricas y de voz/datos</li>
                </ul>
            </div>

            <div class="arch-card">
                <div class="arch-card-icon-pill" style="background: #f4ece2; color: #8b5a2b;">
                    <i class="bi bi-easel2"></i>
                </div>
                <h3>Marcos & Carpintería Arquitectónica</h3>
                <p>Piezas exclusivas con corte a inglete 45° para recepciones ejecutivas, paneles divisorios acústicos y revestimientos en madera noble con ensamble invisible.</p>
                <ul class="arch-specs-list">
                    <li><i class="bi bi-check2-circle"></i> Terminados en laca poliuretánica y texturas RH</li>
                    <li><i class="bi bi-check2-circle"></i> Aislamiento acústico en salas de juntas</li>
                    <li><i class="bi bi-check2-circle"></i> Montaje profesional garantizado en sitio</li>
                </ul>
            </div>

            <div class="arch-card">
                <div class="arch-card-icon-pill" style="background: #ecfdf5; color: #047857;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h3>Especificación Pelikano RH</h3>
                <p>Tableros de partículas con resina melamínica resistente a la humedad y hongos, garantizando estabilidad dimensional en ambientes climatizados de oficina.</p>
                <ul class="arch-specs-list">
                    <li><i class="bi bi-check2-circle"></i> Cantos rígidos PVC 19mm a 33mm termofundidos</li>
                    <li><i class="bi bi-check2-circle"></i> Superficies antibacteriales de fácil asepsia</li>
                    <li><i class="bi bi-check2-circle"></i> Respaldo institucional Sodicol Zomac S.A.S</li>
                </ul>
            </div>

        </div>
    </section>

    <!-- ══ SECCIÓN 2: ESTACIONES DE TRABAJO & MOBILIARIO OPERATIVO (id="estaciones") ══ -->
    <section class="solutions-section" id="estaciones" style="background: #faf8f5; border-top: none;">
        <div class="section-header">
            <span class="section-tag"><i class="bi bi-laptop"></i> Líneas Operativas</span>
            <h2 class="section-title">Estaciones Ergonómicas & Módulos</h2>
            <p class="section-desc">Puestos de trabajo configurables diseñados para soportar jornadas de alta intensidad con confort biomecánico y almacenamiento inteligente.</p>
        </div>

        <div class="solutions-grid">
            
            <!-- Estaciones en L -->
            <div class="arch-card" style="background: #ffffff;">
                <div class="arch-card-icon-pill">
                    <i class="bi bi-sliders"></i>
                </div>
                <h3>Estaciones Ejecutivas en L</h3>
                <p>Dimensiones de 1300mm a 1500mm con gavetero pedestal integrado, rieles zinc de extensión pesada (45 Kg) y estructura reforzada para monitores múltiples.</p>
                <ul class="arch-specs-list">
                    <li><i class="bi bi-shield-check"></i> Tablero principal con nariz termo-reforzada 30mm</li>
                    <li><i class="bi bi-sliders"></i> Patas de acero con niveladores independientes</li>
                    <li><i class="bi bi-lock-fill"></i> Gaveta superior con cerradura de seguridad</li>
                </ul>
            </div>

            <!-- Estación 2380 mm -->
            <div class="arch-card" style="background: #ffffff;">
                <div class="arch-card-icon-pill">
                    <i class="bi bi-grid-1x2"></i>
                </div>
                <h3>Estación Múltiple 2380 mm</h3>
                <p>Configuración lineal de 2380mm x 630mm x 960mm para equipos de ingeniería o trabajo colaborativo con divisorias centrales y pasacables continuos.</p>
                <ul class="arch-specs-list">
                    <li><i class="bi bi-shield-check"></i> Aglomerado RH fresno y blanco de 15mm</li>
                    <li><i class="bi bi-bezier2"></i> Canaleta técnica para cableado oculto</li>
                    <li><i class="bi bi-arrows-fullscreen"></i> Amplia área despejada de trabajo</li>
                </ul>
            </div>

            <!-- Módulos Aéreos -->
            <div class="arch-card" style="background: #ffffff;">
                <div class="arch-card-icon-pill">
                    <i class="bi bi-box-seam"></i>
                </div>
                <h3>Módulos Aéreos & Repisas</h3>
                <p>Estructuras murales de 800mm a 1030mm con brazos neumáticos de elevación vertical, bisagras de cierre suave y tiradores en níquel cepillado.</p>
                <ul class="arch-specs-list">
                    <li><i class="bi bi-shield-check"></i> Brazos neumáticos de apertura vertical 90°</li>
                    <li><i class="bi bi-door-closed"></i> Bisagras con freno amortiguador silencioso</li>
                    <li><i class="bi bi-check-all"></i> Anclaje mural reforzado anti-desprendimiento</li>
                </ul>
            </div>

        </div>
    </section>

    <!-- Características del Sistema Interno -->
    <section class="features-section" id="calidad">
        <div class="features-container">
            <div class="section-header">
                <span class="section-tag">Ecosistema SODICOL</span>
                <h2 class="section-title">Control Total de Producción & Cotizaciones</h2>
                <p class="section-desc">Plataforma centralizada para cotizaciones con fórmula de IVA, control de inventario de materias primas y flujo de tareas en planta.</p>
            </div>

            <div class="features-grid">
                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </div>
                    <h4>Cotizador en PDF</h4>
                    <p>Cálculo interno formal con discriminación de ítems, cálculo automático de IVA y membrete legal corporativo.</p>
                </div>

                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <i class="bi bi-kanban"></i>
                    </div>
                    <h4>Gestor de Tareas</h4>
                    <p>Monitoreo por fases: corte de aglomerado, canteado rígido, ensamble de herrajes y control final de calidad.</p>
                </div>

                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <i class="bi bi-boxes"></i>
                    </div>
                    <h4>Maestro de Módulos</h4>
                    <p>Control exacto de costos de fabricación, márgenes de utilidad comercial y especificaciones milimétricas.</p>
                </div>

                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <h4>Seguridad Robusta</h4>
                    <p>Protección contra ataques CSRF, control de rate limiting anti-fuerza bruta y sesiones con expiración por inactividad.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Banner de Contacto / Acceso -->
    <div class="cta-luxury-banner" id="cotizador">
        <div class="cta-inner-card">
            <div class="cta-content-left">
                <h2>¿Listo para amoblar su próximo proyecto corporativo?</h2>
                <p>Acceda al sistema integral para generar cotizaciones formales en PDF, consultar especificaciones técnicas o coordinar órdenes de fabricación.</p>
            </div>
            <div>
                <button class="btn-gold-action" onclick="openLoginModal()" style="padding: 15px 32px; font-size: 1rem; white-space: nowrap; flex-shrink: 0;">
                    <i class="bi bi-person-fill"></i>
                    <span>Ingresar al Portal</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-luxury">
        <div class="footer-container">
            <div class="footer-top-grid">
                <div class="footer-brand">
                    <h3>SODICOL ZOMAC S.A.S</h3>
                    <p>Soluciones logísticas de diseño, carpintería modular y mobiliario ergonómico para oficinas de alto rendimiento y entornos corporativos.</p>
                </div>
                <div class="footer-col">
                    <h4>Líneas de Diseño</h4>
                    <ul>
                        <li><a href="#estaciones">Estaciones en L</a></li>
                        <li><a href="#estaciones">Módulos Aéreos RH</a></li>
                        <li><a href="#arquitectura">Marcos y Carpintería</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Soporte Técnico</h4>
                    <ul>
                        <li><a href="javascript:void(0)" onclick="openLoginModal()">Portal Administrativo</a></li>
                        <li><a href="javascript:void(0)" onclick="openLoginModal()">Emisión de Cotizaciones</a></li>
                        <li><a href="javascript:void(0)" onclick="openLoginModal()">Seguimiento de Producción</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Identidad Corporativa</h4>
                    <ul>
                        <li><span>Régimen Especial ZOMAC</span></li>
                        <li><span>Aglomerados Pelikano RH</span></li>
                        <li><span>Herrajes Extensión Pesada</span></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <div>&copy; <?= date('Y') ?> SODICOL ZOMAC S.A.S. Todos los derechos reservados.</div>
                <div>Sistema de Gestión Integral v2.0</div>
            </div>
        </div>
    </footer>

    <!-- Modal de Inicio de Sesión -->
    <div class="login-modal-overlay" id="loginModal">
        <div class="login-modal-box">
            <div class="modal-header-luxury">
                <button class="modal-close-btn" onclick="closeLoginModal()">&times;</button>
                <div class="modal-logo-center">
                    <img src="<?= $baseUrl ?>logo/logo.png" alt="Sodicol">
                </div>
                <h3 style="font-size: 1.35rem; font-weight: 700; color: #f7ede2;">Acceso al Sistema</h3>
                <p style="font-size: 0.8rem; color: #c4b5a8; margin-top: 4px;">Ingrese sus credenciales de colaborador</p>
            </div>

            <div class="modal-body-form">
                <?php if (!empty($mensajeError)): ?>
                <div style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 10px 14px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <?= htmlspecialchars($mensajeError) ?>
                </div>
                <?php endif; ?>

                <form action="<?= $baseUrl ?>index.php?action=login" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($tokenCsrf) ?>">

                    <div class="form-group-modal">
                        <label class="form-label-modal" for="correo"><i class="bi bi-envelope"></i> Correo Electrónico</label>
                        <input type="email" id="correo" name="correo" class="form-input-modal" placeholder="usuario@sodicol.com" required autocomplete="email">
                    </div>

                    <div class="form-group-modal">
                        <label class="form-label-modal" for="contrasena"><i class="bi bi-lock"></i> Contraseña</label>
                        <input type="password" id="contrasena" name="contrasena" class="form-input-modal" placeholder="••••••••" required autocomplete="current-password">
                    </div>

                    <button type="submit" class="btn-submit-modal">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Iniciar Sesión</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openLoginModal() {
            document.getElementById('loginModal').classList.add('active');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                const input = document.getElementById('correo');
                if (input) input.focus();
            }, 200);
        }

        function closeLoginModal() {
            document.getElementById('loginModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        document.getElementById('loginModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLoginModal();
            }
        });

        <?php if (!empty($mensajeError)): ?>
        window.addEventListener('DOMContentLoaded', () => {
            openLoginModal();
        });
        <?php endif; ?>
    </script>
</body>
</html>