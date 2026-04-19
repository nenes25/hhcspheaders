<?php

require_once __DIR__ . '/../../../../config/config.inc.php';

$action = strip_tags($_GET['action'] ?? '');
$now = date('Y-m-d H:i:s');

if ($action === 'setup') {
    Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'hhcspheaders_violations`');
    Db::getInstance()->execute(
        'INSERT INTO `' . _DB_PREFIX_ . 'hhcspheaders_violations`
         (document_uri, blocked_uri, violated_directive, occurrences, first_seen, last_seen, is_resolved, date_add, date_upd)
         VALUES
         ("https://example.com/", "https://evil.com/script.js", "script-src", 3, "' . $now . '", "' . $now . '", 0, "' . $now . '", "' . $now . '"),
         ("https://example.com/page2", "https://evil.com/style.css", "style-src", 1, "' . $now . '", "' . $now . '", 0, "' . $now . '", "' . $now . '")'
    );
    echo 'OK: 2 violations created';
} elseif ($action === 'cleanup') {
    Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'hhcspheaders_violations`');
    echo 'OK: violations cleared';
} else {
    echo '<div class="warning">Unknown action: ' . htmlspecialchars($action) . '</div>';
}
