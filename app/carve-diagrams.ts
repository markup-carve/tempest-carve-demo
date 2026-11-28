// Draws the diagram fences the configured extensions emit (mermaid, plantuml,
// graphviz). Each renderer loads on demand; a failure keeps the readable source.

const cdn = (url: string) => import(/* @vite-ignore */ url)

function drawn(pre: HTMLElement, svg: string | Element): void {
	const figure = document.createElement('div')
	figure.className = 'diagram-drawn'
	if (typeof svg === 'string') {
		figure.innerHTML = svg
	} else {
		figure.append(svg)
	}
	pre.replaceWith(figure)
}

async function kroki(pre: HTMLElement, type: string): Promise<void> {
	const response = await fetch(`https://kroki.io/${type}/svg`, {
		method: 'POST',
		headers: { 'Content-Type': 'text/plain' },
		body: pre.textContent ?? '',
	})
	if (response.ok) {
		drawn(pre, await response.text())
	}
}

export async function drawDiagrams(root: ParentNode = document): Promise<void> {
	const mermaid = [...root.querySelectorAll<HTMLElement>('.carve pre.mermaid')]
	if (mermaid.length > 0) {
		try {
			const { default: engine } = await cdn('https://cdn.jsdelivr.net/npm/mermaid@11.12.1/dist/mermaid.esm.min.mjs')
			engine.initialize({ startOnLoad: false, theme: 'neutral' })
			for (const [index, pre] of mermaid.entries()) {
				const { svg } = await engine.render(`carve-mermaid-${index}`, pre.textContent ?? '')
				drawn(pre, svg)
			}
		} catch { /* keep source */ }
	}

	const graphviz = [...root.querySelectorAll<HTMLElement>('.carve pre.graphviz')]
	if (graphviz.length > 0) {
		try {
			const { instance } = await cdn('https://cdn.jsdelivr.net/npm/@viz-js/viz@3.14.0/lib/viz-standalone.mjs')
			const viz = await instance()
			for (const pre of graphviz) {
				drawn(pre, viz.renderSVGElement(pre.textContent ?? ''))
			}
		} catch { /* keep source */ }
	}

	// PlantUML has no in-browser renderer; Kroki draws it (needs network).
	for (const pre of root.querySelectorAll<HTMLElement>('.carve pre.plantuml')) {
		kroki(pre, 'plantuml').catch(() => { /* keep source */ })
	}
}
