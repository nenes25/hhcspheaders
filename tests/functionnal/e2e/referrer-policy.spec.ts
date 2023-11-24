import {test, expect} from '@playwright/test';
import {ConfigurationPage} from './pages/configurationPage';

//We need to run one test after the other
test.describe.configure({mode: 'serial'});
const moduleName = 'hhcspheaders';
/**
 * Check all behavior related to REFERRER POLICY HEADERS
 */
test.describe('CHECK REFERRER POLICY HEADERS', () => {
    const headerReferrer = 'referrer-policy';
    test('REFERRER_DISABLE', async ({page}) => {
        const configurationPage = new ConfigurationPage(page,moduleName);
        await configurationPage.applyConfiguration('REFERRER_DISABLE');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        expect(headers).not.toHaveProperty('Referrer-Policy');
    });
    test('REFERRER_NO_REFERRER', async ({page}) => {
        const configurationPage = new ConfigurationPage(page,moduleName);
        await configurationPage.applyConfiguration('REFERRER_NO_REFERRER');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        expect(headers).toHaveProperty(headerReferrer);
        expect(headers[headerReferrer]).toBe('no-referrer');
    });
    test('REFERRER_NO_REFERRER_DOWNGRADE', async ({page}) => {
        const configurationPage = new ConfigurationPage(page,moduleName);
        await configurationPage.applyConfiguration('REFERRER_NO_REFERRER_DOWNGRADE');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        expect(headers).toHaveProperty(headerReferrer);
        expect(headers[headerReferrer]).toBe('no-referrer-when-downgrade');
    });
    test('REFERRER_ORIGIN', async ({page}) => {
        const configurationPage = new ConfigurationPage(page,moduleName);
        await configurationPage.applyConfiguration('REFERRER_ORIGIN');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        expect(headers).toHaveProperty(headerReferrer);
        expect(headers[headerReferrer]).toBe('origin');
    });
    test('REFERRER_ORIGIN_CROSS', async ({page}) => {
        const configurationPage = new ConfigurationPage(page,moduleName);
        await configurationPage.applyConfiguration('REFERRER_ORIGIN_CROSS');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        expect(headers).toHaveProperty(headerReferrer);
        expect(headers[headerReferrer]).toBe('origin-when-cross-origin');
    });
    test('REFERRER_ORIGIN_SAME', async ({page}) => {
        const configurationPage = new ConfigurationPage(page,moduleName);
        await configurationPage.applyConfiguration('REFERRER_ORIGIN_SAME');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        expect(headers).toHaveProperty(headerReferrer);
        expect(headers[headerReferrer]).toBe('same-origin');
    });
    test('REFERRER_ORIGIN_STRICT', async ({page}) => {
        const configurationPage = new ConfigurationPage(page,moduleName);
        await configurationPage.applyConfiguration('REFERRER_ORIGIN_STRICT');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        expect(headers[headerReferrer]).toBe('strict-origin');
    });
    test('REFERRER_ORIGIN_STRICT_CROSS', async ({page}) => {
        const configurationPage = new ConfigurationPage(page,moduleName);
        await configurationPage.applyConfiguration('REFERRER_ORIGIN_STRICT_CROSS');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        expect(headers).toHaveProperty(headerReferrer);
        expect(headers[headerReferrer]).toBe('strict-origin-when-cross-origin');
    });
    test('REFERRER_UNSAFE', async ({page}) => {
        const configurationPage = new ConfigurationPage(page,moduleName);
        await configurationPage.applyConfiguration('REFERRER_UNSAFE');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        expect(headers).toHaveProperty(headerReferrer);
        expect(headers[headerReferrer]).toBe('unsafe-url');
    });
});