<x-demo-page title="Code line options · Tempest Carve" active="code-lines" base="../">
  <x-slot name="hero">
      <div class="hero-grid hero-grid-single">
        <div>
          <p class="eyebrow">Line numbers and highlighted lines</p>
          <h1>Point at the line.<br><em>Keep the fence plain.</em></h1>
          <p class="lede">Three attributes above a fence: <code>.line-numbers</code>, <code>data-line-start</code> and <code>data-highlight</code>. <code>tempest_highlight</code> reads them and writes the gutter and the marked lines.</p>
        </div>
      </div>
  </x-slot>

      <section class="section" id="code-lines">
        <div class="section-heading">
          <p class="eyebrow">01 · Line options</p>
          <h2>Gutter, start and highlight <a class="section-copy-link" href="#code-lines" data-copy-section-link aria-label="Copy link to Gutter, start and highlight">#</a></h2>
          <p><code>data-highlight</code> takes 1-based numbers and <code>a-b</code> ranges, counted from the first line of the block, not from <code>data-line-start</code>. A caption keeps the options on <code>&lt;pre&gt;</code>.</p>
        </div>
        <div class="output-grid">
          <article class="output-card"><span class="output-label">code-lines.crv</span><div class="preformatted">{!! $source !!}</div></article>
          <article class="output-card output-html"><span class="output-label">Rendered with tempest_highlight</span><div class="carve" id="code-lines-output">{!! $html !!}</div></article>
        </div>
      </section>
</x-demo-page>
