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
