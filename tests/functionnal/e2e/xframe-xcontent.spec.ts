import {test, expect} from '@playwright/test';
import {ConfigurationPage} from './pages/configurationPage';

//We need to run one test after the other
test.describe.configure({mode: 'serial'});
const moduleName = 'hhcspheaders';
/**
 * Check all behavior related to XFRAME / XCONTENT TAGS
 */
test.describe('CHECK XFRAME AND XCONTENT TAGS', () => {
    test('FO_XCONTENT_XFRAME_DISABLE', async ({page}) => {
        const configurationPage = new ConfigurationPage(page,moduleName);
        await configurationPage.applyConfiguration('FO_XCONTENT_XFRAME_DISABLE');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        expect(headers).not.toHaveProperty('x-frame-options');
        expect(headers).not.toHaveProperty('x-content-type-options');
    });
    
    test('FO_XFRAME_DENY', async ({page}) => {
        const configurationPage = new ConfigurationPage(page,moduleName);
        await configurationPage.applyConfiguration('FO_XFRAME_DENY');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        const headerXFrameOptions = 'x-frame-options';
        expect(headers).toHaveProperty(headerXFrameOptions);
        expect(headers[headerXFrameOptions]).toBe('DENY');
    });

    test('FO_XFRAME_SAMEORIGIN', async ({page}) => {
        const configurationPage = new ConfigurationPage(page,moduleName);
        await configurationPage.applyConfiguration('FO_XFRAME_SAMEORIGIN');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        const headerXFrameOptions = 'x-frame-options';
        expect(headers).toHaveProperty(headerXFrameOptions);
        expect(headers[headerXFrameOptions]).toBe('SAMEORIGIN');
    });

    test('FO_XCONTENT_ENABLE', async ({page}) => {
        const configurationPage = new ConfigurationPage(page,moduleName);
        await configurationPage.applyConfiguration('FO_XCONTENT_ENABLE');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        const headerXContentType = 'x-content-type-options';
        expect(headers).toHaveProperty(headerXContentType);
        expect(headers[headerXContentType]).toBe('nosniff');
    });

    test('FO_XCONTENT_XFRAME_ENABLE', async ({page}) => {
        const configurationPage = new ConfigurationPage(page,moduleName);
        await configurationPage.applyConfiguration('FO_XCONTENT_XFRAME_ENABLE');
        const response = await page.goto('');
        const headers = await response.allHeaders();
        const headerXContentType = 'x-content-type-options';
        const headerXFrameOptions ='x-frame-options';
        expect(headers).toHaveProperty(headerXFrameOptions);
        expect(headers[headerXFrameOptions]).toBe('DENY');
        expect(headers).toHaveProperty(headerXContentType);
        expect(headers[headerXContentType]).toBe('nosniff');
    });
});