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

class CspViolation extends ObjectModel
{
    /** @var string */
    public $document_uri;

    /** @var string */
    public $blocked_uri;

    /** @var string */
    public $violated_directive;

    /** @var string */
    public $effective_directive;

    /** @var string */
    public $original_policy;

    /** @var string */
    public $disposition;

    /** @var int */
    public $status_code;

    /** @var int */
    public $occurrences;

    /** @var string */
    public $first_seen;

    /** @var string */
    public $last_seen;

    /** @var bool */
    public $is_resolved;

    /** @var string */
    public $date_add;

    /** @var string */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'hhcspheaders_violations',
        'primary' => 'id_violation',
        'fields' => [
            'document_uri' => ['type' => self::TYPE_STRING, 'validate' => 'isUrl', 'size' => 500, 'required' => true],
            'blocked_uri' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 500, 'required' => true],
            'violated_directive' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 100, 'required' => true],
            'effective_directive' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 100],
            'original_policy' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'disposition' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 20],
            'status_code' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'occurrences' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'first_seen' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
            'last_seen' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
            'is_resolved' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    /**
     * Find existing violation by key fields
     *
     * @param string $documentUri
     * @param string $blockedUri
     * @param string $violatedDirective
     *
     * @return CspViolation|null
     */
    public static function findExisting($documentUri, $blockedUri, $violatedDirective)
    {
        $sql = new DbQuery();
        $sql->select('id_violation');
        $sql->from('hhcspheaders_violations');
        $sql->where('document_uri = \'' . pSQL($documentUri) . '\'');
        $sql->where('blocked_uri = \'' . pSQL($blockedUri) . '\'');
        $sql->where('violated_directive = \'' . pSQL($violatedDirective) . '\'');
        $sql->where('is_resolved = 0');

        $idViolation = Db::getInstance()->getValue($sql);

        if ($idViolation) {
            return new self($idViolation);
        }

        return null;
    }

    /**
     * Increment occurrence count and update last_seen
     *
     * @return bool
     */
    public function incrementOccurrence()
    {
        ++$this->occurrences;
        $this->last_seen = date('Y-m-d H:i:s');
        $this->date_upd = date('Y-m-d H:i:s');

        return $this->update();
    }

    /**
     * Create violation from CSP report data
     *
     * @param array $reportData
     *
     * @return CspViolation|false
     */
    public static function createFromReport(array $reportData)
    {
        $violation = new self();
        $now = date('Y-m-d H:i:s');

        $violation->document_uri = isset($reportData['document-uri']) ? $reportData['document-uri'] : '';
        $violation->blocked_uri = isset($reportData['blocked-uri']) ? $reportData['blocked-uri'] : '';
        $violation->violated_directive = isset($reportData['violated-directive']) ? $reportData['violated-directive'] : '';
        $violation->effective_directive = isset($reportData['effective-directive']) ? $reportData['effective-directive'] : null;
        $violation->original_policy = isset($reportData['original-policy']) ? $reportData['original-policy'] : null;
        $violation->disposition = isset($reportData['disposition']) ? $reportData['disposition'] : null;
        $violation->status_code = isset($reportData['status-code']) ? (int) $reportData['status-code'] : null;
        $violation->occurrences = 1;
        $violation->first_seen = $now;
        $violation->last_seen = $now;
        $violation->is_resolved = false;
        $violation->date_add = $now;
        $violation->date_upd = $now;

        if ($violation->add()) {
            return $violation;
        }

        return false;
    }

    /**
     * Get all unresolved violations ordered by occurrences
     *
     * @param int $limit
     *
     * @return array
     */
    public static function getUnresolvedViolations($limit = 100)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('hhcspheaders_violations');
        $sql->where('is_resolved = 0');
        $sql->orderBy('occurrences DESC, last_seen DESC');
        $sql->limit($limit);

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get violations statistics
     *
     * @return array
     */
    public static function getStatistics()
    {
        $stats = [];

        // Total violations
        $stats['total'] = (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'hhcspheaders_violations'
        );

        // Unresolved violations
        $stats['unresolved'] = (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'hhcspheaders_violations WHERE is_resolved = 0'
        );

        // Total occurrences
        $stats['total_occurrences'] = (int) Db::getInstance()->getValue(
            'SELECT SUM(occurrences) FROM ' . _DB_PREFIX_ . 'hhcspheaders_violations'
        );

        // Most common directive violated
        $stats['most_violated_directive'] = Db::getInstance()->getValue(
            'SELECT violated_directive FROM ' . _DB_PREFIX_ . 'hhcspheaders_violations 
             WHERE is_resolved = 0 
             GROUP BY violated_directive 
             ORDER BY SUM(occurrences) DESC '
        );

        return $stats;
    }
}
