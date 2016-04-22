<?php

namespace DB;

class Record
{
    private $recordset;
    private $fields = [];

    function __construct(AbstractRecordset $recordset, $data)
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

    function getValue($key=0) {
        if (array_key_exists($key, $this->fields)) {
            $result = $this->fields[$key];
        } else {
            $keys = array_keys($this->fields);
            if (is_numeric($key)) {
                $result = $this->fields[$keys[$key]];
            } else {
                $result = $this->fields[$keys[0]];
            }
        }
        return $result;
    }
}