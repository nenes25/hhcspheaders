<?php

define('_PS_VERSION_', '8.1.0');
define('_PS_MODULE_DIR_', dirname(__DIR__, 2) . '/');
define('_DB_PREFIX_', 'ps_');
define('_PS_ROOT_DIR_', '/tmp');

class Module
{
    public $name = '';
    public $tab = '';
    public $version = '';
    public $author = '';
    public $bootstrap = false;
    public $displayName = '';
    public $description = '';

    public function __construct()
    {
    }

    public function l(string $string): string
    {
        return $string;
    }
}

class ObjectModel
{
    const TYPE_INT = 1;
    const TYPE_BOOL = 2;
    const TYPE_STRING = 3;
    const TYPE_FLOAT = 4;
    const TYPE_DATE = 5;
    const TYPE_HTML = 6;
    const TYPE_NOTHING = 7;
    const TYPE_SQL = 8;

    public function add(bool $autoDate = true, bool $nullValues = false): bool
    {
        return true;
    }

    public function update(bool $nullValues = false): bool
    {
        return true;
    }
}

class Configuration
{
    private static array $store = [];

    public static function get(string $key, $idLang = null, $idShopGroup = null, $idShop = null, $default = false)
    {
        return self::$store[$key] ?? $default;
    }

    public static function getMultiple(array $keys, $idLang = null, $idShopGroup = null, $idShop = null): array
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = self::$store[$key] ?? false;
        }

        return $result;
    }

    public static function updateValue(string $key, $value): bool
    {
        self::$store[$key] = $value;

        return true;
    }

    public static function set(string $key, $value): void
    {
        self::$store[$key] = $value;
    }

    public static function resetForTests(): void
    {
        self::$store = [];
    }
}

class Db
{
    private static ?self $instance = null;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function execute(string $sql): bool
    {
        return true;
    }

    public function getValue($sql): mixed
    {
        return false;
    }

    public function executeS($sql): array
    {
        return [];
    }
}

require_once dirname(__DIR__, 2) . '/hhcspheaders.php';
require_once dirname(__DIR__, 2) . '/classes/CspViolation.php';
