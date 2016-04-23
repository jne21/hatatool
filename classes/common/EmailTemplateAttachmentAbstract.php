<?php

namespace common;

class EmailTemplateAttachmentAbstract
{
    const BASE_PATH = 'img/email_template/';
    public $id, $fileName, $description, $templateId;

    function __construct($id = NULL)
    {
        if ($id) {
            $this->registry = registry::getInstance();
            $db = $this->registry->get(static::DB);
            $rs = $db->query("SELECT * FROM `" . static::TABLE . "` WHERE `id`=".intval($id)) or die(__METHOD__ . ': ' . $db->lastError);
            if ($sa = $db->fetch($rs)) {
                $this->loadDataFromArray($sa);
            }
        }
    }

    function loadDataFromArray($array)
    {
        $this->id = $array['id'];
        $this->templateId = $array['email_template_id'];
        $this->fileName = $array['filename'];
        $this->description = $array['description'];
    }

    function getList($templateId) {
        $this->templateId = intval($templateId);
        $result = [];
        if ($this->templateId) {
            $this->registry = registry::getInstance();
            $db = $this->registry->get(static::DB);
            $rs = $db->query("SELECT * FROM `" . static::TABLE . "` WHERE `email_template_id`=".$this->templateId) or die(__METHOD__ . ': ' . $db->lastError);
            while ($sa = $db->fetch($rs)) {
                $item = new static();
                $item->loadDataFromArray($sa);
            }
        }
        return $result;
    }
}
