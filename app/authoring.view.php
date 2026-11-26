<x-demo-page title="Authoring tools · Tempest Carve" active="authoring" base="../">
  <x-slot name="hero">
      <div class="hero-grid hero-grid-single">
        <div>
          <p class="eyebrow">Lint, format, import, validate</p>
          <h1>Help authors.<br><em>Before they publish.</em></h1>
          <p class="lede">The <code>CarveRenderer</code> tools beyond rendering: lint findings, canonical formatting, HTML and Markdown import, the <code>ValidCarve</code> rule, the <code>CarveContent</code> model property, strict losses, and include limits.</p>
        </div>
      </div>
  </x-slot>

      <section class="section" id="lint">
        <div class="section-heading">
          <p class="eyebrow">01 · Lint</p>
          <h2>Find what the author did not mean <a class="section-copy-link" href="#lint" data-copy-section-link aria-label="Copy link to Find what the author did not mean">#</a></h2>
          <p><code>lint()</code> returns <code>LintFinding</code> values with rule, message, line and column. <code>lintIncluded()</code> resolves includes through the configured resolver first, so a missing database row is reported as <code>include-not-found</code>.</p>
        </div>
        <div class="output-grid">
          <article class="output-card"><span class="output-label">samples/lint.crv</span><div class="preformatted">{!! $lintSource !!}</div></article>
          <article class="report-list">
            <h3>lint()</h3>
            <div class="report-row" :foreach="$lint as $finding"><span class="diagnostic-code">{{ $finding['position'] }} {{ $finding['rule'] }}</span><span>{{ $finding['message'] }}</span></div>
            <h3>lintIncluded()</h3>
            <div class="report-row" :foreach="$lintIncluded as $finding"><span class="diagnostic-code">{{ $finding['position'] }} {{ $finding['rule'] }}</span><span>{{ $finding['message'] }}</span></div>
          </article>
        </div>
      </section>

      <section class="section" id="format">
        <div class="section-heading">
          <p class="eyebrow">02 · Format</p>
          <h2>Write the canonical form <a class="section-copy-link" href="#format" data-copy-section-link aria-label="Copy link to Write the canonical form">#</a></h2>
          <p><code>format()</code> returns canonical Carve; <code>alignTables: true</code> also pads table columns.</p>
        </div>
        <div class="output-grid output-grid-three">
          <article class="output-card"><span class="output-label">samples/unformatted.crv</span><div class="preformatted">{!! $unformatted !!}</div></article>
          <article class="output-card"><span class="output-label">format()</span><div class="preformatted">{!! $formatted !!}</div></article>
          <article class="output-card"><span class="output-label">format(alignTables: true)</span><div class="preformatted">{!! $aligned !!}</div></article>
        </div>
      </section>

      <section class="section" id="import">
        <div class="section-heading">
          <p class="eyebrow">03 · Import</p>
          <h2>Bring existing content in <a class="section-copy-link" href="#import" data-copy-section-link aria-label="Copy link to Bring existing content in">#</a></h2>
          <p><code>fromHtml()</code> and <code>fromMarkdown()</code> convert other markup into Carve.</p>
        </div>
        <div class="output-grid">
          <article class="output-card"><span class="output-label">samples/import.html</span><div class="preformatted">{!! $htmlSource !!}</div></article>
          <article class="output-card"><span class="output-label">fromHtml()</span><div class="preformatted">{!! $fromHtml !!}</div></article>
          <article class="output-card"><span class="output-label">samples/import.md</span><div class="preformatted">{!! $markdownSource !!}</div></article>
          <article class="output-card"><span class="output-label">fromMarkdown()</span><div class="preformatted">{!! $fromMarkdown !!}</div></article>
        </div>
      </section>

      <section class="section" id="validation">
        <div class="section-heading">
          <p class="eyebrow">04 · Validation</p>
          <h2>Reject markup at the boundary <a class="section-copy-link" href="#validation" data-copy-section-link aria-label="Copy link to Reject markup at the boundary">#</a></h2>
          <p><code>App\CommentForm</code> carries <code>#[ValidCarve(profile: CarveProfile::Comment, lint: true, maxLength: 120, message: ...)]</code>. Each input below runs through Tempest's validator.</p>
        </div>
        <table class="data-table">
          <thead><tr><th scope="col">Input</th><th scope="col">Result</th></tr></thead>
          <tbody>
            <tr :foreach="$validation as $row"><td><span class="inline-code">{{ $row['input'] }}</span></td><td :class="$row['valid'] ? 'valid' : 'invalid'">{{ $row['message'] }}</td></tr>
          </tbody>
        </table>
      </section>

      <section class="section" id="model">
        <div class="section-heading">
          <p class="eyebrow">05 · Model property</p>
          <h2>Store source, read HTML <a class="section-copy-link" href="#model" data-copy-section-link aria-label="Copy link to Store source, read HTML">#</a></h2>
          <p><code>App\Post::$body</code> is typed <code>CarveContent</code>. The mapper casts the stored string, the object renders on demand, <code>html('comments')</code> uses a named renderer, and serializing writes the source back.</p>
        </div>
        <div class="output-grid">
          <article class="output-card"><span class="output-label">$post-&gt;body-&gt;source</span><div class="preformatted">{!! $post['source'] !!}</div></article>
          <article class="output-card"><span class="output-label">(string) $post-&gt;body</span><div class="preformatted">{!! $post['html'] !!}</div></article>
          <article class="output-card"><span class="output-label">$post-&gt;body-&gt;html('comments')</span><div class="preformatted">{!! $post['comments'] !!}</div></article>
          <article class="output-card"><span class="output-label">map($post)-&gt;toArray()</span><div class="preformatted">{!! $post['stored'] !!}</div></article>
        </div>
      </section>

      <section class="section" id="limits">
        <div class="section-heading">
          <p class="eyebrow">06 · Strict losses and include limits</p>
          <h2>Fail loudly, or stop early <a class="section-copy-link" href="#limits" data-copy-section-link aria-label="Copy link to Fail loudly, or stop early">#</a></h2>
          <p><code>renderWithReport(strictLosses: true)</code> throws instead of dropping content. <code>IncludeOptions</code> narrows include depth, byte budget and the warning count; <code>suppressedWarnings</code> counts the warnings past the limit.</p>
        </div>
        <p class="dependency-line">strictLosses: <span class="diagnostic-code" id="strict-losses">{{ $strictLosses }}</span></p>
        <table class="data-table">
          <thead><tr><th scope="col">IncludeOptions</th><th scope="col">Warnings</th><th scope="col">Suppressed</th></tr></thead>
          <tbody>
            <tr :foreach="$limits as $row"><td><span class="inline-code">{{ $row['options'] }}</span></td><td><div :foreach="$row['warnings'] as $warning">{{ $warning }}</div></td><td>{{ $row['suppressed'] ?? 'n/a' }}</td></tr>
          </tbody>
        </table>
      </section>
</x-demo-page>
