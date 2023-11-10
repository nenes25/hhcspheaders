<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file docs/licenses/LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@h-hennes.fr so we can send you a copy immediately.
 *
 * @author    Hervé HENNES <contact@h-hhennes.fr>
 * @copyright since 2022 Hervé HENNES
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License ("AFL") v. 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Hhcspheaders extends Module
{
    /** @var string */
    public const CSP_MODE_REPORT_ONLY = 'REPORT-ONLY';
    /** @var string */
    public const CSP_MODE_BLOCK = 'BLOCK';
    /** @var string */
    public const CSP_MODE_BOTH = 'BOTH';
    /** @var string */
    public const XFRAME_OPTION_DENY = 'DENY';
    /** @var string */
    public const XFRAME_OPTION_SAMEORIGIN = 'SAMEORIGIN';

    /**
     * @var string
     */
    protected $configPrefix;

    /**
     * Liste des configurations
     *
     * @var string[]
     */
    protected $configFields = [
        'ENABLE_FRONT',
        'ENABLE_BACK',
        'MODE',
        'CSP_DEFAULT_SRC',
        'CSP_SCRIPT_SRC',
        'CSP_STYLE_SRC',
        'CSP_IMG_SRC',
        'CSP_CONNECT_SRC',
        'CSP_FONT_SRC',
        'CSP_OBJECT_SRC',
        'CSP_MEDIA_SRC',
        'CSP_FRAME_SRC',
        'LOG_MAX_FILE_SIZE',
        'ENABLE_XFRAME',
        'XFRAME_OPTION',
        'ENABLE_XCONTENT',
    ];

    public function __construct()
    {
        $this->name = 'hhcspheaders';
        $this->tab = 'others';
        $this->version = '0.4.0';
        $this->author = 'hhennes';
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Hh csp headers');
        $this->description = $this->l('Add csp headers to your website');
        $this->configPrefix = strtoupper($this->name) . '_';
    }

    /**
     * Module installation
     *
     * @return bool
     */
    public function install()
    {
        if (
            !parent::install()
            || !$this->registerHook(['actionControllerInitBefore', 'actionAdminControllerSetMedia'])
            || !Configuration::updateValue('HHCSPHEADERS_LOG_MAX_FILE_SIZE', 10)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Module uninstall
     *
     * @return bool
     */
    public function uninstall()
    {
        foreach ($this->configFields as $config) {
            Configuration::deleteByName($this->configPrefix . $config);
        }

        return parent::uninstall();
    }

    /**
     * Register javascript to preview CSP when configuring module in back office
     *
     * @param array $params
     *
     * @return void
     */
    public function hookActionAdminControllerSetMedia(array $params): void
    {
        if (
            $this->context->controller instanceof AdminModulesController
            && Tools::getValue('configure') == $this->name
        ) {
            $this->context->controller->addJS(
                $this->_path . 'views/js/admin.js'
            );
        }
    }

    /**
     * CSP will be generated if necessary before controller initialisation
     *
     * @param array $params
     *
     * @return void
     *
     * @throws PrestaShopException
     */
    public function hookActionControllerInitBefore(array $params): void
    {
        // CSP
        if (
            (
                Configuration::get($this->configPrefix . 'ENABLE_FRONT')
                && $this->context->controller instanceof FrontController
            )
            || (
                Configuration::get($this->configPrefix . 'ENABLE_BACK')
                && $this->context->controller instanceof AdminController
            )
        ) {
            $cspHeader = $this->getCspHeaders();
            if (!empty($cspHeader)) {
                if (Configuration::get($this->configPrefix . 'MODE') != self::CSP_MODE_REPORT_ONLY) {
                    header('Content-Security-Policy: ' . $cspHeader);
                }
                if (Configuration::get($this->configPrefix . 'MODE') != self::CSP_MODE_BLOCK) {
                    $cspHeader .= ' report-uri ' . $this->getCspReportUri();
                    header('Content-Security-Policy-Report-Only: ' . $cspHeader);
                }
            }
        }

        // X FRAME
        if (Configuration::get($this->configPrefix . 'ENABLE_XFRAME')) {
            header('X-Frame-Options: ' . Configuration::get($this->configPrefix . 'XFRAME_OPTION'));
        }
        // X Content
        if (Configuration::get($this->configPrefix . 'ENABLE_XCONTENT')) {
            header('X-Content-Type-Options: nosniff');
        }
    }

    /**
     * Module configuration
     *
     * @return string
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getContent()
    {
        $html = '';
        $html .= $this->postProcess();
        $html .= $this->renderForm();

        return $html;
    }

    /**
     * Manage configuration form
     *
     * @return string
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function renderForm(): string
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Configuration'),
                    'icon' => 'icon-cogs',
                ],
                'tabs' => [
                    'general' => $this->l('General configuration'),
                    'others' => $this->l('Other Headers'),
                    'logs' => $this->l('Logs'),
                ],
                'description' => sprintf(
                    $this->l('Implement content security policy in Prestashop see %s for details'),
                    '<a href="https://content-security-policy.com/" target="_blank">https://content-security-policy.com/</a>'
                ),
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable Csp headers on front office'),
                        'name' => $this->configPrefix . 'ENABLE_FRONT',
                        'required' => true,
                        'class' => 't',
                        'is_bool' => true,
                        'hint' => $this->l('Enable Csp headers on front office'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                        'tab' => 'general',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable Csp headers on back office'),
                        'name' => $this->configPrefix . 'ENABLE_BACK',
                        'required' => true,
                        'class' => 't',
                        'is_bool' => true,
                        'hint' => $this->l('Enable Csp headers on back office'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                        'tab' => 'general',
                    ],
                    [
                        'type' => 'radio',
                        'label' => $this->l('Cps Mode'),
                        'name' => $this->configPrefix . 'MODE',
                        'required' => true,
                        'class' => 't radio-select-csp-mode',
                        'values' => [
                            [
                                'id' => 'report-only',
                                'value' => self::CSP_MODE_REPORT_ONLY,
                                'label' => $this->l('Report Only'),
                            ],
                            [
                                'id' => 'block',
                                'value' => self::CSP_MODE_BLOCK,
                                'label' => $this->l('Block only'),
                            ],
                            [
                                'id' => 'both',
                                'value' => self::CSP_MODE_BOTH,
                                'label' => $this->l('Both'),
                            ],
                        ],
                        'tab' => 'general',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Default Src'),
                        'name' => $this->configPrefix . 'CSP_DEFAULT_SRC',
                        'class' => 'csp-eval-argument-default-src',
                        'hint' => $this->l('The default-src directive defines the default policy for fetching resources such as JavaScript, Images, CSS, Fonts, AJAX requests, Frames, HTML5 Media.'),
                        'tab' => 'general',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Script Src'),
                        'name' => $this->configPrefix . 'CSP_SCRIPT_SRC',
                        'class' => 'csp-eval-argument-script-src',
                        'hint' => $this->l('Defines valid sources of JavaScript.'),
                        'tab' => 'general',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Style Src'),
                        'name' => $this->configPrefix . 'CSP_STYLE_SRC',
                        'class' => 'csp-eval-argument-style-src',
                        'hint' => $this->l('Defines valid sources of stylesheets or CSS.'),
                        'placeholder' => "Suggested value : 'unsafe-inline'  " . $this->context->link->getBaseLink(),
                        'tab' => 'general',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Img Src'),
                        'name' => $this->configPrefix . 'CSP_IMG_SRC',
                        'class' => 'csp-eval-argument-img-src',
                        'hint' => $this->l('Defines valid sources of images.'),
                        'placeholder' => 'Suggested value : *',
                        'tab' => 'general',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Connect Src'),
                        'name' => $this->configPrefix . 'CSP_CONNECT_SRC',
                        'class' => 'csp-eval-argument-connect-src',
                        'hint' => $this->l('Applies to XMLHttpRequest (AJAX), WebSocket, fetch(), a ping or EventSource.'),
                        'tab' => 'general',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Font Src'),
                        'name' => $this->configPrefix . 'CSP_FONT_SRC',
                        'class' => 'csp-eval-argument-font-src',
                        'hint' => $this->l('Defines valid sources of font resources (loaded via @font-face).'),
                        'tab' => 'general',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Object Src'),
                        'name' => $this->configPrefix . 'CSP_OBJECT_SRC',
                        'class' => 'csp-eval-argument-object-src',
                        'hint' => $this->l('Defines valid sources of font resources (loaded via @font-face).'),
                        'tab' => 'general',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Media Src'),
                        'name' => $this->configPrefix . 'CSP_MEDIA_SRC',
                        'class' => 'csp-eval-argument-media-src',
                        'hint' => $this->l('Defines valid sources of audio and video, eg HTML5 <audio>, <video> elements.'),
                        'tab' => 'general',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Frame Src'),
                        'name' => $this->configPrefix . 'CSP_FRAME_SRC',
                        'class' => 'csp-eval-argument-frame-src',
                        'hint' => $this->l('Defines valid sources for loading frames.'),
                        'tab' => 'general',
                    ],
                    [
                        'type' => 'html',
                        'label' => $this->l('Preview generated csp'),
                        'html_content' => $this->getPreviewContent(),
                        'name' => $this->configPrefix . 'GENERATED_CSP',
                        'tab' => 'general',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Log max file size'),
                        'hint' => sprintf(
                            $this->l('Log max file size in Mb, (current size %s Mb)'),
                            $this->getLogFileSize()
                        ),
                        'name' => $this->configPrefix . 'LOG_MAX_FILE_SIZE',
                        'tab' => 'logs',
                    ],
                    [
                        'type' => 'html',
                        'label' => $this->l('Log content'),
                        'name' => $this->configPrefix . 'LOG_CONTENT',
                        'html_content' => $this->getLogContent(),
                        'tab' => 'logs',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable X frame option'),
                        'name' => $this->configPrefix . 'ENABLE_XFRAME',
                        'required' => true,
                        'class' => 't',
                        'is_bool' => true,
                        'hint' => sprintf(
                            $this->l('Enable X frame option see %s for more details'),
                            '<a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options">
                            ' . $this->l('here') . '</a>'
                        ),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                        'tab' => 'others',
                    ],
                    [
                        'type' => 'radio',
                        'label' => $this->l('X frame option value'),
                        'name' => $this->configPrefix . 'XFRAME_OPTION',
                        'required' => true,
                        'class' => 't radio-select-csp-mode',
                        'values' => [
                            [
                                'id' => 'deny',
                                'value' => self::XFRAME_OPTION_DENY,
                                'label' => $this->l('Deny'),
                            ],
                            [
                                'id' => 'same-origin',
                                'value' => self::XFRAME_OPTION_SAMEORIGIN,
                                'label' => $this->l('Same Origin'),
                            ],
                        ],
                        'tab' => 'others',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable X-Content-Type-Options'),
                        'name' => $this->configPrefix . 'ENABLE_XCONTENT',
                        'required' => true,
                        'class' => 't',
                        'is_bool' => true,
                        'hint' => sprintf(
                            $this->l('Enable X-Content-Type-Options see %s for more details'),
                            '<a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Content-Type-Options">
                            ' . $this->l('here') . '</a>'
                        ),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                        'tab' => 'others',
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'button btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->id = $this->name;
        $helper->submit_action = 'SubmitModuleConfiguration';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    /**
     * Process form submit or log file delete
     *
     * @return string|void
     */
    public function postProcess()
    {
        if (Tools::isSubmit('SubmitModuleConfiguration')) {
            foreach ($this->configFields as $key) {
                Configuration::updateValue($this->configPrefix . $key, Tools::getValue($this->configPrefix . $key));
            }

            return $this->displayConfirmation($this->l('Settings updated'));
        }

        if (Tools::getValue('delete_log_file')) {
            if ($this->deleteLogFile()) {
                return $this->displayConfirmation($this->l('Log file deleted with success'));
            } else {
                return $this->displayError($this->l('Unable to delete log file'));
            }
        }
    }

    /**
     * Get the configuration keys with the module prefix
     *
     * @return array
     */
    protected function getConfigurationKeysWithPrefix(): array
    {
        // Get configuration keys with prefix
        $configurationKeys = array_map(function ($item) {
            return $this->configPrefix . $item;
        }, $this->configFields);

        return $configurationKeys;
    }

    /**
     * Get the configured values for the form
     *
     * @return array
     *
     * @throws PrestaShopException
     */
    public function getConfigFieldsValues(): array
    {
        return Configuration::getMultiple($this->getConfigurationKeysWithPrefix());
    }

    /**
     * Public function which will log the csp content
     *
     * @return void
     */
    public function logCspContent(): void
    {
        if ($this->getLogFileSize() > Configuration::get($this->configPrefix . 'LOG_MAX_FILE_SIZE')) {
            $this->deleteLogFile();
        }
        $data = file_get_contents('php://input');
        if ($data = json_decode($data, true)) {
            file_put_contents(
                $this->getCspLogFile(),
                date('Y-m-d H:i:s') . ' ' . print_r($data['csp-report'], true) . "\n",
                FILE_APPEND
            );
        }
    }

    /**
     * Generate Csp headers
     *
     * @return string
     *
     * @throws PrestaShopException
     */
    protected function getCspHeaders(): string
    {
        $configuration = Configuration::getMultiple($this->getConfigurationKeysWithPrefix());
        $cspHeader = '';
        foreach ($configuration as $key => $value) {
            if (false !== strpos($key, '_CSP_') && !empty(trim($value))) {
                $policy = strtolower(str_replace([$this->configPrefix . 'CSP_', '_'], ['', '-'], $key));
                $cspHeader .= $policy . ' ' . $value . '; ';
            }
        }

        return $cspHeader;
    }

    /**
     * Get Csp report uri link
     *
     * @return string
     */
    protected function getCspReportUri(): string
    {
        return $this->context->link->getModuleLink($this->name, 'report');
    }

    /**
     * Get content of Csp log file or a warning message if it not exists
     *
     * @return string
     *
     * @throws PrestaShopException
     */
    protected function getLogContent(): string
    {
        $logContent = sprintf(
            $this->l('No logs to display the log file %s does not exists'),
            '<i>' . $this->getCspLogFile() . '</i>'
        );

        if (is_file($this->getCspLogFile())) {
            $logContent = '<p>' . sprintf(
                $this->l('Here is the content of the file %s click %s to delete it'),
                '<i>' . $this->getCspLogFile() . '</i>',
                '<a href="' . $this->getDeleteLogFileLink() . '">' . $this->l('here') . '</a>'
            )
                . '</p>';
            $logContent .= '<div style="max-height: 450px;overflow:scroll">';
            $logContent .= nl2br(file_get_contents($this->getCspLogFile()));
            $logContent .= '</div>';
        }

        return $logContent;
    }

    /**
     * Return current log filesize in mb
     *
     * @return float
     */
    protected function getLogFileSize(): float
    {
        if (is_file($this->getCspLogFile())) {
            return round(filesize($this->getCspLogFile()) / 1024 / 1024, 2);
        }

        return 0;
    }

    /**
     * Get delete log file link
     *
     * @return string
     *
     * @throws PrestaShopException
     */
    protected function getDeleteLogFileLink(): string
    {
        return $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name
            . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&delete_log_file=1';
    }

    /**
     * Delete log file
     *
     * @return bool
     */
    protected function deleteLogFile(): bool
    {
        if (is_file($this->getCspLogFile())) {
            return unlink($this->getCspLogFile());
        }

        return true;
    }

    /**
     * Get Csp Log file
     *
     * @return string
     */
    protected function getCspLogFile(): string
    {
        $logDir = _PS_ROOT_DIR_ . '/var/logs/' . $this->name;
        $filesystem = new \Symfony\Component\Filesystem\Filesystem();
        if (!$filesystem->exists($logDir)) {
            $filesystem->mkdir($logDir);
        }

        return $logDir . 'csp-errors.log';
    }

    /**
     * Get html preview base content
     * ( The real content will be evaluated in javascript )
     *
     * @return string
     */
    protected function getPreviewContent(): string
    {
        return '<div id="csp_preview" 
style="margin-top:10px;padding:10px;border:1px solid #CCC;background-color:#FFF6D3">'
            . $this->l('Generated csp will be displayed here')
            . '</div>';
    }
}
