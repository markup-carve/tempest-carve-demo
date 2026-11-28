<?php
/**
 * @var string $title
 * @var string|null $active
 * @var string|null $base Relative path to the site root, so the static export works below a subpath
 */
$active ??= null;
$base ??= '';
$root = $base === '' ? './' : $base;
$pages = [
    'showcase' => [$root, 'Showcase'],
    'extensions' => [$root . 'extensions/', 'Extensions & options'],
    'code-lines' => [$root . 'code-lines/', 'Code lines'],
    'authoring' => [$root . 'authoring/', 'Authoring tools'],
    'console' => [$root . 'console/', 'Console'],
];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="A complete Tempest Carve integration showcase">
  <link rel="icon" href="/favicon/favicon.ico" sizes="any">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
  <link rel="apple-touch-icon" href="/favicon/apple-touch-icon.png">
  <title>{{ $title }}</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/markup-carve/carve-css@5c3547fe489ea86c42eed08e9b27639ac81d72f5/src/carve.css">
  <x-vite-tags />
</head>
<body>
  <div class="page-shell">
    <header class="hero">
      <nav class="nav" aria-label="Primary navigation">
        <a class="brand" :href="$root">Tempest <span>×</span> Carve</a>
        <div class="nav-links">
          <a :foreach="$pages as $key => $page" :href="$page[0]" :aria-current="$key === $active ? 'page' : 'false'">{{ $page[1] }}</a>
        </div>
      </nav>
      <x-slot name="hero" />
    </header>

    <main>
      <x-slot />
    </main>

    <footer><span>Tempest <a href="https://markup-carve.github.io/carve/">Carve</a> demo</span><span>Powered by <a href="https://github.com/markup-carve/tempest-carve"><code>markup-carve/tempest-carve</code></a></span></footer>
  </div>
</body>
</html>
