import {expect, Page} from '@playwright/test';

export class ViolationsAdminPage {
    private readonly page: Page;
    private readonly adminEmail: string;
    private readonly adminPassword: string;
    private readonly fixtureUrl: string;

    private readonly adminPath: string;

    public constructor(page: Page, moduleName: string) {
        this.page = page;
        this.adminEmail = process.env.ADMIN_EMAIL ?? 'demo@prestashop.com';
        this.adminPassword = process.env.ADMIN_PASSWORD ?? 'prestashop_demo';
        this.adminPath = process.env.ADMIN_PATH ?? '/admin';
        this.fixtureUrl = `modules/${moduleName}/tests/functionnal/manage_violations.php`;
    }

    public async setupViolations(): Promise<void> {
        await this.page.goto(this.fixtureUrl + '?action=setup');
        await expect(this.page.locator('.warning')).toHaveCount(0);
    }

    public async cleanupViolations(): Promise<void> {
        await this.page.goto(this.fixtureUrl + '?action=cleanup');
    }

    public async loginToAdmin(): Promise<void> {
        await this.page.goto(this.adminPath);
        await this.page.fill('#email', this.adminEmail);
        await this.page.fill('#passwd', this.adminPassword);
        await this.page.click('#submit_login');
        await this.page.waitForURL(new RegExp(this.adminPath));
    }

    public async navigateToModuleConfig(): Promise<void> {
        const token = await this.page.evaluate((): string => {
            const link = document.querySelector('a[href*="_token="]');
            if (!link) return '';
            const m = link.getAttribute('href')!.match(/_token=([^&]+)/);
            return m ? m[1] : '';
        });
        await this.page.goto(
            `${this.adminPath}/improve/modules/manage/action/configure/hhcspheaders?_token=${token}`
        );
        await this.activateLogsTab();
    }

    public async activateLogsTab(): Promise<void> {
        await this.page.locator('a[href="#logs"]').click();
        await this.page.locator('#logs').waitFor({state: 'visible'});
    }

    public getViolationRows() {
        return this.page.locator('table.table tbody tr');
    }

    public getResolveButton(index: number) {
        return this.getViolationRows().nth(index).locator('a.btn-success');
    }

    public getMarkAllButton() {
        return this.page.locator('a.btn-success[href*="resolve_all_violations"]');
    }
}
