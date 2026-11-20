<x-demo-page title="Tempest Carve showcase" active="showcase">
  <x-slot name="hero">
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
  </x-slot>

      <section class="section component-demo" id="component">
        <div class="section-heading">
          <p class="eyebrow">01 · View component</p>
          <h2>Drop Carve into a Tempest view <a class="section-copy-link" href="#component" data-copy-section-link aria-label="Copy link to Drop Carve into a Tempest view">#</a></h2>
          <p>The output below comes directly from <code>&lt;x-carve :content="$componentSource" /&gt;</code>. The application config enables safe mode, source-line annotations, heading numbering, smart typography, and Tempest Cache.</p>
        </div>
        <div class="paper carve">
          <x-carve :content="$componentSource" />
        </div>
      </section>

      <section class="section" id="profiles">
        <div class="section-heading">
          <p class="eyebrow">02 · Publishing profiles</p>
          <h2>Match the vocabulary to the context <a class="section-copy-link" href="#profiles" data-copy-section-link aria-label="Copy link to Match the vocabulary to the context">#</a></h2>
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
          <h2>Render once for every channel <a class="section-copy-link" href="#outputs" data-copy-section-link aria-label="Copy link to Render once for every channel">#</a></h2>
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
          <p class="eyebrow">04 · Database-backed includes</p>
          <h2>Compose documents from reusable sources <a class="section-copy-link" href="#includes" data-copy-section-link aria-label="Copy link to Compose documents from reusable sources">#</a></h2>
          <p>A container-injected resolver backed by an in-memory SQLite repository loads Carve snippets from rows. The included row references a sibling row relatively, automatic heading shifts preserve the assembled outline, a CSV row becomes a table with <code>@rows</code> and <code>@cols</code> picking records and columns, and content revisions invalidate cached output.</p>
        </div>
        <div class="output-grid">
          <article class="output-card"><span class="output-label">Parent document</span><div class="preformatted">{!! $includeSource !!}</div></article>
          <article class="output-card output-html"><span class="output-label">Expanded result</span><div class="carve">{!! $includeHtml !!}</div></article>
        </div>
        <p class="dependency-line">Tracked dependencies: <span class="diagnostic-code">{{ implode(', ', $includeDependencies) }}</span> · {{ $includeWarnings }} warnings</p>
      </section>

      <section class="section diagnostics" id="diagnostics">
        <div class="section-heading">
          <p class="eyebrow">05 · Authoring diagnostics</p>
          <h2>Explain every compromise <a class="section-copy-link" href="#diagnostics" data-copy-section-link aria-label="Copy link to Explain every compromise">#</a></h2>
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
              <span>{{ $violation['message'] }}</span>
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
      <section class="section feature-links" id="more">
        <div class="section-heading">
          <p class="eyebrow">08 · Everything else</p>
          <h2>The rest of the adapter <a class="section-copy-link" href="#more" data-copy-section-link aria-label="Copy link to The rest of the adapter">#</a></h2>
          <p>Every remaining tempest-carve feature has a page of its own.</p>
        </div>
        <div class="link-grid">
          <a class="link-card" href="extensions/"><strong>Extensions &amp; options</strong><span>Named and container-resolved extensions, rendering options, named renderers and a host writer.</span></a>
          <a class="link-card" href="authoring/"><strong>Authoring tools</strong><span>Lint, formatting, HTML and Markdown import, validation, model properties and include limits.</span></a>
          <a class="link-card" href="console/"><strong>Console</strong><span>The <code>carve:render</code>, <code>carve:lint</code>, <code>carve:fmt</code> and <code>carve:convert</code> commands.</span></a>
        </div>
      </section>
</x-demo-page>
