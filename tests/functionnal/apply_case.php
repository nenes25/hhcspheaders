<?php

//Change here  the path with the absolute path of the config file of your testing instance
require_once '/home/herve/www/prestashop/tests/810/config/config.inc.php';
//Eof config path

$testCasesConfiguration = [
    // X-Content - Xframes Tags
    'FO_XCONTENT_XFRAME_DISABLE' => [
        'HHCSPHEADERS_ENABLE_XCONTENT' => 0,
        'HHCSPHEADERS_ENABLE_XFRAME' => 0,
    ],
    'FO_XFRAME_DENY' => [
        'HHCSPHEADERS_ENABLE_XFRAME' => 1,
        'HHCSPHEADERS_XFRAME_OPTION' => 'DENY',
    ],
    'FO_XFRAME_SAMEORIGIN' => [
        'HHCSPHEADERS_ENABLE_XFRAME' => 1,
        'HHCSPHEADERS_XFRAME_OPTION' => 'SAMEORIGIN',
    ],
    'FO_XCONTENT_ENABLE' => [
        'HHCSPHEADERS_ENABLE_XCONTENT' => 1,
    ],
    'FO_XCONTENT_XFRAME_ENABLE' => [
        'HHCSPHEADERS_ENABLE_XCONTENT' => 1,
        'HHCSPHEADERS_ENABLE_XFRAME' => 1,
        'HHCSPHEADERS_XFRAME_OPTION' => 'DENY',
    ],
    // CSP Headers Tests
    'CSP_DISABLE_FRONT' => [
        'HHCSPHEADERS_ENABLE_FRONT' => 0,
        'HHCSPHEADERS_ENABLE_BACK' => 0,
    ],
    'CSP_ENABLE_FRONT_REPORT_ONLY' => [
        'HHCSPHEADERS_ENABLE_FRONT' => 1,
        'HHCSPHEADERS_MODE' => 'REPORT-ONLY',
        'HHCSPHEADERS_CSP_DEFAULT_SRC' => "'self'",
    ],
    'CSP_ENABLE_FRONT_BLOCK' => [
        'HHCSPHEADERS_ENABLE_FRONT' => 1,
        'HHCSPHEADERS_MODE' => 'BLOCK',
        'HHCSPHEADERS_CSP_DEFAULT_SRC' => "'self'",
    ],
    'CSP_ENABLE_FRONT_BOTH' => [
        'HHCSPHEADERS_ENABLE_FRONT' => 1,
        'HHCSPHEADERS_MODE' => 'BOTH',
        'HHCSPHEADERS_CSP_DEFAULT_SRC' => "'self'",
    ],
    'CSP_WITH_DEFAULT_SRC' => [
        'HHCSPHEADERS_ENABLE_FRONT' => 1,
        'HHCSPHEADERS_MODE' => 'BLOCK',
        'HHCSPHEADERS_CSP_DEFAULT_SRC' => "'self'",
    ],
    'CSP_WITH_SCRIPT_SRC' => [
        'HHCSPHEADERS_ENABLE_FRONT' => 1,
        'HHCSPHEADERS_MODE' => 'BLOCK',
        'HHCSPHEADERS_CSP_SCRIPT_SRC' => "'self'",
    ],
    'CSP_WITH_STYLE_SRC' => [
        'HHCSPHEADERS_ENABLE_FRONT' => 1,
        'HHCSPHEADERS_MODE' => 'BLOCK',
        'HHCSPHEADERS_CSP_STYLE_SRC' => "'self' 'unsafe-inline'",
    ],
    'CSP_WITH_IMG_SRC' => [
        'HHCSPHEADERS_ENABLE_FRONT' => 1,
        'HHCSPHEADERS_MODE' => 'BLOCK',
        'HHCSPHEADERS_CSP_IMG_SRC' => "*",
    ],
    'CSP_WITH_FONT_SRC' => [
        'HHCSPHEADERS_ENABLE_FRONT' => 1,
        'HHCSPHEADERS_MODE' => 'BLOCK',
        'HHCSPHEADERS_CSP_FONT_SRC' => "'self'",
    ],
    'CSP_WITH_CONNECT_SRC' => [
        'HHCSPHEADERS_ENABLE_FRONT' => 1,
        'HHCSPHEADERS_MODE' => 'BLOCK',
        'HHCSPHEADERS_CSP_CONNECT_SRC' => "'self'",
    ],
    'CSP_WITH_FRAME_SRC' => [
        'HHCSPHEADERS_ENABLE_FRONT' => 1,
        'HHCSPHEADERS_MODE' => 'BLOCK',
        'HHCSPHEADERS_CSP_FRAME_SRC' => "'self'",
    ],
    'CSP_WITH_OBJECT_SRC' => [
        'HHCSPHEADERS_ENABLE_FRONT' => 1,
        'HHCSPHEADERS_MODE' => 'BLOCK',
        'HHCSPHEADERS_CSP_OBJECT_SRC' => "'none'",
    ],
    'CSP_WITH_MEDIA_SRC' => [
        'HHCSPHEADERS_ENABLE_FRONT' => 1,
        'HHCSPHEADERS_MODE' => 'BLOCK',
        'HHCSPHEADERS_CSP_MEDIA_SRC' => "'self'",
    ],
    'CSP_COMPLETE_POLICY' => [
        'HHCSPHEADERS_ENABLE_FRONT' => 1,
        'HHCSPHEADERS_MODE' => 'BLOCK',
        'HHCSPHEADERS_CSP_DEFAULT_SRC' => "'self'",
        'HHCSPHEADERS_CSP_SCRIPT_SRC' => "'self'",
        'HHCSPHEADERS_CSP_STYLE_SRC' => "'self' 'unsafe-inline'",
        'HHCSPHEADERS_CSP_IMG_SRC' => "*",
    ],
    'REFERRER_DISABLE' => [
        'HHCSPHEADERS_ENABLE_REFERRER' => 0,
    ],
    'REFERRER_NO_REFERRER' => [
        'HHCSPHEADERS_ENABLE_REFERRER' => 1,
        'HHCSPHEADERS_REFERRER_POLICY' => 'no-referrer',
    ],
    'REFERRER_NO_REFERRER_DOWNGRADE' => [
        'HHCSPHEADERS_ENABLE_REFERRER' => 1,
        'HHCSPHEADERS_REFERRER_POLICY' => 'no-referrer-when-downgrade',
    ],
    'REFERRER_ORIGIN' => [
        'HHCSPHEADERS_ENABLE_REFERRER' => 1,
        'HHCSPHEADERS_REFERRER_POLICY' => 'origin',
    ],
    'REFERRER_ORIGIN_CROSS' => [
        'HHCSPHEADERS_ENABLE_REFERRER' => 1,
        'HHCSPHEADERS_REFERRER_POLICY' => 'origin-when-cross-origin',
    ],
    'REFERRER_ORIGIN_SAME' => [
        'HHCSPHEADERS_ENABLE_REFERRER' => 1,
        'HHCSPHEADERS_REFERRER_POLICY' => 'same-origin',
    ],
    'REFERRER_ORIGIN_STRICT' => [
        'HHCSPHEADERS_ENABLE_REFERRER' => 1,
        'HHCSPHEADERS_REFERRER_POLICY' => 'strict-origin',
    ],
    'REFERRER_ORIGIN_STRICT_CROSS' => [
        'HHCSPHEADERS_ENABLE_REFERRER' => 1,
        'HHCSPHEADERS_REFERRER_POLICY' => 'strict-origin-when-cross-origin',
    ],
    'REFERRER_UNSAFE' => [
        'HHCSPHEADERS_ENABLE_REFERRER' => 1,
        'HHCSPHEADERS_REFERRER_POLICY' => 'unsafe-url',
    ],
];

// Get the case to run
$testCase = strip_tags($_GET['test_case']);

if (array_key_exists($testCase, $testCasesConfiguration)) {
    $configurationToApply = $testCasesConfiguration[$testCase];
    // Apply require configuration
    foreach ($configurationToApply as $key => $value) {
        echo 'Set Value <strong>' . $value . '</strong> for configuration key <i>' . $key . '</i><br />';
        Configuration::updateValue($key, $value);
    }
} else {
    echo '<div class="warning" style="border:1px solid red;color:red;font-weight:bold;padding:10px;margin-bottom: 20px">';
    echo 'Error : Unknow test' . $testCase;
    echo '</div>';
}
