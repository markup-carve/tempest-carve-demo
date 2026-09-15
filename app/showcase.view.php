<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="A complete Tempest Carve integration showcase">
  <title>Tempest Carve showcase</title>
  <x-vite-tags />
</head>
<body>
  <div class="page-shell">
    <header class="hero">
      <nav class="nav" aria-label="Primary navigation">
        <a class="brand" href="./">Tempest <span>×</span> Carve</a>
        <div class="nav-links">
          <a href="#profiles">Profiles</a>
          <a href="#outputs">Outputs</a>
          <a href="#includes">Includes</a>
          <a href="#diagnostics">Diagnostics</a>
        </div>
      </nav>

      <div class="hero-grid">
        <div>
          <p class="eyebrow">Safe document rendering for Tempest</p>
          <h1>One source.<br><em>Every publishing surface.</em></h1>
          <p class="lede">A complete tour of the Tempest integration: view components, profiles, extensions, caching, diagnostics, editor source lines, and four output formats.</p>
          <div class="pills" aria-label="Enabled configuration">
            <span>Safe mode</span><span>Cached</span><span>Source lines</span><span>Heading numbers</span>
          </div>
        </div>
        <div class="code-window">
          <div class="window-bar"><i></i><i></i><i></i><span>article.crv</span></div>
          <div class="preformatted">{!! $componentSourceEscaped !!}</div>
        </div>
      </div>
    </header>

    <main>
      <section class="section component-demo" id="component">
        <div class="section-heading">
          <p class="eyebrow">01 · View component</p>
          <h2>Drop Carve into a Tempest view</h2>
          <p>The output below comes directly from <code>&lt;x-carve :content="$componentSource" /&gt;</code>. The application config enables safe mode, source-line annotations, heading numbering, smart typography, and Tempest Cache.</p>
        </div>
        <div class="paper carve">
          <x-carve :content="$componentSource" />
        </div>
      </section>

      <section class="section" id="profiles">
        <div class="section-heading">
          <p class="eyebrow">02 · Publishing profiles</p>
          <h2>Match the vocabulary to the context</h2>
          <p>The same document is filtered through each carve-php profile. Restricted constructs degrade to text instead of disappearing silently.</p>
        </div>
        <div class="profile-grid">
          <article class="profile-card" :foreach="$profiles as $profile">
            <div class="card-head">
              <span class="profile-name">{{ $profile['name'] }}</span>
              <span class="status-dot"></span>
            </div>
            <p>{{ $profile['purpose'] }}</p>
            <div class="profile-output carve">{!! $profile['html'] !!}</div>
          </article>
        </div>
      </section>

      <section class="section" id="outputs">
        <div class="section-heading">
          <p class="eyebrow">03 · Output formats</p>
          <h2>Render once for every channel</h2>
          <p>HTML for pages, Markdown for interchange, plain text for search and excerpts, and ANSI for the terminal.</p>
        </div>
        <div class="output-grid">
          <article class="output-card output-html"><span class="output-label">HTML</span><div class="carve">{!! $htmlOutput !!}</div></article>
          <article class="output-card"><span class="output-label">Markdown</span><div class="preformatted">{!! $markdownOutput !!}</div></article>
          <article class="output-card"><span class="output-label">Plain text</span><div class="preformatted">{!! $plainOutput !!}</div></article>
          <article class="output-card output-terminal"><span class="output-label">ANSI</span><div class="preformatted">{!! $ansiOutput !!}</div></article>
        </div>
      </section>

      <section class="section" id="includes">
        <div class="section-heading">
          <p class="eyebrow">04 · Carve core includes</p>
          <h2>Compose documents from files</h2>
          <p>The latest carve-php core runs inside the Tempest app to resolve a chapter and its nested include beneath an explicit filesystem root. Automatic heading shifts preserve the assembled outline.</p>
        </div>
        <div class="output-grid">
          <article class="output-card"><span class="output-label">handbook.crv</span><div class="preformatted">{!! $includeSource !!}</div></article>
          <article class="output-card output-html"><span class="output-label">Expanded result</span><div class="carve">{!! $includeHtml !!}</div></article>
        </div>
        <p class="dependency-line">Tracked dependencies: <span class="diagnostic-code">{{ implode(', ', $includeDependencies) }}</span></p>
      </section>

      <section class="section diagnostics" id="diagnostics">
        <div class="section-heading">
          <p class="eyebrow">05 · Authoring diagnostics</p>
          <h2>Explain every compromise</h2>
          <p><code>renderWithReport()</code> combines safe HTML with source-aware warnings, profile violations, and bounded target-loss reporting.</p>
        </div>
        <div class="diagnostic-grid">
          <article class="metric warning"><strong>{{ count($report->warnings) }}</strong><span>Parser warnings</span></article>
          <article class="metric violation"><strong>{{ count($report->profileViolations) }}</strong><span>Profile violations</span></article>
          <article class="metric loss"><strong>{{ $lossReport->totalLosses }}</strong><span>Render losses</span></article>
          <article class="report-list">
            <h3>Warnings</h3>
            <div class="report-row" :foreach="$report->warnings as $warning">
              <span class="diagnostic-code">L{{ $warning['line'] }}:C{{ $warning['column'] }}</span>
              <span>{{ $warning['message'] }}</span>
            </div>
          </article>
          <article class="report-list">
            <h3>Profile decisions</h3>
            <div class="report-row" :foreach="$report->profileViolations as $violation">
              <span class="diagnostic-code">{{ $violation['nodeType'] }}</span>
              <span>{{ $violation['reasonDescription'] ?? $violation['reason'] }}</span>
            </div>
          </article>
          <article class="report-list">
            <h3>Target losses</h3>
            <div class="report-row" :foreach="$lossReport->losses as $loss">
              <span class="diagnostic-code">{{ $loss['code'] }}</span>
              <span>{{ $loss['message'] }}</span>
            </div>
          </article>
        </div>
      </section>

      <section class="section feature-strip">
        <article><span>06</span><h3>Source lines</h3><p>Supported blocks in the main preview carry a <code>data-source-line</code> anchor for scroll synchronization.</p></article>
        <article><span>07</span><h3>Content cache</h3><p>The configured renderer stores deterministic output by source, format, profile, options, extensions, and engine version.</p><p class="feature-note">Enabled through Tempest Cache with a one-hour expiration.</p></article>
        <article><span>∞</span><h3>Safe by default</h3><p>Raw HTML is escaped in HTML and Markdown, omitted from plain text, and represented only as text in ANSI.</p></article>
      </section>
    </main>

    <footer><span>Tempest Carve demo</span><span>Powered by <code>markup-carve/tempest-carve</code></span></footer>
  </div>
</body>
</html>
