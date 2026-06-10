<?php
/**
 * Vista: Login
 * Variables: $mensajeError (string), $csrf_token (string)
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema Sodicol</title>
    <script>if (localStorage.getItem('sodicol_tema') === 'dia') document.documentElement.style.background = '#f0e6d3';</script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/PROYECTO_SODICOL/css/estilos.css">
    <style>
        #btnModo { position: fixed; top: 18px; right: 22px; z-index: 1000; padding: 10px 14px; }
        #btnModo .modo-label { display: none; }
    </style>
</head>
<body class="login-page">

<canvas id="particle-canvas"></canvas>
<div class="noise-overlay"></div>

<div id="cursor"></div>
<div id="cursor-ring"></div>

<div class="loading-overlay" id="loadingOverlay">
    <div class="loader"></div>
    <div class="loading-text">VERIFICANDO...</div>
</div>

<button class="btn-modo" id="btnModo" title="Cambiar tema">
    <span class="modo-icon-dia"><i class="bi bi-sun-fill"></i></span>
    <span class="modo-icon-noche"><i class="bi bi-moon-stars-fill"></i></span>
    <span class="modo-label"></span>
</button>

<div class="card-wrapper" id="card3d">

    <!-- Panel izquierdo informativo -->
    <div class="panel-left">
        <div class="acc-ring acc-ring-1"></div>
        <div class="acc-ring acc-ring-2"></div>
        <div class="panel-brand">
            <p class="brand-eyebrow"><i class="bi bi-gem"></i> Sistema de Gestión</p>
            <h1>Soluciones<span>Logísticas</span>de Diseño</h1>
            <div class="services">
                <div class="svc">
                    <div class="svc-icon"><i class="bi bi-laptop"></i></div>
                    <div class="svc-text"><strong>Escritorios & Estaciones</strong>Diseño ergonómico a medida</div>
                </div>
                <div class="svc">
                    <div class="svc-icon"><i class="bi bi-easel2"></i></div>
                    <div class="svc-text"><strong>Marcos & Decoración</strong>Piezas únicas para cada espacio</div>
                </div>
                <div class="svc">
                    <div class="svc-icon"><i class="bi bi-file-earmark-pdf"></i></div>
                    <div class="svc-text"><strong>Cotizaciones PDF</strong>Cálculo interno automático</div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="footer-divider"></div>
            <p>© 2026 Sodicol · Todos los derechos reservados</p>
        </div>
    </div>

    <!-- Panel derecho: formulario -->
    <div class="panel-right">
        <div class="logo-wrap">
            <div class="logo-ring-wrap">
                <div class="logo-ring"></div>
                <div class="logo-ring"></div>
                <div class="logo-ring"></div>
                <div class="logo-halo"></div>
                <div class="logo-halo"></div>
                <div class="logo-halo"></div>
                <div class="logo-dash"></div>
                <div class="logo-circle">
                    <img src="/PROYECTO_SODICOL/logo/logo.png" alt="Logo Sodicol">
                </div>
            </div>
            <h2 class="login-title">Iniciar Sesión</h2>
            <p class="login-sub">Ingresa tus credenciales para continuar</p>
        </div>

        <?php if (!empty($mensajeError)): ?>
        <div class="error-box">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?= htmlspecialchars($mensajeError) ?>
        </div>
        <?php endif; ?>

        <div class="form-body">
            <form action="/PROYECTO_SODICOL/index.php" method="POST" id="loginForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                <div class="fgroup">
                    <label for="correo"><i class="bi bi-person-fill"></i> Usuario</label>
                    <div class="iw">
                        <i class="bi bi-envelope bi-left"></i>
                        <input type="email" id="correo" name="correo" placeholder="correo@ejemplo.com"
                               required autocomplete="email">
                    </div>
                </div>

                <div class="fgroup">
                    <label for="contrasena"><i class="bi bi-shield-lock-fill"></i> Contraseña</label>
                    <div class="iw">
                        <i class="bi bi-lock bi-left"></i>
                        <input type="password" id="contrasena" name="contrasena" placeholder="••••••••"
                               required autocomplete="current-password">
                        <button type="button" class="eye-btn" id="eyeBtn">
                            <i class="bi bi-eye-slash" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="submitBtn">
                    <i class="bi bi-box-arrow-in-right" id="btnIcon"></i>
                    <span id="btnText">Iniciar Sesión</span>
                    <div class="btn-particles"></div>
                </button>
            </form>
        </div>
    </div>

</div>

<script>
    /* Cursor personalizado */
    const cur = document.getElementById('cursor');
    const curR = document.getElementById('cursor-ring');
    let mx = 0, my = 0, rx = 0, ry = 0;
    document.addEventListener('mousemove', e => {
        mx = e.clientX; my = e.clientY;
        cur.style.left = mx + 'px'; cur.style.top = my + 'px';
    });
    (function lerp() {
        rx += (mx - rx) * .12; ry += (my - ry) * .12;
        curR.style.left = rx + 'px'; curR.style.top = ry + 'px';
        requestAnimationFrame(lerp);
    })();
    document.addEventListener('mousedown', () => cur.classList.add('clicked'));
    document.addEventListener('mouseup',   () => cur.classList.remove('clicked'));
    document.querySelectorAll('button, a, input, label').forEach(el => {
        el.addEventListener('mouseenter', () => {
            cur.style.width  = '20px'; cur.style.height = '20px';
            cur.style.background = 'var(--brown-warm)';
            curR.style.width = '50px'; curR.style.height = '50px';
            curR.style.borderColor = 'rgba(196,131,74,.7)';
        });
        el.addEventListener('mouseleave', () => {
            cur.style.width  = '12px'; cur.style.height = '12px';
            cur.style.background = 'var(--gold)';
            curR.style.width = '38px'; curR.style.height = '38px';
            curR.style.borderColor = 'rgba(196,131,74,.55)';
        });
    });
</script>
<script src="/PROYECTO_SODICOL/includes/script.js"></script>
</body>
</html>
