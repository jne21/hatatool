<?php

namespace CMS;

use \CMS\TemplateL10n;

class TemplateEditor extends SimpleObject {

	public
		$id,
		$name,
		$alias
	;

	const
        DB = 'db',
	    TABLE = 'template'
	;

    function __construct($templateId=NULL) {
        parent::__construct($templateId);
        $this->l10n = new TemplateL10n($this->id);
    }

	function loadDataFromArray($data) {
	    $this->id    = $sa['id'];
	    $this->name  = $sa['name'];
	    $this->alias = $sa['alias'];
	    $this->html  = $sa['html'];
	}
	
	function save() {
        $record = [
                'name'  => trim($this->name),
                'alias' => trim($this->alias),
        ];
		if ($this->id) {
			$db->update(self::TABLE, $record, "id=".$this->id);
		}
		else {
			$db->insert(self::TABLE, $record);
			$this->id = $db->insertId();
			$this->l10n->parentId = $this->id;
		}
		$this->l10n->save();
	}

	static function delete($templateId) {
		if ($id = intval($templateId)) {
			$db = Registry::getInstance()->get('db');
			$attachment = new attachment(self::TABLE, $id);
			$attachment->unlinkAll();
			parent::delete($id);
		}
	}

}