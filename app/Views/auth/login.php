<div class="auth-split">
    <section class="auth-brand" aria-label="PromptForge">
        <div class="auth-brand-inner">
            <?= icon('forge', 48) ?>
            <h1 class="auth-brand-title">PromptForge</h1>
            <p class="auth-brand-tagline">Versioná. Probá. Exportá.</p>
            <p class="auth-brand-copy">Biblioteca de prompts, instrucciones y plantillas con historial, pruebas y evaluaciones.</p>
        </div>
    </section>
    <section class="auth-form-panel">
        <div class="auth-card">
            <h2>Iniciar sesión</h2>
            <form method="post" action="<?= e(url('/login')) ?>" class="stack-form">
                <?= csrf_field() ?>
                <label class="field">
                    <span>Correo</span>
                    <input type="email" name="email" required autocomplete="username" placeholder="tu@correo.com">
                </label>
                <label class="field">
                    <span>Contraseña</span>
                    <input type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                </label>
                <button type="submit" class="btn btn-accent btn-block">Entrar</button>
            </form>
            <p class="auth-switch">¿No tenés cuenta? <a href="<?= e(url('/registro')) ?>">Registrate</a></p>
        </div>
    </section>
</div>
