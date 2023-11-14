import {expect, Page} from '@playwright/test';

/**
 * Default page
 */
export class ConfigurationPage {
    //Page information
    protected readonly page: Page;
    protected pageUrl: string;

    protected moduleName: string;
    //Configuration page
    protected configurationPageUrl: string = 'modules/MODULENAME/tests/functionnal/apply_case.php?test_case=';

    public constructor(page:Page,moduleName: string) {
        this.page = page;
        this.moduleName = moduleName; //@TODO validate module name
        this.configurationPageUrl = this.configurationPageUrl.replace('MODULENAME', this.moduleName);
    }

    /**
     * Apply the required configuration before the execution of the test
     * @param configurationCode
     */
    public async applyConfiguration(configurationCode: string) {
        await this.page.goto(this.configurationPageUrl + configurationCode);
        //Check that no warning are displayed on the configuration page
        await expect(await this.page.locator('.warning').count()).toEqual(0);
    }
}