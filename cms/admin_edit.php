<?
	require('inc/authent.php');
	require_once($site_include_path.'trans_url.php');
//	require_once($site_include_path.'image_resample.php');

	$id       = intval($_GET['id']);
	$model_id = intval($_GET['mid']);

	if ($id) { // редактирование
		$product = new Product($id, Product::BY_ID, Product::ALL); //d($product,1);
		if ($product->id) {
			$model = new Model($product->modelId, Model::ALL);
			$manufacturer = new Manufacturer($product->manufacturerId, Manufacturer::ALL);
		}
	}
	else { // добавление
		$model = new Model($model_id, Model::ALL);
		if ($model->id) {
			$manufacturer = new Manufacturer($model->manufacturerId, Manufacturer::ALL);
			$product = new Product(); //d($product,1);
			$product->modelId        = $model->id;
			$product->manufacturerId = $model->manufacturerId;
		}
	}
	if (!isset($manufacturer->id)) {
		header('Location: manufacturer.php'); exit;
	}

	$i18n = new i18n($registry->get('site_i18n_root').'product.xml');

	$fnames = array('img');

	if ($_POST['action'] == 'save' && !$demo_mode) { //d($_POST, 1);
		$product->sku             = trim($_POST['sku']);
		$product->show            = intval($_POST['show']=='on');
		$product->new             = intval($_POST['new']=='on');
		$product->status          = intval($_POST['status']);
/*
		$product->priceUAH        = str_replace(',', '.', trim($_POST['price_uah']));
		$product->priceUAHold     = str_replace(',', '.', trim($_POST['price_uah_old']));
		$product->priceUSD        = str_replace(',', '.', trim($_POST['price_usd']));
		$product->priceUSDold     = str_replace(',', '.', trim($_POST['price_usd_old']));
*/
		$product->weight          = str_replace(',', '.', trim($_POST['weight']));
		$product->clsId           = intval($_POST['cls_id']);

		foreach (array_keys($registry->get('locales')) as $locale) {
			$post_url = trim($_POST['url_'.$locale]);

			$product->l10n->loadDataFromArray(
				$locale,
				[
					'name'        => trim($_POST['name_'.$locale]),
					'title'       => trim($_POST['title_'.$locale]),
					'meta'        => trim($_POST['meta_'.$locale]),
					'description' => trim($_POST['description_'.$locale]),
					'url'         => trim($post_url=='')
						? trans_url($product->l10n->get('name', $locale).'-'.$i18n->getText('for', $locale).'-'.$manufacturer->name.'-'.$model->name)
						: $post_url
				]
			);
		}

		foreach(Currency::getAvailableIndex() as $currencyId) {
			$product->setPrice($currencyId, $_POST["price_{$currencyId}"], $_POST["price_{$currencyId}_old"]);
		}
//d($_FILES, 1);

		foreach($fnames as $fn) {
			$product->images[] = array(
				'new_filename' => basename($_FILES[$fn]['name']),
				'tmp_filename' => $_FILES[$fn]['tmp_name']
			);
		}

		$product->save();

		if ($_POST['ret']==0)
			header('Location: prod.php?mid='.$product->modelId);
		else
			header('Location: '.$_SERVER['HTTP_REFERER']);
		exit;
	}


	$tpl             = new template('tpl/prod_edit.htm',             Template::SOURCE_FILE);
	$tplpsi          = new template('tpl/select_option.htm',         Template::SOURCE_FILE);
	$tpli            = new template('tpl/prod_image.htm',            Template::SOURCE_FILE);
	$tplpi           = new template('tpl/prod_edit_price_item.htm',  Template::SOURCE_FILE);
	$tpl_tab         = new template('tpl/prod_tab.htm',              Template::SOURCE_FILE);
	$tpl_tab_content = new template('tpl/prod_tab_content.htm',      Template::SOURCE_FILE);

	$product_status_items = '';
	foreach (Product::getStatusList() as $status_id) {
		$product_status_items .= $tplpsi->apply (
			array (
				'value'    => $status_id,
				'selected' => $product->status==$status_id,
				'name'     => $i18n->getText('status'.$status_id)
			)
		);
	}

	$product_price_items = '';
	$product_price_items_count = 0;
	$currencies = Currency::getList(Currency::ALL);
//d($currencies,1);
	foreach (Currency::getAvailableIndex() as $currencyId) {
		$price = $product->getPrice($currencyId);
//d($currencies[$currencyId],1);
		$product_price_items .= $tplpi->apply ([
			'product_price_items' => $product_price_items,
			'currency_id'         => $currencyId,
			'currency_char3'      => $currencies[$currencyId]->char3,
			'price'               => $price->value,
			'price_old'           => $price->valueOld
		]);
		$product_price_items_count++;
	}

	$product_cls_items = '';
	$productClsList = ProductCls::getList(ProductCls::ALL);
//d($productClsList, 1);
	foreach ($productClsList as $cls_id=>$category) {
		$product_cls_items .= $tplpsi->apply (
			array (
				'value'    => $cls_id,
				'selected' => $product->clsId==$cls_id,
				'name'     => $category->l10n->get('name')
			)
		);
	}

	$images = '';
	$cnt = count($product->images);
	foreach ($product->images as $image_id=>$data) {
		$images .= $tpli->apply (
			array (
				'id'        => $image_id,
				'prod_id'   => $product->id,
				'thumb100'  => $image_id.'_100.jpg',
				'left'      => $data['num']>1,
				'right'     => $data['num']<$cnt,
				'site_root' => $site_root
			)
		);
	}

	$tab_items = '';
	$tab_content_items = '';
	foreach ($registry->get('locales') as $locale=>$localeData) {
		$tab_items .= $tpl_tab->apply ([
			'locale'      => $locale,
			'name'        => $localeData['name']
//			'active'      => $locale == $registry->get('i18n_language')
		]);
		$tab_content_items .= $tpl_tab_content->apply ([
			'locale'      => $locale,
			'name'        => htmlspecialchars($product->l10n->get('name', $locale)),
			'title'       => htmlspecialchars($product->l10n->get('title', $locale)),
			'meta'        => htmlspecialchars($product->l10n->get('meta', $locale)),
			'description' => htmlspecialchars($product->l10n->get('description', $locale)),
			'url'         => htmlspecialchars($product->l10n->get('url', $locale))
		]);
	}
//d($product, 1);
	$pTitle = (($id==0)?'Створення':'Редагування').' товару';
	$page = new Page;
	$page->content = $tpl->apply (
		array (
			'id'                         => $product->id,
			'name_ua'                    => $product->l10n->data['ua']['name'],
			'price_uah'                  => $product->priceUAH,
			'price_uah_old'              => $product->priceUAHold,
			'price_usd'                  => $product->priceUSD,
			'price_usd_old'              => $product->priceUSDold,
			'weight'                     => $product->weight,
			'product_status_items'       => $product_status_items,
			'product_price_items'        => $product_price_items,
			'product_price_items_count'  => $product_price_items_count,
			'product_cls_items'          => $product_cls_items,
			'sku'                        => $product->sku,
			'show'                       => $product->show,
			'new'                        => $product->new,
			'model_id'                   => $product->modelId,
			'model_name'                 => $model->name,
			'images'                     => $images,
			'manufacturer_id'            => $product->manufacturerId,
			'manufacturer_name'          => $manufacturer->name,
			'tabs'                       => $tab_items,
			'tabs_content'               => $tab_content_items
		)
	);
	$page->title = $pTitle;
	$page->h1 = $pTitle;

	$renderer = new Renderer($page);
	$renderer->output(); 
?>