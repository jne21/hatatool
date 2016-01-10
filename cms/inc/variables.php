<?php
	$registry->set('cms_i18n_path', $site_root_absolute.'cms/i18n/');
    if (isset($_SESSION['admin']['locale'])) {
        $registry->set('i18n_language', $_SESSION['admin']['locale']);
    }
//  $registry->set('cms_template_path', $site_root_absolute.'cms/tpl/');
    $registry->set(
            'cms_template_path',
            $registry->get('cms_i18n_path').'template/'.$registry->get('i18n_language').'/'
    );
    