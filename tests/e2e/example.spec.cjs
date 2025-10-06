// @ts-check
const { test, expect } = require('@playwright/test');

test('Has Login', async ({ page }) => {
  await page.goto('./');

  //Click Sign In button
  await page.getByRole('button', { name: 'Sign In' }).click();

  // Expects page to have a heading with the name of Installation.
  await expect(page.getByRole('group', { name: 'Portal Login' })).toBeVisible();
});
