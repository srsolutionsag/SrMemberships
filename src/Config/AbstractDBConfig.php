<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

declare(strict_types=1);

namespace srag\Plugins\SrMemberships\Config;

use ilDBInterface;
use JsonException;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
abstract class AbstractDBConfig implements Config
{
    use Packer;

    public const TABLE_NAME = 'srms_config';
    /**
     * @var \ilDBInterface
     */
    protected $db;
    /**
     * @var array
     */
    protected $values = [];

    public function __construct(ilDBInterface $db)
    {
        $this->db = $db;
        $this->read();
    }

    public function read(): void
    {
        $this->values = [];
        $set = $this->db->query('SELECT * FROM ' . self::TABLE_NAME);
        while ($rec = $this->db->fetchAssoc($set)) {
            $this->values[$rec['namespace']][$rec['config_key']] = $this->unpack(
                new PackedValue(
                    $rec['value'],
                    (int) $rec['type']
                )
            );
        }
    }

    public function get(string $key, $default = null)
    {
        return $this->values[$this->getNamespace()][$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->values[$this->getNamespace()][$key] = $value;
        $this->saveToDB($key, $value);
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @return void
     * @throws JsonException
     */
    protected function saveToDB(string $key, $value): void
    {
        $packed_value = $this->pack($value);

        $query_result = $this->db->queryF(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE `namespace` = %s AND `config_key` = %s',
            ['text', 'text'],
            [$this->getNamespace(), $key]
        );
        if ($query_result->numRows() > 0) {
            // UPDATE VALUE
            $this->db->update(self::TABLE_NAME, [
                'namespace' => ['text', $this->getNamespace()],
                'config_key' => ['text', $key],
                'value' => ['text', $packed_value->getPackedValue()],
                'type' => ['integer', $packed_value->getType()],
            ], [
                'namespace' => ['text', $this->getNamespace()],
                'config_key' => ['text', $key],
            ]);
        } else {
            // INSERT VALUE
            $this->db->insert(self::TABLE_NAME, [
                'namespace' => ['text', $this->getNamespace()],
                'config_key' => ['text', $key],
                'value' => ['text', $packed_value->getPackedValue()],
                'type' => ['integer', $packed_value->getType()],
            ]);
        }
    }
}
