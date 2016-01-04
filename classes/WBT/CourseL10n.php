<?
namespace WBT;

use common\L10n;
use common\Registry;

class CourseL10n extends L10n {
	const
		TABLE = 'course_l10n'
	;

	public
		$id,
		$parentId
	;

	function __construct($parentId=NULL) {
		parent::__construct(self::TABLE, $parentId);
	}
	
	function loadDataFromArray($localeId, $array) {
		$this->set('name',        $array['name'],          $localeId);
		$this->set('meta',        $array['meta'],          $localeId);
		$this->set('description', $array['description'],   $localeId);
		$this->set('brief',       $array['brief'],         $localeId);
		$this->set('url',         $array['url'],           $localeId);
		$this->set('title',       $array['title'],         $localeId);
		$this->set('state',       intval($array['state']), $localeId);
	}

	static function getListByIds($idList) {
		$result = [];
		if (is_array($idList) && count($idList)) {
			$ids = array_map('intval', $idList);
			foreach($l = parent::loadByParentIds(self::TABLE, $ids) as $parentId=>$l10nData) {
				$l10n = new CourseL10n();
				$l10n->parentId = $parentId;
				foreach ($l10nData as $localeId=>$l10nItem) {
					$l10n->loadDataFromArray($localeId, $l10nItem);
				}
				$result[$parentId] = $l10n;
			}
		}
		return $result;
	}

	function save() {
		foreach(array_keys(Registry::getInstance()->get('locales')) as $locale) {
			$data[$locale] = [
				'name'        => $this->get('name',        $locale),
				'meta'        => $this->get('meta',        $locale),
				'description' => $this->get('description', $locale),
				'brief'       => $this->get('brief',       $locale),
				'url'         => $this->get('url',         $locale),
				'title'       => $this->get('title',       $locale),
				'state'       => $this->get('state',       $locale)
			];
		}
		parent::saveData($this->parentId, self::TABLE, $data);
	}

}
