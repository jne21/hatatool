<?
	echo $tpl_page->apply(
		array(
			'title'     => $pTitle,
			'main_menu' => $main_menu,
			'content'   => $content/*,
			'site_root' => $site_root*/
		)
	);
?>