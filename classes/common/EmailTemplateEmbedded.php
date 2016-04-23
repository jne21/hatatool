<?php

namespace common;

class EmailTemplateEmbedded extends EmailTemplateAttachmentAbstract
{
    const PATH = 'images/';

    const DB = 'db';
    const TABLE = 'email_template_embedded';
    
    public $cid;

    function loadDataFromArray($array)
    {
        parent::loadDataFromArray($array);
        $this->cid = $array['cid'];
    }
}