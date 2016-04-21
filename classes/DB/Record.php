<?php

namespace DB;

class Record
{
    private $recordset;
    private $fields = [];

    function __construct(AbstractDbRecordset $recordset, $data)
    {
        $this->recordset = $recordset;
        if (is_array($data)) {
            $this->fields = $data;
        }
    }

    function __get($propertyName)
    {
        $result = null;
        if (array_key_exists($propertyName, $this->fields)) {
            $result = $this->fields[$propertyName];
        }
        return $result;
    }

    function asArray()
    {
        return $this->fields;
    }

    function getValue() {
        $keys = array_keys($this->fields);
        return $this->fields[$keys[0]];
    }
}