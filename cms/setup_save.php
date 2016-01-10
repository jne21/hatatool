<?
	require('inc/authent.php');
	if ($name = $_GET['name']) {
		$value = $_GET['value'].'';
		$registry->get('setup')->updateValue($name, $value);
	}
	echo $value;
?>