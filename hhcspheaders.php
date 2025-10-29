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
        'ENABLE_REFERRER',
        'REFERRER_POLICY',
    ];

    public function __construct()
    {
        $this->name = 'hhcspheaders';
        $this->tab = 'others';
        $this->version = '0.5.0';
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
        include dirname(__FILE__) . '/sql/install.php';

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
        include dirname(__FILE__) . '/sql/uninstall.php';

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

        // REFERRER POLICY
        if (Configuration::get($this->configPrefix . 'ENABLE_REFERRER')) {
            header('Referrer-Policy: ' . Configuration::get($this->configPrefix . 'REFERRER_POLICY'));
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
                        'type' => 'html',
                        'label' => $this->l('CSP Violations Statistics'),
                        'name' => $this->configPrefix . 'VIOLATIONS_STATS',
                        'html_content' => $this->getViolationsStats(),
                        'tab' => 'logs',
                    ],
                    [
                        'type' => 'html',
                        'label' => $this->l('Recent Violations'),
                        'name' => $this->configPrefix . 'VIOLATIONS_LIST',
                        'html_content' => $this->getRecentViolations(),
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
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable Referrer-Policy'),
                        'name' => $this->configPrefix . 'ENABLE_REFERRER',
                        'required' => true,
                        'class' => 't',
                        'is_bool' => true,
                        'hint' => sprintf(
                            $this->l('Enable Referrer-Policy see %s for more details'),
                            '<a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referrer-Policy">
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
                        'label' => $this->l('Referrer-Policy option value'),
                        'name' => $this->configPrefix . 'REFERRER_POLICY',
                        'required' => true,
                        'class' => 't radio-select-csp-mode',
                        'values' => [
                            [
                                'id' => 'no-referrer',
                                'value' => 'no-referrer',
                                'label' => 'no-referrer',
                            ],
                            [
                                'id' => 'no-referrer-when-downgrade',
                                'value' => 'no-referrer-when-downgrade',
                                'label' => 'no-referrer-when-downgrade',
                            ],
                            [
                                'id' => 'origin',
                                'value' => 'origin',
                                'label' => 'origin',
                            ],
                            [
                                'id' => 'origin-when-cross-origin',
                                'value' => 'origin-when-cross-origin',
                                'label' => 'origin-when-cross-origin',
                            ],
                            [
                                'id' => 'same-origin',
                                'value' => 'same-origin',
                                'label' => 'same-origin',
                            ],
                            [
                                'id' => 'strict-origin',
                                'value' => 'strict-origin',
                                'label' => 'strict-origin',
                            ],
                            [
                                'id' => 'strict-origin-when-cross-origin',
                                'value' => 'strict-origin-when-cross-origin',
                                'label' => 'strict-origin-when-cross-origin',
                            ],
                            [
                                'id' => 'unsafe-url',
                                'value' => 'unsafe-url',
                                'label' => 'unsafe-url',
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
     * Process form submit or violations cleanup
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

        if (Tools::getValue('clear_violations')) {
            require_once _PS_MODULE_DIR_ . 'hhcspheaders/classes/CspViolation.php';

            if ($this->clearResolvedViolations()) {
                return $this->displayConfirmation($this->l('Resolved violations cleared successfully'));
            } else {
                return $this->displayError($this->l('Unable to clear violations'));
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
        require_once _PS_MODULE_DIR_ . 'hhcspheaders/classes/CspViolation.php';

        $data = file_get_contents('php://input');
        if ($data = json_decode($data, true)) {
            if (isset($data['csp-report'])) {
                $reportData = $data['csp-report'];

                $documentUri = isset($reportData['document-uri']) ? $reportData['document-uri'] : '';
                $blockedUri = isset($reportData['blocked-uri']) ? $reportData['blocked-uri'] : '';
                $violatedDirective = isset($reportData['violated-directive']) ? $reportData['violated-directive'] : '';

                $existingViolation = CspViolation::findExisting($documentUri, $blockedUri, $violatedDirective);

                if ($existingViolation) {
                    $existingViolation->incrementOccurrence();
                } else {
                    CspViolation::createFromReport($reportData);
                }
            }
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
     * Get CSP violations statistics
     *
     * @return string
     */
    protected function getViolationsStats(): string
    {
        require_once _PS_MODULE_DIR_ . 'hhcspheaders/classes/CspViolation.php';

        $stats = CspViolation::getStatistics();

        $html = '<div class="alert alert-info">';
        $html .= '<h4>' . $this->l('CSP Violations Summary') . '</h4>';
        $html .= '<ul>';
        $html .= '<li><strong>' . $this->l('Total violations:') . '</strong> ' . (int) $stats['total'] . '</li>';
        $html .= '<li><strong>' . $this->l('Unresolved violations:') . '</strong> ' . (int) $stats['unresolved'] . '</li>';
        $html .= '<li><strong>' . $this->l('Total occurrences:') . '</strong> ' . (int) $stats['total_occurrences'] . '</li>';
        if (!empty($stats['most_violated_directive'])) {
            $html .= '<li><strong>' . $this->l('Most violated directive:') . '</strong> '
                . htmlspecialchars($stats['most_violated_directive']) . '</li>';
        }
        $html .= '</ul>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Get recent CSP violations
     *
     * @return string
     */
    protected function getRecentViolations(): string
    {
        require_once _PS_MODULE_DIR_ . 'hhcspheaders/classes/CspViolation.php';

        $violations = CspViolation::getUnresolvedViolations(20);

        if (empty($violations)) {
            return '<div class="alert alert-success">' . $this->l('No violations detected yet.') . '</div>';
        }

        $clearLink = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name
            . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&clear_violations=1';

        $html = '<p class="alert alert-info">';
        $html .= $this->l('Showing the 20 most recent unresolved violations.');
        $html .= ' <a href="' . $clearLink . '" class="btn btn-sm btn-danger" onclick="return confirm(\''
            . $this->l('Are you sure you want to clear all resolved violations?') . '\');">';
        $html .= '<i class="icon-trash"></i> ' . $this->l('Clear Resolved Violations');
        $html .= '</a>';
        $html .= '</p>';

        $html .= '<div style="max-height: 500px; overflow-y: auto;">';
        $html .= '<table class="table table-bordered table-striped">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>' . $this->l('Directive') . '</th>';
        $html .= '<th>' . $this->l('Blocked URI') . '</th>';
        $html .= '<th>' . $this->l('Document URI') . '</th>';
        $html .= '<th>' . $this->l('Occurrences') . '</th>';
        $html .= '<th>' . $this->l('Last Seen') . '</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($violations as $violation) {
            $html .= '<tr>';
            $html .= '<td><code>' . htmlspecialchars($violation['violated_directive']) . '</code></td>';
            $html .= '<td style="word-break: break-all; max-width: 250px;">'
                . htmlspecialchars($violation['blocked_uri']) . '</td>';
            $html .= '<td style="word-break: break-all; max-width: 250px;">'
                . htmlspecialchars($violation['document_uri']) . '</td>';
            $html .= '<td><span class="badge badge-warning">' . (int) $violation['occurrences'] . '</span></td>';
            $html .= '<td>' . htmlspecialchars($violation['last_seen']) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Clear all resolved violations from database
     *
     * @return bool
     */
    protected function clearResolvedViolations(): bool
    {
        $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'hhcspheaders_violations` WHERE is_resolved = 1';

        return Db::getInstance()->execute($sql);
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
