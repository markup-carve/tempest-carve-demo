const links = document.querySelectorAll<HTMLAnchorElement>('a[href^="#"]')

for (const link of links) {
	link.addEventListener('click', (event) => {
		const target = document.querySelector(link.hash)

		if (target) {
			event.preventDefault()
			target.scrollIntoView({ behavior: 'smooth', block: 'start' })
		}
	})
}

const copySectionLinks = document.querySelectorAll<HTMLAnchorElement>('[data-copy-section-link]')

for (const button of copySectionLinks) {
	button.addEventListener('click', async (event) => {
		const section = button.closest<HTMLElement>('section[id]')

		if (!section) {
			return
		}

		const url = new URL(window.location.href)
		url.hash = section.id
		const label = section.querySelector('h2')?.textContent?.replace('#', '').trim() ?? 'this section'

		try {
			event.preventDefault()
			await navigator.clipboard.writeText(url.href)
			history.replaceState(null, '', url)
			button.dataset.copied = 'true'
			button.setAttribute('aria-label', 'Link copied')
			button.textContent = 'Copied'
			window.setTimeout(() => {
				button.dataset.copied = 'false'
				button.setAttribute('aria-label', `Copy link to ${label}`)
				button.textContent = '#'
			}, 1800)
		} catch {
			window.location.hash = section.id
		}
	})
}
