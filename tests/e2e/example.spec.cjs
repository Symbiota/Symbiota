// @ts-check
const { test, expect } = require('@playwright/test');

test('Has Login', async ({ page }) => {
  await page.goto('./');

  //Click Sign In button
  await page.getByText('Sign In').click({force: true});

  // Expects page to have a heading with the name of Installation.
  await expect(page.getByText('Portal Login')).toBeVisible();
});
