import type { Page, Locator } from '@playwright/test';
import { Form } from "./Form";

const collectionFields = {
}

export class CollectionForm extends Form {

	constructor(selector: string, page: Page) {
		super(selector, page, collectionFields);
	}
}
