const common = {
	header: '¡Hola, Usuario del Portal!',
	main: (content) => `<p style="font-size:17px; font-weight:600;">¿Utilizas y te encanta ${content.domain} y sus colecciones?</p>
		<p>Este portal, al igual que otros similares, cuenta con el apoyo de un <strong>pequeño grupo de personas muy comprometidas</strong>, el Symbiota Support Hub (SSH), que se encarga del mantenimiento del sitio web.</p>
		<p><strong style="color:red;">La financiación federal para el SSH ha finalizado</strong>, y este pequeño equipo se encarga ahora del mantenimiento de <mark>más de 52 portales</mark> y <mark>90 millones de registros de presencia</mark> de vida en la Tierra... ¡y la cifra sigue creciendo!</p>
		<p><a href="${content.donate_url}" target="_blank" onclick="setDonateCookie(60*60*24*31);">Por favor, apoya este portal mediante una donación al SSH.</a> Tu contribución ayuda a cada colección que comparte datos aquí.</p>
		<p>Muchas gracias.<br>-${content.domain}, Nico, Ed, Jenn, Katie, Greg</p>`,
	close: 'Cerrar',
	donate: 'Donar',
	more_ways: 'Más Formas de Apoyar el Portal'
};