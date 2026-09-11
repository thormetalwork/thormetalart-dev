const { test, expect } = require('@playwright/test');

const routes = [
    '/',
    '/custom-metal-gates-miami/',
    '/metal-railings-miami/',
    '/metal-fences-miami/',
    '/custom-metal-furniture-miami/',
    '/metal-stairs-miami/',
    '/art-commissions/',
    '/how-we-work/',
    '/contact/',
    '/blog/',
    '/portfolio/',
];

const serviceRoutes = routes.slice(1, 6);

for (const language of ['en', 'es']) {
    for (const route of routes) {
        const localizedRoute = language === 'es' ? `/es${route}` : route;
        const slug = route === '/' ? 'home' : route.replaceAll('/', '');

        test(`${language} ${route} renders without structural regressions`, async ({ page }, testInfo) => {
            const response = await page.goto(localizedRoute, { waitUntil: 'domcontentloaded' });
            expect(response?.status()).toBe(200);
            await expect(page.locator('h1')).toHaveCount(1);

            const hasHorizontalOverflow = await page.evaluate(
                () => document.documentElement.scrollWidth > document.documentElement.clientWidth + 1
            );
            expect(hasHorizontalOverflow).toBe(false);

            await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
            await page.waitForTimeout(200);
            const brokenImages = await page
                .locator('img[src]')
                .evaluateAll((images) =>
                    images.filter((image) => image.complete && image.naturalWidth === 0).map((image) => image.src)
                );
            expect(brokenImages).toEqual([]);

            await page.screenshot({
                path: `test-results/evidence/${testInfo.project.name}/${language}-${slug}.png`,
                fullPage: true,
            });
        });
    }
}

test('canonical service navigation exposes five reachable links', async ({ page }, testInfo) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    const navigation =
        testInfo.project.name === 'mobile'
            ? page.locator('.tma-main-nav--mobile')
            : page.locator('.tma-main-nav--desktop');

    if (testInfo.project.name === 'mobile') {
        await navigation.locator(':scope > summary').click();
        await navigation.locator('.tma-services-menu > summary').click();
    } else {
        await navigation.locator('.tma-services-menu').hover();
    }

    const serviceLinks = navigation.locator('.tma-services-submenu a');
    await expect(serviceLinks).toHaveCount(5);
    for (const link of await serviceLinks.all()) {
        await expect(link).toBeVisible();
        const href = await link.getAttribute('href');
        const response = await page.request.get(href);
        expect(response.status()).toBe(200);
    }
});

for (const route of serviceRoutes) {
    test(`${route} owns one service shell`, async ({ page }) => {
        await page.goto(route, { waitUntil: 'domcontentloaded' });
        await expect(page.locator('.tma-page-shell')).toHaveCount(1);
        await expect(page.locator('.tma-service-hero')).toHaveCount(1);
        await expect(page.locator('.tma-breadcrumbs')).toHaveCount(1);
        await expect(page.locator('h1')).toHaveCount(1);
    });
}

test('contact form exposes the complete canonical catalog', async ({ page }) => {
    await page.goto('/contact/', { waitUntil: 'domcontentloaded' });
    const values = await page
        .locator('#tma_service option')
        .evaluateAll((options) => options.map((option) => option.value));
    expect(values).toEqual(['', 'custom-gates', 'railings', 'fences', 'furniture', 'stairs', 'metal-art', 'other']);
});
