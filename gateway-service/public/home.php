<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BanqueApp - API Reference</title>
    <style>
        :root {
            --primary: #1e3a5f;
            --primary-light: #2a5298;
            --bg: #f1f5f9;
            --card: #ffffff;
            --border: #e2e8f0;
            --text: #1e293b;
            --text-muted: #64748b;
            --radius: 8px;
            --shadow: 0 1px 3px rgba(0,0,0,.08);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 14px; color: var(--text); background: var(--bg); line-height: 1.5; }

        header { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: #fff; padding: 36px 40px; }
        header h1 { font-size: 24px; font-weight: 700; }
        header p  { margin-top: 4px; font-size: 13px; color: rgba(255,255,255,.65); }
        .header-meta { margin-top: 16px; display: flex; gap: 20px; }
        .meta-badge { background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2); border-radius: 20px; padding: 4px 12px; font-size: 12px; }

        main { max-width: 860px; margin: 32px auto; padding: 0 24px 60px; }

        .section { margin-bottom: 36px; }
        .section-header { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 2px solid var(--border); }
        .section-icon { width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; color: #fff; flex-shrink: 0; }
        .section-title { font-size: 16px; font-weight: 700; color: var(--text); }
        .section-desc  { font-size: 12px; color: var(--text-muted); margin-top: 1px; }

        .endpoint { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); margin-bottom: 10px; overflow: hidden; }
        .endpoint-header { display: flex; align-items: center; gap: 12px; padding: 12px 16px; cursor: pointer; user-select: none; }
        .endpoint-header:hover { background: #f8fafc; }
        .method { font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 4px; min-width: 52px; text-align: center; letter-spacing: .04em; }
        .GET    { background: #dbeafe; color: #1d4ed8; }
        .POST   { background: #dcfce7; color: #15803d; }
        .PUT    { background: #fef9c3; color: #a16207; }
        .DELETE { background: #fee2e2; color: #b91c1c; }
        .endpoint-path { font-family: 'Menlo', 'Monaco', monospace; font-size: 13px; font-weight: 600; color: var(--text); flex: 1; }
        .param { color: #7c3aed; }
        .auth-badge { font-size: 11px; padding: 2px 8px; border-radius: 10px; font-weight: 500; }
        .auth-required { background: #fef3c7; color: #92400e; }
        .auth-public   { background: #f0fdf4; color: #166534; }

        .endpoint-body { border-top: 1px solid var(--border); padding: 14px 16px; display: none; background: #fafafa; }
        .endpoint-body.open { display: block; }
        .body-row { display: flex; gap: 16px; }
        .body-col { flex: 1; }
        .body-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--text-muted); margin-bottom: 6px; }
        pre { background: #0f172a; color: #7dd3fc; font-size: 12px; font-family: 'Menlo', monospace; padding: 10px 14px; border-radius: 6px; overflow-x: auto; white-space: pre-wrap; }
        .no-body { color: var(--text-muted); font-size: 12px; font-style: italic; }

        footer { text-align: center; font-size: 12px; color: var(--text-muted); padding-bottom: 24px; }
    </style>
</head>
<body>

<header>
    <h1>BanqueApp - API Reference</h1>
    <p>Documentation des endpoints exposés par le gateway</p>
    <div class="header-meta">
        <span class="meta-badge">Base URL : http://localhost:8080</span>
        <span class="meta-badge">Auth : Bearer JWT</span>
        <span class="meta-badge">Format : application/json</span>
    </div>
</header>

<main>

    <!-- AUTH -->
    <div class="section">
        <div class="section-header">
            <div class="section-icon" style="background:#1e3a5f">A</div>
            <div>
                <div class="section-title">Auth Service</div>
                <div class="section-desc">Inscription, connexion et renouvellement de token</div>
            </div>
        </div>

        <?php endpoint('POST', '/auth/register', false, 'Créer un compte utilisateur',
            '{"email": "user@example.com", "password": "motdepasse"}',
            '{"id": "usr_abc123", "email": "user@example.com"}',
            201
        ); ?>

        <?php endpoint('POST', '/auth/login', false, 'Obtenir un token JWT',
            '{"email": "user@example.com", "password": "motdepasse"}',
            '{"token": "eyJ0eXAiOiJKV1Q..."}',
            200
        ); ?>

        <?php endpoint('POST', '/auth/refresh', false, 'Renouveler un token JWT',
            '{"token": "eyJ0eXAiOiJKV1Q..."}',
            '{"token": "eyJ0eXAiOiJKV1Q..."}',
            200
        ); ?>
    </div>

    <!-- CLIENT -->
    <div class="section">
        <div class="section-header">
            <div class="section-icon" style="background:#0891b2">C</div>
            <div>
                <div class="section-title">Client Service</div>
                <div class="section-desc">Gestion des clients bancaires</div>
            </div>
        </div>

        <?php endpoint('POST', '/clients', true, 'Créer un client',
            '{"nom": "Dupont", "prenom": "Jean", "email": "jean@dupont.fr"}',
            '{"id": "client_abc123", "nom": "Dupont", "prenom": "Jean", "email": "jean@dupont.fr"}',
            201
        ); ?>

        <?php endpoint('GET', '/clients', true, 'Lister tous les clients',
            null,
            '[{"id": "client_abc123", "nom": "Dupont", "prenom": "Jean", "email": "jean@dupont.fr"}]',
            200
        ); ?>

        <?php endpoint('GET', '/clients/{id}', true, 'Consulter un client par ID',
            null,
            '{"id": "client_abc123", "nom": "Dupont", "prenom": "Jean", "email": "jean@dupont.fr"}',
            200
        ); ?>
    </div>

    <!-- COMPTE -->
    <div class="section">
        <div class="section-header">
            <div class="section-icon" style="background:#059669">€</div>
            <div>
                <div class="section-title">Compte Service</div>
                <div class="section-desc">Création et gestion des comptes bancaires</div>
            </div>
        </div>

        <?php endpoint('POST', '/accounts', true, 'Créer un compte',
            '{"clientId": "client_abc123", "soldeInitial": 500.00}',
            '{"id": "cpt_xyz789", "clientId": "client_abc123", "solde": 500.00, "estBloque": false}',
            201
        ); ?>

        <?php endpoint('GET', '/accounts', true, 'Lister tous les comptes',
            null,
            '[{"id": "cpt_xyz789", "clientId": "client_abc123", "solde": 500.00, "estBloque": false}]',
            200
        ); ?>

        <?php endpoint('GET', '/accounts/{id}', true, 'Consulter un compte par ID',
            null,
            '{"id": "cpt_xyz789", "clientId": "client_abc123", "solde": 500.00, "estBloque": false}',
            200
        ); ?>

        <?php endpoint('POST', '/accounts/{id}/deposit', true, 'Effectuer un dépôt',
            '{"montant": 200.00}',
            '{"id": "cpt_xyz789", "clientId": "client_abc123", "solde": 700.00, "estBloque": false}',
            200
        ); ?>

        <?php endpoint('POST', '/accounts/{id}/withdraw', true, 'Effectuer un retrait',
            '{"montant": 100.00}',
            '{"id": "cpt_xyz789", "clientId": "client_abc123", "solde": 400.00, "estBloque": false}',
            200
        ); ?>
    </div>

    <!-- TRANSACTION -->
    <div class="section">
        <div class="section-header">
            <div class="section-icon" style="background:#7c3aed">T</div>
            <div>
                <div class="section-title">Transaction Service</div>
                <div class="section-desc">Virements entre comptes</div>
            </div>
        </div>

        <?php endpoint('POST', '/transactions', true, 'Effectuer un virement',
            '{"compteSourceId": "cpt_aaa", "compteDestinationId": "cpt_bbb", "montant": 150.00}',
            '{"id": "trx_def456", "compteSourceId": "cpt_aaa", "compteDestinationId": "cpt_bbb", "montant": 150.00, "statut": "COMPLETED"}',
            201
        ); ?>

        <?php endpoint('GET', '/transactions', true, 'Lister toutes les transactions',
            null,
            '[{"id": "trx_def456", "compteSourceId": "cpt_aaa", "compteDestinationId": "cpt_bbb", "montant": 150.00, "statut": "COMPLETED"}]',
            200
        ); ?>

        <?php endpoint('GET', '/transactions/{id}', true, 'Consulter une transaction par ID',
            null,
            '{"id": "trx_def456", "compteSourceId": "cpt_aaa", "compteDestinationId": "cpt_bbb", "montant": 150.00, "statut": "COMPLETED"}',
            201
        ); ?>
    </div>

</main>

<footer>BanqueApp &mdash; Architecture hexagonale multi-services &mdash; <?= date('Y') ?></footer>

<script>
    document.querySelectorAll('.endpoint-header').forEach(function (header) {
        header.addEventListener('click', function () {
            var body = header.nextElementSibling;
            if (body) body.classList.toggle('open');
        });
    });
</script>

</body>
</html>

<?php

function endpoint(string $method, string $path, bool $auth, string $desc, ?string $reqBody, ?string $resBody, int $status): void
{
    $pathHtml = preg_replace('/\{(\w+)\}/', '<span class="param">{$1}</span>', htmlspecialchars($path));
    $authHtml = $auth
        ? '<span class="auth-badge auth-required">JWT requis</span>'
        : '<span class="auth-badge auth-public">Public</span>';
    ?>
    <div class="endpoint">
        <div class="endpoint-header">
            <span class="method <?= $method ?>"><?= $method ?></span>
            <span class="endpoint-path"><?= $pathHtml ?></span>
            <span style="font-size:12px;color:var(--text-muted);margin-right:8px"><?= htmlspecialchars($desc) ?></span>
            <?= $authHtml ?>
        </div>
        <div class="endpoint-body">
            <div class="body-row">
                <div class="body-col">
                    <div class="body-label">Corps de la requête</div>
                    <?php if ($reqBody): ?>
                        <pre><?= htmlspecialchars($reqBody) ?></pre>
                    <?php else: ?>
                        <p class="no-body">Aucun corps requis</p>
                    <?php endif; ?>
                </div>
                <div class="body-col">
                    <div class="body-label">Réponse (HTTP <?= $status ?>)</div>
                    <?php if ($resBody): ?>
                        <pre><?= htmlspecialchars($resBody) ?></pre>
                    <?php else: ?>
                        <p class="no-body">Aucune réponse</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}
