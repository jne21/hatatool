<?
namespace common;

trait entity {
	static function renumberAll($orderedIds, $table=NULL) {
		$db = Registry::getInstance()->get(self::DB);
		foreach($orderedIds as $index=>$id) {
			if (intval($id)) {
				$db->update(
					$table ? $db->realEscapeString($table) : self::TABLE,
					['num' => intval($index)+1],
					"`id` = $id"
				);
			}
		}
	}

	static function updateValue($pKey, $field, $value) {
		if ($id = intval($pKey)) {
			$db = Registry::getInstance()->get(self::DB);
			$db->update (
				self::TABLE,
				array($db->realEscapeString($field) => $value),
				"`id`=$id"
			);
		}
	}

	static function toggle($id, $action) {
		self::updateValue($id, 'show', intval($action));
	}

	static function getNextOrderIndex($whereCondition = NULL, $fieldName='num') {
		$db = Registry::getInstance()->get(self::DB);
		return $db->result($db->query("SELECT IFNULL(MAX(`".$db->realEscapeString($fieldName)."`), 0)+1 FROM `".self::TABLE."`".($whereCondition ? " WHERE $whereCondition" : '')), 0, 0);
	}

	static function transURL($s) {
		$L['from'] = array(
			'Ж', 'Ц', 'Ч', 'Ш', 'Щ', 'Ы', 'Ю', 'Я', 'Ї',
			'ж', 'ц', 'ч', 'ш', 'щ', 'и', 'ю', 'я', 'ї',
			'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Є', 'З', 'І', 'И', 'Й', 'К',
			'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ь',
			'а', 'б', 'в', 'г', 'д', 'е', 'є', 'з', 'і', 'и', 'й', 'к',
			'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ь',
			'\'',
			'Ä', 'Ö', 'Ü', '�', 'ä', 'ö', 'ü', 'ß'
		);

		$L['to'] = array(
			"ZH", "TS", "CH", "SH", "SCH", "Y", "YU", "YA", "YI",
			"zh", "ts", "ch", "sh", "sch", "y", "yu", "ya", "yi",
			"A",  "B" , "V" , "G",  "D",   "E", "E",  "Z",  "I", "Y", "J", "K",
			"L",  "M",  "N",  "O",  "P",   "R", "S",  "T",  "U", "F", "H", "",
			"a",  "b",  "v",  "g",  "d",   "e", "e",  "z",  "i", "y", "j", "k",
			"l",  "m",  "n",  "o",  "p",   "r", "s",  "t",  "u", "f", "h", "",
			"y",
			'A', 'O' ,'U', 'SS', 'a', 'o', 'u', 'ss'
		);

		$r = str_replace($L['uk'], $L['en'], $s);

		$r = mb_strtolower($r);
		$r = preg_replace(array('/\s/', '/[\W]/', ), array('-', '-'), $r);
		$r = preg_replace('/[_\-]{2,}/', '-', $r);
		$r = preg_replace(array('/^\W/', '/\W$/', ), array('', ''), $r);
		$r = preg_replace('/^(\d){1}/', '-$1', $r); // leading digit

		return $r;
	}
}
?>