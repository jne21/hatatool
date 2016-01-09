<?
	require('inc/authent.php');

	use common\Page;
	use CMS\RendererCMS as Renderer;

	$pTitle = "Керуючий інтерфейс";

	$renderer = new Renderer(Page::MODE_NORMAL);
	$renderer->page->title = $pTitle;

	$renderer->output();

?>
