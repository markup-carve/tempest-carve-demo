<x-demo-page title="Extensions and options · Tempest Carve" active="extensions" base="../">
  <x-slot name="hero">
      <div class="hero-grid hero-grid-single">
        <div>
          <p class="eyebrow">Extensions, options and renderers</p>
          <h1>Shape the output.<br><em>Keep the source.</em></h1>
          <p class="lede">Every <code>CarveConfig</code> setting on one page: named and container-resolved extensions, the rendering options, named renderers, and a host output writer.</p>
        </div>
      </div>
  </x-slot>

      <section class="section" id="extensions">
        <div class="section-heading">
          <p class="eyebrow">01 · Extensions</p>
          <h2>Configured extensions <a class="section-copy-link" href="#extensions" data-copy-section-link aria-label="Copy link to Configured extensions">#</a></h2>
          <p><code>app/carve.config.php</code> lists an engine definition (<code>Navigation::headingNumbers()</code>), a container-resolved class (<code>App\Carve\DisclosureExtensions</code> for <code>details</code> and <code>spoiler</code>), and the adapter names <code>tabs</code>, <code>mermaid</code>, <code>plantuml</code>, <code>fenced_render</code> with a <code>language</code>, <code>wikilinks</code> with a <code>url_template</code>, and <code>tempest_highlight</code>.</p>
        </div>
        <div class="output-grid">
          <article class="output-card"><span class="output-label">extensions.crv</span><div class="preformatted">{!! $source !!}</div></article>
          <article class="output-card output-html"><span class="output-label">Rendered by the container's renderer</span><div class="carve" id="extensions-output">{!! $html !!}</div></article>
        </div>
      </section>

      <section class="section" id="wiki-generator">
        <div class="section-heading">
          <p class="eyebrow">02 · Wiki URL generator</p>
          <h2>Map wiki pages with a closure <a class="section-copy-link" href="#wiki-generator" data-copy-section-link aria-label="Copy link to Map wiki pages with a closure">#</a></h2>
          <p><code>wikilinks</code> also takes a <code>url_generator</code> closure. <code>WikiLinkUrlTemplate</code> builds one from a <code>{page}</code> template with its own slug rules.</p>
        </div>
        <div class="output-grid">
          <article class="output-card"><span class="output-label">Source</span><div class="preformatted">{!! $wikiGenerator['source'] !!}</div></article>
          <article class="output-card"><span class="output-label">HTML</span><div class="preformatted">{!! $wikiGenerator['html'] !!}</div></article>
        </div>
      </section>

      <section class="section" id="options">
        <div class="section-heading">
          <p class="eyebrow">03 · Rendering options</p>
          <h2>Each option, before and after <a class="section-copy-link" href="#options" data-copy-section-link aria-label="Copy link to Each option, before and after">#</a></h2>
          <p>The same source rendered with the default and with one changed option.</p>
        </div>
        <div class="option-list">
          <article class="option-row" :foreach="$options as $row">
            <h3><span class="inline-code">{{ $row['option'] }}</span></h3>
            <div class="option-columns">
              <div><span class="output-label">Source</span><div class="preformatted">{!! $row['source'] !!}</div></div>
              <div><span class="output-label">{{ $row['beforeLabel'] }}</span><div class="preformatted">{!! $row['before'] !!}</div></div>
              <div><span class="output-label">{{ $row['afterLabel'] }}</span><div class="preformatted">{!! $row['after'] !!}</div></div>
            </div>
          </article>
        </div>
        <h3 class="table-title"><code>headingIds</code> for <code>= Über Straße &amp; Co</code></h3>
        <table class="data-table">
          <thead><tr><th scope="col">Value</th><th scope="col">Generated id</th></tr></thead>
          <tbody>
            <tr :foreach="$headingIds as $row"><td><span class="inline-code">{{ $row['value'] }}</span></td><td><span class="inline-code">{{ $row['id'] }}</span></td></tr>
          </tbody>
        </table>
      </section>

      <section class="section" id="named">
        <div class="section-heading">
          <p class="eyebrow">04 · Named renderers</p>
          <h2>One container, several policies <a class="section-copy-link" href="#named" data-copy-section-link aria-label="Copy link to One container, several policies">#</a></h2>
          <p><code>namedRenderers</code> in the config builds extra renderers; <code>$carve-&gt;named('comments')</code> returns one.</p>
        </div>
        <div class="profile-grid">
          <article class="profile-card" :foreach="$named as $row">
            <div class="card-head"><span class="profile-name">{{ $row['name'] }}</span><span class="status-dot"></span></div>
            <p><span class="inline-code">{{ $row['config'] }}</span></p>
            <div class="preformatted named-output">{!! $row['html'] !!}</div>
          </article>
        </div>
      </section>

      <section class="section" id="writers">
        <div class="section-heading">
          <p class="eyebrow">05 · Output writers</p>
          <h2>Register a host writer <a class="section-copy-link" href="#writers" data-copy-section-link aria-label="Copy link to Register a host writer">#</a></h2>
          <p>The <code>excerpt</code> renderer registers <code>App\Carve\ExcerptWriter</code> for the <code>text</code> target in <code>writers</code>, so <code>renderPlainText()</code> returns a one-line excerpt instead of the engine's plain-text export.</p>
        </div>
        <div class="output-grid">
          <article class="output-card"><span class="output-label">renderPlainText() · engine export</span><div class="preformatted">{!! $excerpt['default'] !!}</div></article>
          <article class="output-card"><span class="output-label">named('excerpt')->renderPlainText()</span><div class="preformatted" id="excerpt-output">{!! $excerpt['writer'] !!}</div></article>
        </div>
      </section>
</x-demo-page>
