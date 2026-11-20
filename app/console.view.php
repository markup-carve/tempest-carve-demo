<x-demo-page title="Console commands · Tempest Carve" active="console" base="../">
  <x-slot name="hero">
      <div class="hero-grid hero-grid-single">
        <div>
          <p class="eyebrow">Console commands</p>
          <h1>The same renderer.<br><em>From the terminal.</em></h1>
          <p class="lede">tempest-carve registers <code>carve:render</code>, <code>carve:lint</code>, <code>carve:fmt</code> and <code>carve:convert</code>. They use the discovered <code>CarveRenderer</code>, so the demo's extensions, named renderers and writers apply.</p>
        </div>
      </div>
  </x-slot>

      <section class="section" id="commands">
        <div class="section-heading">
          <p class="eyebrow">01 · Live runs</p>
          <h2>Every command, run for this page <a class="section-copy-link" href="#commands" data-copy-section-link aria-label="Copy link to Every command, run for this page">#</a></h2>
          <p>Each block is the output of a real <code>php tempest</code> process on the files in <code>content/</code>, with its exit code. Put the path before the flags: Tempest matches positional arguments by position.</p>
        </div>
        <div class="console-list">
          <article class="output-card output-terminal console-run" :foreach="$runs as $run">
            <span class="output-label">$ {{ $run['command'] }} <span class="exit-code">exit {{ $run['exit'] }}</span></span>
            <div class="preformatted">{!! $run['output'] !!}</div>
          </article>
        </div>
      </section>
</x-demo-page>
