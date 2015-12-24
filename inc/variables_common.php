<?
	require($site_include_path.'removedir.php');

	if (
		isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
		||
		isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
	) {
		$site_protocol = 'https://';
	}
	else {
		$site_protocol = 'http://';
	}

	$original_url = $_SERVER['REDIRECT_URL']; // Apache module
//	$original_url = $_SERVER['REQUEST_URI'];  // FastCGI

//	if ($original_url=='' && $_SERVER['REQUEST_URI']!='') {
//		header('Location: '.$site_root, TRUE, 301); exit;
//	}

//echo "[$original_url, {$_SERVER['REQUEST_URI']}]";

	$parsed_url = parse_url($site_protocol.$_SERVER['HTTP_HOST'].$original_url);

	$segment = explode('/', substr($parsed_url['path'], 1));
//die(print_r($segment, true));

	$registry->set('segment', $segment);

	$registry->set('site_name', 'westmedicalgroup.com');

	$registry->set('site_protocol', $site_protocol);
	$registry->set('site_root', $site_protocol.$_SERVER['HTTP_HOST'].'/');
	$registry->set('site_attachment_path', 'attachments/');

	$registry->set('site_i18n_root', $site_root_absolute.'i18n/');

	parse_str($_SERVER["REDIRECT_QUERY_STRING"], $__GET);
	if (get_magic_quotes_gpc()) {
		function stripslashes_deep($value) {
			$value = is_array($value) ?
				array_map('stripslashes_deep', $value) :
				stripslashes($value);
			return $value;
		}
		$_POST = array_map('stripslashes_deep', $_POST);
		$_GET = array_map('stripslashes_deep', $_GET);
		$__GET = array_map('stripslashes_deep', $__GET);
		$_COOKIE = array_map('stripslashes_deep', $_COOKIE); 
		$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
	}

//	$rss = $db->query("SELECT * FROM `setup`");
//	while ($sas = $db->fetch($rss)) $_SESSION['setup'][$sas['name']]=$sas['value'];
	$registry->set('setup', new common\Setup);

	$registry->set('attachment_settings',
		array(
			'news'     => array('name'=>'Новости',         'list_page'=>'news.php',     'edit_page'=>'news_edit.php',     'path'=>'news/'),
			'block'    => array('name'=>'Блоки',           'list_page'=>'block.php',    'edit_page'=>'block_edit.php',    'path'=>'block/'),
			'page'     => array('name'=>'Страницы',        'list_page'=>'page.php',     'edit_page'=>'page_edit.php',     'path'=>'page/'),
			'tape'     => array('name'=>'Ленты',           'list_page'=>'tape.php',     'edit_page'=>'tape_edit.php',     'path'=>'tape/'),
			'template' => array('name'=>'Шаблоны страниц', 'list_page'=>'template.php', 'edit_page'=>'template_edit.php', 'path'=>'template/'),
			'email_template' => array('name'=>'Шаблоны уведомлений', 'list_page'=>'email_template.php', 'edit_page'=>'email_template_edit.php', 'path'=>'email_template/')
		)
	);

	function is_valid_attachment_parent_table ($parent_table, $attachment_settings) {
		return $parent_table && (FALSE!==strpos(implode('|', array_keys($attachment_settings)), $parent_table));
	}

	$default_language = 'ru';
//	$_SESSION['country_code'] = Country::getClientCountryCode();
//	setcookie('country_code', $_SESSION['country_code'], time() + 5184000); // 60 days
//	if ($segment[0]=='ru') {
//		$lang = 'ru';
//		unset($segment[0]);
//	}
//	else {
		$lang = 'ru';
//	}
	$registry->set('lang', $lang);

	$current_date = mktime(0,0,0, date('n'), date('j'), date('Y'));

//	$languages = array(
//		'ru'=>'Русский',
//		'de'=>'Deutsch',
//		'en'=>'English'
//	);
//	$languages_dns = array (
//		'ru'=>'http://ru.mmotoparts.com',
//		'de'=>'http://de.mmotoparts.com',
//		'en'=>'http://www.mmotoparts.com'
//	);

//	$languages_string = implode('|', array_keys($languages));

	$lang_date_format = array (
		'uk'=>'d.m.Y H:i',
		'ru'=>'d.m.Y H:i',
		'de'=>'d.m.Y H:i',
		'en'=>'m.d.Y H:i'
	);		


	define('SITE_ROOT_SIGN', '{site_root}');

	if ($user_account_id = intval($_SESSION['account_id'])) {
		$me = new Person($user_account_id);
		$registry->set('me', $me);
		$registry->set('i18n_language', $me->locale);
	}

	function d($var, $stop=false) {
		echo '<pre>'.print_r($var, true).'</pre>';
		if ($stop) die();
	}
?>