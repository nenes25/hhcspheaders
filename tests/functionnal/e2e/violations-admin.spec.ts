import {test, expect} from '@playwright/test';
import {ViolationsAdminPage} from './pages/violationsAdminPage';

test.describe.configure({mode: 'serial'});
const moduleName = 'hhcspheaders';

test.describe('CHECK VIOLATIONS RESOLUTION', () => {

    test.afterAll(async ({browser}) => {
        const ctx = await browser.newContext();
        const page = await ctx.newPage();
        const vp = new ViolationsAdminPage(page, moduleName);
        await vp.cleanupViolations();
        await ctx.close();
    });

    test('RESOLVE_SINGLE_VIOLATION', async ({page}) => {
        const vp = new ViolationsAdminPage(page, moduleName);
        await vp.setupViolations();
        await vp.loginToAdmin();
        await vp.navigateToModuleConfig();

        await expect(vp.getViolationRows()).toHaveCount(2);

        await vp.getResolveButton(0).click();
        await vp.activateLogsTab();

        await expect(vp.getViolationRows()).toHaveCount(1);
    });

    test('RESOLVE_ALL_VIOLATIONS', async ({page}) => {
        const vp = new ViolationsAdminPage(page, moduleName);
        await vp.setupViolations();
        await vp.loginToAdmin();
        await vp.navigateToModuleConfig();

        page.once('dialog', dialog => dialog.accept());
        await vp.getMarkAllButton().click();
        await vp.activateLogsTab();

        await expect(
            page.locator('div.alert-success').filter({hasText: 'No violations detected'})
        ).toBeVisible();
    });
});
