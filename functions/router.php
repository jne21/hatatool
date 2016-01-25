<?
	$rsr = $db->query("SELECT * FROM `redirect` WHERE `active`<>0 ORDER BY `num`") or die('Redirect: '.$db->lastError);
	$redirect_source_url = substr($parsed_url['path'], 1);
	if ($_SERVER["REDIRECT_QUERY_STRING"]) {
		 $redirect_source_url .= '?'.$_SERVER["REDIRECT_QUERY_STRING"];
	}
//die($redirect_source_url);
	while ($sar = $db->fetch($rsr)) {
		if (
			($sar['regexp'] && preg_match("/{$sar['source']}/", $redirect_source_url))
			||
			(!$sar['regexp'] && $sar['source']==$redirect_source_url)
		) {
			$db->update(
				'redirect',
				array(
					'request_dt' => $db->makeForcedValue('NOW()')
				),
				"`id`={$sar['id']}"
			);
			$db->insert(
				'redirect_query',
				array (
					'redirect_id'           => $sar['id'],
					'HTTP_REFERER'          => $_SERVER['HTTP_REFERER'],
					'REMOTE_ADDR'           => $_SERVER['REMOTE_ADDR'],
					'HTTP_USER_AGENT'       => $_SERVER['HTTP_USER_AGENT'],
					'REDIRECT_URL'          => $_SERVER['REDIRECT_URL'],
					'REDIRECT_QUERY_STRING' => $_SERVER['REDIRECT_QUERY_STRING']
				)
			) or die('Redirect Query: '.$db->lastError);

			$destination = str_replace('{site_root}', $registry->get('site_root'), $sar['destination']);
			header('Location: '.$destination, TRUE, $sar['status']);
			exit;
		}
	}

	$rssm = $db->query("SELECT * FROM `module` ORDER BY num") or die('Modules: '.$db->error());
	while ($sasm = $db->fetch($rssm)) {
		$modules[$sasm['url']]=$sasm['path'];
	}

	foreach ($modules as $regexp=>$path) {

// echo (preg_match($regexp, substr($original_url, 1)) . "[$regexp]" . "[$original_url]<br />");

		if (preg_match($regexp, substr($original_url, 1))) {
			$module_path = $path;
			break;
		}
	}
//die($site_module_path . $path);
	require ($site_module_path . $path);

?>