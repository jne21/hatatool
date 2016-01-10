<?
	$only_admin = TRUE;

	require('inc/authent.php');
	if ($name = $_GET['value']) {
		$value = $_GET['value'].'';
		$registry->get('setup')->updateValue($name, $value);
	}
	echo $value;
?>