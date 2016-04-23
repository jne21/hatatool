<?php
namespace common;

use common\SetupItem;

final class Setup
{
    const
        TABLE = 'setup',
        DB    = 'db'
    ;
    protected
        $table, $values;

    function __construct($table = self::TABLE)
    {
        $db = Registry::getInstance()->get(self::DB);
        $recordset = $db->getRecordset("SELECT * FROM `" . self::TABLE . "` ORDER BY `name`");
        while ($record = $recordset->fetch()) {
            $this->values[$record->name] = new SetupItem($this, $record);
        }
    }

    function get($name)
    {
        if (isset($this->values[$name])) {
            return $this->values[$name]->getProperty('value');
        }
        else {
            return null;
        }
    }

    function getList()
    {
        return $this->values;
    }

    function updateValue($name, $value)
    {
        if (isset($this->values[$name])) {
            $this->values[$name]->setProperty('value', $value);
                $this->values[$name]->save();
            }
    }

    function save($name, $value, $description)
    {
        if (isset($this->values[$name])) {
            $this->values[$name]->setProperty('value', $value);
            $this->values[$name]->setProperty('description', $description);
            $this->values[$name]->save();
        }
        else {
            $this->values[$name] = new SetupItem(
                $this,
                array (
                    'name'        => $name,
                    'value'       => $value,
                    'description' => $description
                )
            );
            $this->values[$name]->create();
        }
    }

    /**
     * Standard getter
     * @param string $name
     * @return mixed
     */
    function getProperty($name)
    {
        return $this->$name;
    }
}
