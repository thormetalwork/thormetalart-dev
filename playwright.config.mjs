import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
    testDir: './tests/e2e',
    outputDir: './test-results/playwright',
    timeout: 45_000,
    expect: { timeout: 8_000 },
    fullyParallel: false,
    retries: 1,
    reporter: [['line'], ['html', { outputFolder: 'playwright-report', open: 'never' }]],
    use: {
        baseURL: process.env.TMA_BASE_URL || 'https://dev.thormetalart.com',
        ignoreHTTPSErrors: true,
        screenshot: 'only-on-failure',
        trace: 'retain-on-failure',
    },
    projects: [
        {
            name: 'desktop',
            use: { ...devices['Desktop Chrome'], viewport: { width: 1440, height: 900 } },
        },
        {
            name: 'mobile',
            use: { ...devices['Pixel 7'], viewport: { width: 390, height: 844 } },
        },
    ],
});
