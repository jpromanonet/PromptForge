<section class="landing-hero">
    <nav class="landing-nav">
        <a class="brand brand-lg" href="<?= e(url('/')) ?>">
            <?= icon('forge', 28) ?>
            <span class="brand-text">PromptForge</span>
        </a>
        <div class="landing-nav-actions">
            <a class="btn btn-ghost" href="<?= e(url('/login')) ?>">Entrar</a>
            <a class="btn btn-accent" href="<?= e(url('/registro')) ?>">Registrarse</a>
        </div>
    </nav>

    <div class="landing-stage">
        <p class="eyebrow landing-eyebrow">Biblioteca versionada</p>
        <h1 class="landing-brand">PromptForge</h1>
        <p class="landing-lede">Guardá prompts, instrucciones y plantillas con historial de versiones, pruebas, evaluaciones y exportación a Markdown o JSON.</p>
        <div class="landing-cta">
            <a class="btn btn-accent btn-lg" href="<?= e(url('/registro')) ?>">Abrir la forja</a>
            <a class="btn btn-lg" href="<?= e(url('/login')) ?>">Ya tengo cuenta</a>
        </div>
    </div>

    <div class="landing-visual" aria-hidden="true">
        <svg class="forge-scene" viewBox="0 0 960 320" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="48" y="230" width="864" height="14" fill="#0B1520"/>
            <rect x="120" y="90" width="200" height="140" stroke="#0E9AA7" stroke-width="8"/>
            <rect x="380" y="70" width="220" height="160" stroke="#E07A2F" stroke-width="8"/>
            <rect x="660" y="110" width="180" height="120" stroke="#3E5C76" stroke-width="8"/>
            <text x="150" y="165" fill="#0E9AA7" font-family="JetBrains Mono, monospace" font-size="18">v3 · prompt</text>
            <text x="420" y="155" fill="#E07A2F" font-family="JetBrains Mono, monospace" font-size="18">{{vars}}</text>
            <text x="690" y="175" fill="#3E5C76" font-family="JetBrains Mono, monospace" font-size="18">eval</text>
            <circle cx="220" cy="55" r="12" fill="#0E9AA7" class="ember ember-a"/>
            <circle cx="490" cy="40" r="10" fill="#E07A2F" class="ember ember-b"/>
            <circle cx="750" cy="70" r="11" fill="#1F7A5C" class="ember ember-c"/>
        </svg>
    </div>
</section>

<section class="landing-section">
    <h2>Del borrador a la versión estable</h2>
    <p class="lede">Cada cambio queda versionado. Cada prueba deja rastro. Exportá cuando esté listo.</p>
    <div class="feature-strip">
        <article>
            <h3>Versionado</h3>
            <p>Historial completo de prompts, instrucciones y plantillas.</p>
        </article>
        <article>
            <h3>Pruebas</h3>
            <p>Casos de input/esperado y criterios de calidad.</p>
        </article>
        <article>
            <h3>Export</h3>
            <p>Markdown o JSON listos para compartir o versionar afuera.</p>
        </article>
    </div>
</section>
