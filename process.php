<?php
/*
**processing header reqeust
*/

global $current_user;
get_currentuserinfo();

//print_r($_POST);

$arrUsers = get_users();


/* replying convo */
if(isset($_POST['reply-convo']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'nm-convo-nonce-reply'))
{
	nmMemberConvo::$message = sanitize_text_field($_POST['nm-reply']);
	nmMemberConvo::$files 	= sanitize_text_field($_POST['file-name-reply']);
	
	$receiverID = nmMemberConvo::getOtherBuddyID(intval($_POST['reply-c-id']), $current_user->ID);
	
	if(nmMemberConvo::replyConvo($_POST['reply-c-id'], $receiverID))
	{
		
		$receiver_info = get_userdata($receiverID);
		//var_dump($receiver_info);
		
		nmMemberConvo::sendEmailNotification($receiver_info -> user_email, 
											 $receiver_info -> user_login,
											 $current_user -> user_login,
											 true);
		
		echo "<div class=\"green\">". get_option(nmMemberConvo::$short_name.'_sent_message') ."</div>";
	}
}


/* saving new convo */
if(isset($_POST['nm-new-convo']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'nm-convo-nonce-new'))
{
	//getting user id from $arrUsers
	$toEmail = '';
	$toName = '';
	foreach($arrUsers as $u)
	{
		if($u -> user_login == $_POST['started_with'])
		{
			nmMemberConvo::$started_with = $u -> ID;
			$toEmail = $u -> user_email;
			$toName = $u -> user_login;
		}
	}
	
	
	if(nmMemberConvo::$started_with != '')
	{
		nmMemberConvo::$started_by 	 = $current_user -> ID;
		nmMemberConvo::$subject 	 = sanitize_text_field($_POST['subject']);
		nmMemberConvo::$message 	 = sanitize_text_field($_POST['message']);
		nmMemberConvo::$files 		 = sanitize_text_field($_POST['file-name']);
		
		if(nmMemberConvo::sendConvo())
		{
			nmMemberConvo::sendEmailNotification($toEmail, $toName, $current_user -> user_login);
			echo "<div class=\"green\">". get_option(nmMemberConvo::$short_name.'_sent_message') ."</div>";
		}
	}else
	{
		echo "<div class=\"green\">". get_option(nmMemberConvo::$short_name.'_not_user_message') ."</div>";
	}
	
}


/* deleting convo */
if(isset($_POST['nm-delete-convo']))
{
	//print_r($_POST);
	if(is_array($_POST['convos'])){
		foreach($_POST['convos'] as $cid)
		{
			if($res = nmMemberConvo::deleteConvo($cid))
				echo "<div class=\"green\">". $res .' '. get_option(nmMemberConvo::$short_name.'_delete_message') ."</div>";
			else
				$res = false;
		}
	}
}



$arrConvo = nmMemberConvo::getUserConvos();
//print_r($arrConvo);

/* pagination stuff */
nmMemberConvo::$convo_per_page = ( get_option(nmMemberConvo::$short_name.'_convo_limit') == 0) ? 3 : get_option(nmMemberConvo::$short_name.'_convo_limit');
nmMemberConvo::$total_pages = ceil(count($arrConvo) / nmMemberConvo::$convo_per_page);

?>

<script language="javascript">
// js global variables populated from server side script
var url_convo_detail = '<?php echo plugins_url('load-convo-detail.php', __FILE__);?>';
var dir_path = '<?php echo ABSPATH;?>';
scriptAr = new Array(); // initializing the javascript array
plugin_path = '<?php echo dirname(__FILE__)?>';
plugin_url = '<?php echo plugins_url('', __FILE__)?>';

<?php

//In the below lines we get the values of the php array one by one and update it in the script array.
foreach ($arrUsers as $u)
{
	$username = $u -> user_login;
	echo "scriptAr.push(\"$username\" );"; // This line updates the script array with new entry
}
?>

</script>