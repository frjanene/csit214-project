<?php // bare layout: no header/nav (for Welcome + Auth) ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($title) ? $title . ' · ' : '' ?>FlyDreamAir</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome (for icons) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <!-- App styles -->
    <link href="assets/css/styles.css" rel="stylesheet">
  </head>
  <body class="auth-shell">
    <main class="py-5">
      <div class="container">

        <?php
        // Show flash here for bare pages EXCEPT welcome (welcome shows flash inside the modal)
        if (($template ?? '') !== 'welcome' && ($flash = get_flash())):
          foreach ($flash as $type => $msg): ?>
            <div class="alert alert-<?= $type === 'error' ? 'danger' : 'success' ?>"><?= $msg ?></div>
          <?php endforeach;
        endif; ?>

        <?php require __DIR__ . '/../pages/' . $template . '.php'; ?>
      </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
  </body>
</html>
