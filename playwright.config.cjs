// @ts-check
const { defineConfig, devices } = require('@playwright/test');

const APP_URL = 'http://localhost:8080';

/**
 * Read environment variables from file.
 * https://github.com/motdotla/dotenv
 */

/**
 * @see https://playwright.dev/docs/test-configuration
 */
module.exports = defineConfig({
  testDir: './tests/e2e',
  /* Run tests in files in parallel */
  fullyParallel: false,
  /* Fail the build on CI if you accidentally left test.only in the source code. */
  forbidOnly: !!process.env.CI,
  /* Retry on CI only */
  retries: process.env.CI ? 2 : 0,
  /* Opt out of parallel tests on CI. */
  workers: process.env.CI ? 1 : undefined,
  /* Reporter to use. See https://playwright.dev/docs/test-reporters */
  reporter: [
		['html', { open: 'never' } ]
  ],
  /* Shared settings for all the projects below. See https://playwright.dev/docs/api/class-testoptions. */
  use: {
    /* Base URL to use in actions like `await page.goto('/')`. */
    baseURL: APP_URL,

    /* Collect trace when retrying the failed test. See https://playwright.dev/docs/trace-viewer */
    trace: 'on-first-retry',
  },

  /* Configure projects for major browsers */
  projects: [
    {
      name: 'chromium',
      use: { 
	    ...devices['Desktop Chrome'],
		viewport: { width: 1920, height: 1080 },
	  },
    },

    {
      name: 'firefox',
      use: { 
	    ...devices['Desktop Firefox'],
		viewport: { width: 1920, height: 1080 }
	  },
    },

    {
      name: 'webkit',
      use: { 
		...devices['Desktop Safari'],
	    viewport: { width: 1920, height: 1080 }
	  },
    },
  ],
});
