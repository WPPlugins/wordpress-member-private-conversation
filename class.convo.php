<?php

class nmMemberConvo {

	var $nmconvo_db_version = "1.0";

	/*
	 **data vars
	*/

	static $started_by;
	static $started_with;
	static $subject;
	static $message;
	static $files;


	/*
	 ** pagination vars
	*/

	static $convo_row_count;
	static $convo_per_page = 5;
	static $total_pages;
	static $total_convos;


	/*
	 ** file attachment setting vars
	*/
	static $multiAllow = 'false';
	static $fileLimit;
	static $fileExt;
	static $fileSize;

	/*
	 ** plugin short name
	*/
	static $short_name = 'nmconvo';


	/*
	 ** plugin table name
	*/
	static $tblName = 'nm_convo';



	function renderUserArea()
	{


		if ( is_user_logged_in() ) {

			global $wpdb ;
			global $current_user;
			get_currentuserinfo();

			nmMemberConvo::applyFileAttachmentSettings();

			ob_start();
			$file = dirname(__FILE__).'/_template_convo.php';
			include($file);
			$output_string = ob_get_contents();
			ob_end_clean();
			return $output_string;
		}
		else
		{

			/*wp_redirect( home_url() ); exit;*/
			echo '<script type="text/javascript">
			window.location = "'.wp_login_url( get_permalink() ).'"
			</script>';
		}

	}


	
	/*
	 * rendering unread messages 
	 */
	
	function renderAlertBox($atts){
		
		extract(shortcode_atts(array(
				'pageurl' 		=> ''
		), $atts));
		
		
		ob_start();
		$unread = nmMemberConvo::unreadConvoAlert();
		$unread = '<a href="'."{$pageurl}".'">'.$unread.'</a>';
		echo $unread;
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
		
				
	}
	/*
	 ** this function setting is setting file attachment vars
	*/
	function applyFileAttachmentSettings()
	{

		nmMemberConvo::$fileLimit = get_option('nmconvo_file_limit');

		if(nmMemberConvo::$fileLimit > 1)
			nmMemberConvo::$multiAllow = "true";
			
		nmMemberConvo::$fileExt = get_option('nmconvo_file_ext');

		nmMemberConvo::$fileSize = get_option('nmconvo_size_limit');
	}
	
	// plugin localization
	function wpp_textdomain() {
		load_plugin_textdomain(nmMemberConvo::$short_name, false, dirname(plugin_basename( __FILE__ )) . '/locale/');
	}


	/*
	 ** This function is making directory in follownig path
	** wp-content/uploads/user_uploads
	*/

	function makeUploadDirectory()
	{
		nmMemberConvo::$pathUploads = ABSPATH . 'wp-content/uploads/user_uploads/';
		if(!is_dir(nmMemberConvo::$pathUploads))
		{
			if(mkdir(nmMemberConvo::$pathUploads, 0777))
				return true;
			else
				return false;
		}
		else
		{
			return true;
		}
	}


	/*
	 ** Installing database table for this plugin: nm_convo
	*/
	public function nmconvo_install() {
		global $wpdb;
		global $nmconvo_db_version;

		$table_name = $wpdb->prefix . nmMemberConvo::$tblName;

		$sql = "CREATE TABLE `$table_name` (
		`convo_id` INT( 8 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`started_by` INT( 7 ) NOT NULL ,
		`started_with` INT( 7 ) NOT NULL ,
		`subject` VARCHAR( 150 ) NOT NULL,
		`convo_thread` MEDIUMTEXT NOT NULL ,
		`read_by` VARCHAR( 150 ) DEFAULT '0',
		`deleted_by` VARCHAR( 150 ) DEFAULT '0',
		`last_sent_by` INT( 7 ) NOT NULL ,
		`sent_on` DATETIME NOT NULL
		);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		add_option("nmconvo_db_version", $nmconvo_db_version);
	}


	/*
	 ** It is making title with suject and latest message excerpt
	*/
	public function convoTitle($subject, $thread)
	{
		$thread = json_decode($thread);

		//Getting last message array
		$lastChunk = end($thread);
		$lastMessage = stripslashes(nmMemberConvo::fixLengthWords($lastChunk -> message, 6));
		//print_r($lastMessage);

		$html = "<strong>".stripslashes($subject)."</strong>";
		$html .= "<span style=\"color:#999\"> - $lastMessage</span>";
		return $html;
	}


	/*
	 ** It is making Parties (buddies names)
	*/
	public function convoParties($thread, $current_user_name)
	{
		$thread = json_decode($thread);

		//Getting last message array
		$lastChunk = end($thread);

		//check it is first convo
		if($lastChunk -> username == $current_user_name)
		{
			$user_info = get_userdata($started_with);
			$html = "<strong>".__('me', nmMemberConvo::$short_name)."</strong>, ".$lastChunk -> sentto;
		}
		else
		{
			$html = __('me', nmMemberConvo::$short_name) . ", <strong>".$lastChunk -> username."</strong>";
		}
			
		return $html;
	}



	/*
	 Listing user files in admin
	*/
	public function renderListings()
	{
		$file = dirname(__FILE__).'/listings.php';
		include($file);
	}



	public function set_up_admin_page () {

		add_menu_page(	'UserConvo',
				'NM Convo',
				'manage_options',
				'nmconvo-settings',
				array('nmMemberConvo', 'show_admin_options'),
				'');

	}


	public function show_admin_options()
	{
		$file = dirname(__FILE__).'/options.php';
		include($file);
	}


	/*
	 ** sending convo to user, NEW convo
	*/
	public function sendConvo()
	{
		global $current_user;
		get_currentuserinfo();

		global $wpdb;

		$userinfo = get_userdata(nmMemberConvo::$started_with);

		$thread[] = array( 	'username'	=> $current_user -> user_login,
				'sentto'	=> $userinfo -> user_login,
				'userid'	=> $current_user -> ID,
				'message'	=> nmMemberConvo::$message,
				'files'		=> nmMemberConvo::$files,
				'senton'	=> time(),
		);


		$dt = array('started_by'	=> $current_user -> ID,
				'started_with'	=> nmMemberConvo::$started_with,
				'subject'		=> nmMemberConvo::$subject,
				'convo_thread'	=> json_encode($thread),
				'last_sent_by'	=> $current_user -> ID,
				'sent_on'		=> current_time('mysql')
		);
		$format = array('%d', '%d', '%s', '%s', '%d', '%s');


		/*var_dump($dt);
		 exit;*/

		$wpdb -> insert($wpdb->prefix . nmMemberConvo::$tblName,
				$dt,
				$format
		);

		if($wpdb->insert_id)
			return true;
		else
			return false;

		//$wpdb->print_error();
	}


	/*
	 ** this function getting other buddy id
	*/

	function getOtherBuddyID($convoID, $userID)
	{
		global $wpdb;

		$convo = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix . nmMemberConvo::$tblName."
				WHERE convo_id = $convoID");

		if($userID == $convo -> started_by)
			return $convo -> started_with;
		else
			return $convo -> started_by;

	}

	/*
	 **replying convo
	*/

	function replyConvo($convoID, $sentTo)
	{
		global $wpdb;
		global $current_user;
		get_currentuserinfo();

		$thread = array();

		$convo = $wpdb->get_row($wpdb -> prepare("SELECT * FROM ".$wpdb->prefix . nmMemberConvo::$tblName."
				WHERE convo_id = %d", $convoID));

		//getting chunk
		$thread = json_decode($convo -> convo_thread, true);

		$userinfo = get_userdata($sentTo);
		//updating chunk
		$thread[] = array( 	'username'	=> $current_user -> user_login,
				'sentto'	=> $userinfo -> user_login,
				'userid'	=> $current_user -> ID,
				'message'	=> nmMemberConvo::$message,
				'files'		=> nmMemberConvo::$files,
				'senton'	=> time(),
		);
		
		/*echo "<pre>";
		 print_r($thread);
		echo "</pre>";
		exit;*/

		$dt = array('convo_thread'	=> json_encode($thread),
				'last_sent_by'	=> $current_user -> ID,
				'read_by'		=> 0,
				'sent_on'		=> current_time('mysql')
		);


		$where = array('convo_id'	=> $convoID);

		$res = $wpdb -> update($wpdb->prefix . nmMemberConvo::$tblName,$dt, $where,
				array('%s', '%d', '%d', '%s'), array('%d'));

		
		//Now update delete_by if other user is deleted the message
		//this bug is identified by Albert Brückmann <mail@albertbrueckmann.de>
		//Jan 6, 2013
		
			
		$res = $wpdb->update($wpdb->prefix . nmMemberConvo::$tblName,
				array('deleted_by'	=> 0),
				array('convo_id' 	=> $convoID)
		);

			
		return $res;
	}


	/*
	 ** Get Current User Conversations
	*/

	function getUserConvos()
	{
		//echo "hello";
		global $wpdb;
		global $user_ID;

		$myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix . nmMemberConvo::$tblName."
				WHERE (started_by = $user_ID
				OR started_with = $user_ID)
				AND (deleted_by != $user_ID
				AND deleted_by NOT LIKE '$user_ID,%'
				AND deleted_by NOT LIKE '%,$user_ID')
				ORDER BY sent_on DESC");

		/*$wpdb->show_errors();
		 $wpdb->print_error(); */

		nmMemberConvo::$total_convos = nmMemberConvo::inboxCount(count($myrows));


		return $myrows;
	}

	/*
	 ** Get all conversations for admin view
	*/

	function getAllConvos()
	{
		//echo "hello";
		global $wpdb;
		global $user_ID;

		$myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix . nmMemberConvo::$tblName."
				ORDER BY sent_on DESC");

		/*$wpdb->show_errors();
		 $wpdb->print_error(); */

		nmMemberConvo::$total_convos = nmMemberConvo::inboxCount(count($myrows));


		return $myrows;
	}
	
	/*
	 * upload file
	 */
	
	function uploadFile($username){
		
						
		$upload_dir = wp_upload_dir();
		$path_folder = $upload_dir['basedir'].'/user_uploads/'.$username.'/';
		
		if (!empty($_FILES)) {
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$targetPath = $path_folder;
			$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];
		
			// Validate the file type
			$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
			$fileParts = pathinfo($_FILES['Filedata']['name']);
		
			if (in_array($fileParts['extension'],$fileTypes)) {
				move_uploaded_file($tempFile,$targetFile);
				echo '1';
			} else {
				echo 'Invalid file type.';
			}
		}
	}


	/*
	 ** Deleting convo,
	*/

	function deleteConvo($cid)
	{
		//echo "hello";
		global $wpdb;
		global $user_ID;

		/*$del = $wpdb->get_col( "SELECT deleted_by FROM ".$wpdb->prefix . nmMemberConvo::$tblName."
		 WHERE convo_id = $cid");*/

		$convo = $wpdb -> get_row("SELECT convo_thread, deleted_by
				FROM ".$wpdb->prefix . nmMemberConvo::$tblName."
				WHERE convo_id = $cid");

		//var_dump($convo);
		//getting chunk
		$thread = json_decode($convo -> convo_thread);


		if($convo -> deleted_by == 0)
		{
			$deleted_by = $user_ID;
				
			$res = $wpdb->update($wpdb->prefix . nmMemberConvo::$tblName,
					array('deleted_by'	=> $deleted_by),
					array('convo_id' 	=> $cid)
			);
		}
		else
		{
			// so, both buddies deleted this convo
			$res = $wpdb->query("DELETE FROM ".$wpdb->prefix . nmMemberConvo::$tblName."
					WHERE convo_id = $cid");


			//deleting attachments
			$upload_dir = wp_upload_dir();
			$path_folder = $upload_dir['basedir'].'/user_uploads/';
			//var_dump($thread);
			foreach($thread as $t)
			{
				if($t -> files)
				{
					$files = explode(',', $t -> files);
					foreach($files as $f)
					{
						$file_path = $path_folder . $t -> username .'/'.$f;
						$res = @unlink($file_path);
						echo 'file path '.$file_path;
						echo "<br />";
					}
				}
			}
		}

		/*$wpdb->show_errors();
		 $wpdb->print_error(); */

		return $res;
	}



	/*
	 ** Get Convo Detail
	*/

	function getConvoDetail($convoID)
	{
		//echo "hello";
		global $wpdb;

		$myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix . nmMemberConvo::$tblName."
				WHERE convo_id= $convoID
				ORDER BY sent_on DESC");
		return $myrows[0];
	}


	/*
	 ** Helper: getting fix lenght of string
	*/
	function fixLengthWords($pStr,$pLength)
	{
		$length = $pLength; // The number of words you want

		$text = strip_tags($pStr);
		/*echo $text;
		 exit;*/
		$words = explode(' ', $text); // Creates an array of words
		$words = array_slice($words, 0, $length);
		$str = implode(' ', $words);

		$str .= (count($words) < $pLength) ? '' : '...';

		return $str;
	}


	/*
	 ** inbox counter rendering
	*/

	function inboxCount($total)
	{
		if($total <= 0)
		{
			return '';
		}
		else
		{
			return "($total)";
		}

	}


	/*
	 ** getting unread messages
	*/

	function unreadConvo()
	{
		//getting new message counter
		global $wpdb;
		global $user_ID;

		$myrows = $wpdb->get_results( "SELECT COUNT(*) AS UNREAD FROM ".$wpdb->prefix . nmMemberConvo::$tblName."
				WHERE (started_by = $user_ID
				OR started_with = $user_ID)
				AND last_sent_by != $user_ID
				AND read_by = 0");

		/* $wpdb->show_errors();
		 $wpdb->print_error();
		print_r($myrows); */

		if($myrows[0] -> UNREAD > 0)
		{
			?>
			
			<div class="nm-unread-alert"><?php _e("You have ", nmMemberConvo::$short_name)?>
			<?php echo $myrows[0] -> UNREAD?>
			<?php _e(" unread conversations", nmMemberConvo::$short_name)?>
			</div>
			<?php 
		}
	}
	
	
	/*
	 * rendering unread messages for shortcode
	 */
	function unreadConvoAlert()
	{
		//getting new message counter
		global $wpdb;
		global $user_ID;
	
		$myrows = $wpdb->get_results( "SELECT COUNT(*) AS UNREAD FROM ".$wpdb->prefix . nmMemberConvo::$tblName."
				WHERE (started_by = $user_ID
				OR started_with = $user_ID)
				AND last_sent_by != $user_ID
				AND read_by = 0
				AND deleted_by = 0");
	
				/* $wpdb->show_errors();
				 $wpdb->print_error();
				print_r($myrows); */
	
		if($myrows[0] -> UNREAD > 0)
		{
			return '<div class="nm-unread-alert">'.__('You have '.$myrows[0] -> UNREAD.' unread conversations', nmMemberConvo::$short_name).'</div>';
		}
	}

	/*
	 ** marking conversation as read
	*/
	function markAsRead($convo_id)
	{
		global $wpdb;
		global $user_ID;

		$dt = array('read_by'	=> $user_ID);


		$where = array('convo_id'	=> $convo_id);
		/*$where = "WHERE convo_id = $convo_id
		 AND last_sent_by != $user_ID";*/

		$res = $wpdb -> update($wpdb->prefix . nmMemberConvo::$tblName,$dt, $where, array('%d'), array('%d'));

		/*$wpdb->show_errors();
		 $wpdb->print_error();*/
	}


	/*
	 ** this is setting users array based on IDs so easily can get user info in_array index
	*/
	function setUsersByID($arrUsers)
	{
		$arr = array();
		foreach($arrUsers as $user)
		{
			$arr[$user -> ID] = $user;
		}

		/*echo "<pre>";
		 print_r($arr);
		echo "</pre>";*/
		return $arr;

	}

	/*
	 ** User conversation list
	*/

	function listUserConvos()
	{
		$file = dirname(__FILE__).'/listings-all.php';
		include($file);
	}

	/*
	 ** sending user an email with instructino/attachment
	*/

	function sendEmailNotification($toEmail, $toName, $senderName, $isReply = false)
	{
		//date_default_timezone_set('America/Toronto');
		//echo $toEmail.' '.$toName.' '.$senderName; return;

		if($isReply)
		{
			$subject    = get_option('nmconvo_email_reply_subject');
			$body             = get_option('nmconvo_email_reply_message');
		}
		else
		{
			$subject    = get_option('nmconvo_email_new_subject');
			$body             = get_option('nmconvo_email_new_message');
		}


		$body             = eregi_replace("[\]",'',$body);
		$body             = str_replace("[sendername]",$senderName,$body);
		$body             = str_replace("[receivername]",$toName,$body);
		$body             = str_replace("[subject]",nmMemberConvo::$subject ,$body);
		$body 			  = str_replace("[convourl]", get_permalink(), $body);


		$body .= get_option('nmconvo_email_footer');
		$body             = str_replace("\n",'<br />',$body);


		$host_name = str_replace('www.', '', $_SERVER['HTTP_HOST']);

		$from = 'donotreply@'.$host_name;
		$reply = $from;
			
		$headers = 'From: Admin<'.$from.'>' . "\r\n";
		$headers .= 'Reply-To: Do not reply<'.$reply.'>' . "\r\n";

		add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));

		wp_mail($toEmail, $subject, $body, $headers);

		/*$php_mailer_path = dirname(__FILE__) . '/mailer/class.phpmailer.php';

		if(file_exists($php_mailer_path))
			require_once($php_mailer_path);
		else
			die("file not loaded ".$php_mailer_path);

		$mail             = new PHPMailer(); // defaults to using php "mail()"

		$mail->IsSendmail(); // telling the class to use SendMail transport

		//$mail->IsSMTP(); // telling the class to use SMTP
		//$mail->Host       = "mail.".$_SERVER['HTTP_HOST']; // SMTP server
		//$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
		// 1 = errors and messages
		// 2 = messages only




		$mail->AddReplyTo("donotreply@".$_SERVER['HTTP_HOST'],"donotreply");

		$mail->SetFrom('donotreply@'.$_SERVER['HTTP_HOST'], 'Admin');

		$mail->AddAddress($toEmail, $toName);

			
			
			
		$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

		$mail->MsgHTML($body);


		if(!$mail->Send()) {
		echo "Mailer Error: " . $mail->ErrorInfo;
		} else {
		//echo "Message sent!";
		return true;
		}*/

	}


	/*
	 ** attach style for plugin
	*/

	function nmConvoStyle()
	{
		//jquery ui
		wp_register_style('nmconvo_ui_stylesheet', plugins_url('js/ui/ui-lightness/jquery-ui-1.8.16.custom.css', __FILE__));
		wp_enqueue_style('nmconvo_ui_stylesheet');


		wp_register_style('file_attachment_stylesheet', plugins_url('js/uploadify/uploadify.css', __FILE__));
		wp_enqueue_style( 'file_attachment_stylesheet');

		//loading tempalte style
		$current_template = 'default';		//get_option('nm_convo_template');
		wp_register_style('_template_stylesheet', plugins_url('templates/'.$current_template.'/css/styles.css', __FILE__));
		wp_enqueue_style( '_template_stylesheet');
	}


	/*
	 ** attach style for convo detail in admin
	*/

	function nmConvoStyleAdmin()
	{
		echo '<style type="text/css">ul.nm-convo-detail
		{
		margin:0;
		padding:0;
	}
		
	ul.nm-convo-detail li
	{
	list-style:none;
	}
		
	ul.nm-convo-detail li.convo-head
	{
	font-weight:bold;
	border-bottom:#CCC 1px dashed;
	}
		
	ul.nm-convo-detail li.convo-text
	{
	margin-bottom:14px;
	}
		
		
	ul.nm-convo-detail li.convo-attachment
	{
	color:#666;
	font-style:italic;
	margin-bottom:25px;
	}
	</style>';
	}

	 
}