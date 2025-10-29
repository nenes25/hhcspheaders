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

/**
 * Upgrade module 0.5.0 - Create database table for CSP violations
 *
 * @param Module $module
 *
 * @return bool
 */
function upgrade_module_0_5_0(Module $module): bool
{
    $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'hhcspheaders_violations` (
        `id_violation` int(11) NOT NULL AUTO_INCREMENT,
        `document_uri` varchar(500) NOT NULL,
        `blocked_uri` varchar(500) NOT NULL,
        `violated_directive` varchar(100) NOT NULL,
        `effective_directive` varchar(100) DEFAULT NULL,
        `original_policy` text DEFAULT NULL,
        `disposition` varchar(20) DEFAULT NULL,
        `status_code` int(11) DEFAULT NULL,
        `occurrences` int(11) NOT NULL DEFAULT 1,
        `first_seen` datetime NOT NULL,
        `last_seen` datetime NOT NULL,
        `is_resolved` tinyint(1) NOT NULL DEFAULT 0,
        `date_add` datetime NOT NULL,
        `date_upd` datetime NOT NULL,
        PRIMARY KEY (`id_violation`),
        KEY `idx_violated_directive` (`violated_directive`),
        KEY `idx_is_resolved` (`is_resolved`),
        KEY `idx_occurrences` (`occurrences`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

    return Db::getInstance()->execute($sql);
}
