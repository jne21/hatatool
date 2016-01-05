<?
	require('inc/authent.php');

	use CMS\Page;
	use CMS\Renderer;

	$pTitle = "Керуючий інтерфейс";

	$page = new Page;
	$renderer = new Renderer($page);
	$renderer->output();

?>
