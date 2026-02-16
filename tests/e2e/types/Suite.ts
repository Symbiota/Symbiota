export enum Suite {
	Laravel= 'Symbiota-Laravel',
	Symbiota = 'Symbiota',
}

export function getSuite() {
	return process.env.suite ? process.env.suite: Suite.Symbiota
}
