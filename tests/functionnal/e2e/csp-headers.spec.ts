import {test, expect} from '@playwright/test';
import {ConfigurationPage} from './pages/configurationPage';

test.describe.configure({mode: 'serial'});
const moduleName = 'hhcspheaders';

/**
 * Check all behavior related to CSP HEADERS
 */
test.describe('CHECK CSP HEADERS', () => {
    
    test('CSP_DISABLE_FRONT', async ({page}) => {
        const configurationPage = new ConfigurationPage(page, moduleName);
        await configurationPage.applyConfiguration('CSP_DISABLE_FRONT');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        expect(headers).not.toHaveProperty('content-security-policy');
        expect(headers).not.toHaveProperty('content-security-policy-report-only');
    });

    test('CSP_ENABLE_FRONT_REPORT_ONLY', async ({page}) => {
        const configurationPage = new ConfigurationPage(page, moduleName);
        await configurationPage.applyConfiguration('CSP_ENABLE_FRONT_REPORT_ONLY');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        expect(headers).toHaveProperty('content-security-policy-report-only');
        expect(headers).not.toHaveProperty('content-security-policy');
    });

    test('CSP_ENABLE_FRONT_BLOCK', async ({page}) => {
        const configurationPage = new ConfigurationPage(page, moduleName);
        await configurationPage.applyConfiguration('CSP_ENABLE_FRONT_BLOCK');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        expect(headers).toHaveProperty('content-security-policy');
        expect(headers).not.toHaveProperty('content-security-policy-report-only');
    });

    test('CSP_ENABLE_FRONT_BOTH', async ({page}) => {
        const configurationPage = new ConfigurationPage(page, moduleName);
        await configurationPage.applyConfiguration('CSP_ENABLE_FRONT_BOTH');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        expect(headers).toHaveProperty('content-security-policy');
        expect(headers).toHaveProperty('content-security-policy-report-only');
    });

    test('CSP_WITH_DEFAULT_SRC', async ({page}) => {
        const configurationPage = new ConfigurationPage(page, moduleName);
        await configurationPage.applyConfiguration('CSP_WITH_DEFAULT_SRC');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        const cspHeader = headers['content-security-policy'];
        expect(cspHeader).toContain("default-src 'self'");
    });

    test('CSP_WITH_SCRIPT_SRC', async ({page}) => {
        const configurationPage = new ConfigurationPage(page, moduleName);
        await configurationPage.applyConfiguration('CSP_WITH_SCRIPT_SRC');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        const cspHeader = headers['content-security-policy'];
        expect(cspHeader).toContain("script-src 'self'");
    });

    test('CSP_WITH_STYLE_SRC', async ({page}) => {
        const configurationPage = new ConfigurationPage(page, moduleName);
        await configurationPage.applyConfiguration('CSP_WITH_STYLE_SRC');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        const cspHeader = headers['content-security-policy'];
        expect(cspHeader).toContain("style-src 'self' 'unsafe-inline'");
    });

    test('CSP_WITH_IMG_SRC', async ({page}) => {
        const configurationPage = new ConfigurationPage(page, moduleName);
        await configurationPage.applyConfiguration('CSP_WITH_IMG_SRC');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        const cspHeader = headers['content-security-policy'];
        expect(cspHeader).toContain("img-src *");
    });

    test('CSP_WITH_FONT_SRC', async ({page}) => {
        const configurationPage = new ConfigurationPage(page, moduleName);
        await configurationPage.applyConfiguration('CSP_WITH_FONT_SRC');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        const cspHeader = headers['content-security-policy'];
        expect(cspHeader).toContain("font-src 'self'");
    });

    test('CSP_WITH_CONNECT_SRC', async ({page}) => {
        const configurationPage = new ConfigurationPage(page, moduleName);
        await configurationPage.applyConfiguration('CSP_WITH_CONNECT_SRC');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        const cspHeader = headers['content-security-policy'];
        expect(cspHeader).toContain("connect-src 'self'");
    });

    test('CSP_WITH_FRAME_SRC', async ({page}) => {
        const configurationPage = new ConfigurationPage(page, moduleName);
        await configurationPage.applyConfiguration('CSP_WITH_FRAME_SRC');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        const cspHeader = headers['content-security-policy'];
        expect(cspHeader).toContain("frame-src 'self'");
    });

    test('CSP_WITH_OBJECT_SRC', async ({page}) => {
        const configurationPage = new ConfigurationPage(page, moduleName);
        await configurationPage.applyConfiguration('CSP_WITH_OBJECT_SRC');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        const cspHeader = headers['content-security-policy'];
        expect(cspHeader).toContain("object-src 'none'");
    });

    test('CSP_WITH_MEDIA_SRC', async ({page}) => {
        const configurationPage = new ConfigurationPage(page, moduleName);
        await configurationPage.applyConfiguration('CSP_WITH_MEDIA_SRC');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        const cspHeader = headers['content-security-policy'];
        expect(cspHeader).toContain("media-src 'self'");
    });

    test('CSP_COMPLETE_POLICY', async ({page}) => {
        const configurationPage = new ConfigurationPage(page, moduleName);
        await configurationPage.applyConfiguration('CSP_COMPLETE_POLICY');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        const cspHeader = headers['content-security-policy'];
        expect(cspHeader).toContain("default-src 'self'");
        expect(cspHeader).toContain("script-src 'self'");
        expect(cspHeader).toContain("style-src 'self' 'unsafe-inline'");
        expect(cspHeader).toContain("img-src *");
    });
});
