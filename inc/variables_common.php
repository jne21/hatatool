<?php
use CMS\Page;
use CMS\Renderer;

require ($site_include_path . 'removedir.php');

if (isset($_SERVER['HTTPS']) &&
         ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
         isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
         $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    $site_protocol = 'https://';
} else {
    $site_protocol = 'http://';
}

$original_url = $_SERVER['REDIRECT_URL']; // Apache module
                                          // $original_url =
                                          // $_SERVER['REQUEST_URI']; // FastCGI
                                          
// if ($original_url=='' && $_SERVER['REQUEST_URI']!='') {
                                          // header('Location: '.$site_root,
                                          // TRUE, 301); exit;
                                          // }
                                          
// echo "[$original_url, {$_SERVER['REQUEST_URI']}]";

$parsed_url = parse_url($site_protocol . $_SERVER['HTTP_HOST'] . $original_url);

$segment = explode('/', substr($parsed_url['path'], 1));
// die(print_r($segment, true));

$registry->set('segment', $segment);

$registry->set('site_name', 'wbt.com');

$registry->set('site_protocol', $site_protocol);
$registry->set('site_root', $site_protocol . $_SERVER['HTTP_HOST'] . '/');
$registry->set('site_attachment_path', 'attachments/');

$registry->set('site_i18n_root', $site_root_absolute . 'i18n/');

parse_str($_SERVER["REDIRECT_QUERY_STRING"], $__GET);
if (get_magic_quotes_gpc()) {

    function stripslashes_deep ($value)
    {
        $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes(
                $value);
        return $value;
    }
    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $__GET = array_map('stripslashes_deep', $__GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}

// $rss = $db->query("SELECT * FROM `setup`");
// while ($sas = $db->fetch($rss))
// $_SESSION['setup'][$sas['name']]=$sas['value'];
$registry->set('setup', new common\Setup());

$registry->set('attachment_settings', 
        array(
                'news' => array(
                        'name' => 'Новости',
                        'list_page' => 'news.php',
                        'edit_page' => 'news_edit.php',
                        'path' => 'news/'
                ),
                'block' => array(
                        'name' => 'Блоки',
                        'list_page' => 'block.php',
                        'edit_page' => 'block_edit.php',
                        'path' => 'block/'
                ),
                'page' => array(
                        'name' => 'Страницы',
                        'list_page' => 'page.php',
                        'edit_page' => 'page_edit.php',
                        'path' => 'page/'
                ),
                'tape' => array(
                        'name' => 'Ленты',
                        'list_page' => 'tape.php',
                        'edit_page' => 'tape_edit.php',
                        'path' => 'tape/'
                ),
                'template' => array(
                        'name' => 'Шаблоны страниц',
                        'list_page' => 'template.php',
                        'edit_page' => 'template_edit.php',
                        'path' => 'template/'
                ),
                'email_template' => array(
                        'name' => 'Шаблоны уведомлений',
                        'list_page' => 'email_template.php',
                        'edit_page' => 'email_template_edit.php',
                        'path' => 'email_template/'
                )
        ));

function is_valid_attachment_parent_table ($parent_table, $attachment_settings)
{
    return $parent_table &&
             (FALSE !==
             strpos(implode('|', array_keys($attachment_settings)), 
                    $parent_table));
}

$locales = array(
        'uk' => array(
                'locale' => array(
                        'uk_UA.UTF-8',
                        'ukr_UKR.UTF-8',
                        'Ukrainian_Ukraine.UTF-8'
                ),
                'name' => 'Українська',
                'dateFormat' => 'd.m.Y',
                'active' => TRUE
        ),
        'en' => array(
                'locale' => array(
                        'en_US.UTF-8',
                        'eng_USA.UTF-8',
                        'English_USA.UTF-8'
                ),
                'name' => 'English',
                'dateFormat' => 'm.d.Y',
                'active' => TRUE
        ),
        'de' => array(
                'locale' => array(
                        'de_DE.UTF-8',
                        'deu_DEU.UTF-8',
                        'German_Germany.UTF-8'
                ),
                'name' => 'Deutch',
                'dateFormat' => 'd.m.Y',
                'active' => TRUE
        ),
        'ru' => array(
                'locale' => array(
                        'ru_RU.UTF-8',
                        'rus_RUS.UTF-8',
                        'Russian_Russia.UTF-8'
                ),
                'name' => 'Русский',
                'dateFormat' => 'd.m.Y',
                'active' => TRUE
        )
);
$registry->set('locales', $locales);
$registry->set(
        'i18n_language',
        $registry->get('setup')->get('cms_locale')
);

setlocale(LC_ALL, $locales[$registry->get('i18n_language')]['locale']);
// echo strftime("%A %d %B %Y", mktime(0, 0, 0, 12, 1, 1968));

define('SITE_ROOT_SIGN', '{site_root}');

if ($user_account_id = intval($_SESSION['account_id'])) {
    $me = new Person($user_account_id);
    $registry->set('me', $me);
    $registry->set('i18n_language', $me->locale);
}

function d ($var, $stop = false)
{
    echo '<pre>' . print_r($var, true) . '</pre>';
    if ($stop)
        die();
}
