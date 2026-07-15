import { test as base } from "@playwright/test"
import { getSuite, Suite } from "../types/Suite";

const SymbLocations = {
	Taxonomy: {
		Editor: (tid: string|number)=> 'taxa/profile/tpeditor.php?tid=' + tid,
	}
}

const LaravelLocations = {
	Taxonomy: {
		Editor: (tid: string|number)=> 'taxa/' + tid + '/editor',
	}
}

function nav() {
	switch (getSuite()) {
	  case Suite.Laravel:
		return LaravelLocations;
	  default:
		return SymbLocations;
	}
}

const test = base.extend<{ nav: Object }>({
	nav: async ({}, use) => {
		await use(nav());
	}
});

export { test, nav };
