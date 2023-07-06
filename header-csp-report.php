<?php
/**
 * This page receives the report from the csp and log them
 */
require_once dirname(__FILE__).'/../../config/config.inc.php';
include(dirname(__FILE__).'/../../init.php');
/** @var Hhcspheaders $module */
$module = Module::getInstanceByName('hhcspheaders');
if ( $module){
    $module->logCspContent();
}
