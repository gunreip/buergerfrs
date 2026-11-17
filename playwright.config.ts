import { defineConfig } from '@playwright/test';

export default defineConfig({
    testDir: './tests/Browser',
    workers: 1,
    timeout: 180_000,
    expect: { timeout: 15_000 },
    outputDir: 'storage/app/private/playwright/results',
    reporter: [
        ['list'],
        ['html', { outputFolder: 'storage/app/private/playwright/report', open: 'never' }],
    ],
    use: {
        baseURL: 'https://buergerfrs.test',
        storageState: 'storage/app/private/playwright/auth.json',
        ignoreHTTPSErrors: true,
        viewport: { width: 1440, height: 1000 },
        locale: 'de-DE',
        timezoneId: 'Europe/Berlin',
        colorScheme: 'dark',
    },
});
