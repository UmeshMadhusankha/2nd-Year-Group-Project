<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>404 — Page Not Found | Fix Lanka</title>
  <link rel="stylesheet" href="../../assets/css/common/404.css">
</head>
<body>
  <main class="error-page" role="main" aria-labelledby="error-title">
    <div class="error-card">
      <div class="visual">
        <svg viewBox="0 0 120 120" aria-hidden="true" focusable="false">
          <defs>
            <linearGradient id="g" x1="0" x2="1">
              <stop offset="0" stop-color="var(--primary-color)" />
              <stop offset="1" stop-color="var(--accent-color)" />
            </linearGradient>
          </defs>
          <circle cx="60" cy="60" r="56" fill="url(#g)" opacity="0.12"></circle>
          <text x="50%" y="52%" text-anchor="middle" font-size="46" fill="url(#g)" font-weight="700">404</text>
        </svg>
      </div>

      <h1 id="error-title" class="title">Page not found</h1>
      <p class="subtitle">We can’t find the page you’re looking for. It may have been moved or deleted.</p>

      <div class="actions">
        <a class="btn btn-primary" href="/">Return home</a>
        <button class="btn btn-outline" id="goBack">Go back</button>
      </div>

      <p class="hint">If you think this is a mistake, contact support.</p>
    </div>
  </main>

  <script>
    document.getElementById('goBack').addEventListener('click', function () {
      if (history.length > 1) history.back();
      else window.location.href = '/';
    });
  </script>
</body>
</html>