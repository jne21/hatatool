<?php
$registry->set('cms_i18n_path', $site_root_absolute.'cms/i18n/');

if (isset($_SESSION['admin']['locale'])) {
    $language = $_SESSION['admin']['locale'];
}
else {
    $language = $registry->get('setup')->get('cms_locale');
}

$registry->set('i18n_language', $language);
$registry->set('cms_locale', $language);

//  $registry->set('cms_template_path', $site_root_absolute.'cms/tpl/');
$registry->set(
        'cms_template_path',
        $registry->get('cms_i18n_path').'template/'.$registry->get('i18n_language').'/'
);
