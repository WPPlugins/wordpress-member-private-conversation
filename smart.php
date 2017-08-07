<?php
/*
** this is smart script using wp core file out of the box, DO NOT touch it
*/

/* Loading wp core files */
$wp_path = dirname(__FILE__);
$generate_path = str_replace("wp-content/plugins/nm-wp-member-convo-pro", "wp-load.php", $wp_path);


if(file_exists($generate_path))
	require $generate_path;
else
	die('file could not be loaded');
	
//loading convo class
include_once ("class.convo.php");

$convo = new nmMemberConvo();

$convo -> markAsRead(sanitize_text_field((int)$_POST['convo_id']));
?>