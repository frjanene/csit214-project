<?php // main layout: includes header/nav ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($title) ? $title . ' · ' : '' ?>FlyDreamAir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome (for icons) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    
    <link href="assets/css/styles.css" rel="stylesheet">
  </head>
  <body class="page-shell">
    <?php require __DIR__ . '/../partials/header.php'; ?>
    <main class="py-4">
      <div class="container">
        <?php require __DIR__ . '/../pages/' . $template . '.php'; ?>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
  </body>
</html>
