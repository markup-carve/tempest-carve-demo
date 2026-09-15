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
