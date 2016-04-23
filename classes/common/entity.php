<?php
namespace common;

trait entity
{
    /**
     * Ручная (пользовательская сортировка записей)
     * @param array $orderedIds Массив первичных ключей и значения сортирующего поля.
     * @param string $table Идентификатор таблицы БД. По умолчанию задаётся внешней константой TABLE основного класса
     * @param string $fieldName Имя сортирующего поля
     * @param string $filter Дополнительное условие WHERE для SQL-запроса
     * @return NULL
     **/
    static function renumberAll($orderedIds, $table=NULL, $fieldName=self::ORDER_FIELD_NAME)
    {
        $db = Registry::getInstance()->get(self::DB);
        foreach($orderedIds as $index=>$id) {
            if (intval($id)) {
                $db->update(
                    $table ? $db->realEscapeString($table) : self::TABLE,
                    [$fieldName => intval($index)+1],
                    "`id` = $id"
                );
            }
        }
    }

    /**
     * Установка значения атрибута в БД без создания экземпляра объекта.
     * @param $pKey Int Значение первичного ключа.
     * @param $field Идентификатор поля таблицы БД
     * @param $value Новое значение
     * @return NULL
     **/
    static function updateValue($pKey, $field, $value)
    {
        $id = intval($pKey);
        if ($id) {
            $db = Registry::getInstance()->get(self::DB);
            $db->update (
                self::TABLE,
                array($db->realEscapeString($field) => $value),
                "`id`=$id"
            );
        }
    }

    /**
     * Установка значения атрибута видимости записи в БД без создания экземпляра объекта.
     * @param $id Int Значение первичного ключа.
     * @param $action Int Новое значение (1 или 0)
     * @return NULL
     **/
    static function toggle($id, $action, $property='show')
    {
        self::updateValue($id, $property, intval($action));
    }

    /**
     * Получение значения сортирующего поля при добавлении новой записи в БД.
     * @param $whereCondition String Фраза для WHERE, если требуется фильтрация данных.
     * @param $fieldName String Имя сортирующего поля
     * @return NULL
     **/
    static function getNextOrderIndex($whereCondition = NULL, $fieldName=self::ORDER_FIELD_NAME)
    {
        $db = Registry::getInstance()->get(self::DB);
        return $db->getValue("SELECT IFNULL(MAX(`".$db->realEscapeString($fieldName)."`), 0)+1 FROM `".self::TABLE."`".($whereCondition ? " WHERE $whereCondition" : ''));
    }

    /**
     * Translit decoder function creates data for making long SEO-friendly URLs.
     * @param string $s
     * @return string
     */
    static function transURL($s)
    {
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
