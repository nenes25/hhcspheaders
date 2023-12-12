<?php

// TMP load en dur, trouver comment dynamiser ce point
require_once '/home/herve/www/prestashop/tests/1786/config/config.inc.php';

$testCasesConfiguration = [
    // X-Content - Xframes Tags
    'FO_XCONTENT_XFRAME_DISABLE' => [
        'HHCSPHEADERS_ENABLE_XCONTENT' => 0,
        'HHCSPHEADERS_ENABLE_XFRAME' => 0,
    ],
    'FO_XCONTENT_XFRAME_ENABLE' => [
        'HHCSPHEADERS_ENABLE_XCONTENT' => 1,
        'HHCSPHEADERS_ENABLE_XFRAME' => 1,
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
    //CSP POLICIES
    'DISABLE_ALL' => [],
    'DISABLE_BO_ENABLE_FO'=> [],
    'DISABLE_FO_ENABLE_BO'=> [],
    'CSP_MODE_REPORT_ONLY' => [],
    'CSP_MODE_BLOCK_ONLY' => [],
    'CSP_MODE_BOTH' => [],
    'CSP_DEFAULT_SRC_ONLY' => [],
    'CSP_ALL' => [],
    'CSP_SCRIPT_SRC' => [],
    'CSP_STYLE_SRC' => [],
    'CSP_IMG_SRC' => [],
    'CSP_CONNECT_SRC' => [],
    'CSP_FONT_SRC'=> [],
    'CSP_OBJECT_SRC'=> [],
    'CSP_MEDIA_SRC'=> [],
    'CSP_FRAME_SRC'=>[],
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
