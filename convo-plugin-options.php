<?php
$convo_plugin_name = "NajeebMedia Private Conversation";
$convo_short_name = 'nmconvo';
$convo_option_name = 'nm-convo';

// Create Plugin nm_convo_options

$nm_convo_options = array (

array( "name" => $convo_plugin_name." Options",
	"type" => "title"),

	array( 	"name" => __("General Settings", $convo_short_name),	
		"type" => "section"),	
		array( "type" => "open"),
		
		
		array(  "name" => __("Conversations Limit", $convo_short_name),
				"desc" => __("How many conversations do you want to list on a page/view", $convo_short_name),
				"id" => $convo_short_name."_convo_limit",
				"type" => "text",
				"std"	=> 3),		
	  			
		array( "type" => "close"),
		
		array( 	"name" => __("Dialog Messages", $convo_short_name),	
		"type" => "section"),	
		array( "type" => "open"),
		
		array( 	"name" => __("Convo Sent Conversation", $convo_short_name),
		  		"desc" => __("Type a message here, it will be shown when user will send/reply  conversation. <br />e.g: Conversation is sent successfully", $convo_short_name),
				"id" => $convo_short_name."_sent_message",
				"type" => "textarea",
				"std" => "Conversation is sent successfully"),
				
	  array( 	"name" => __("User does not Exist Message", $convo_short_name),
		  		"desc" => __("Type a message here, it will be shown when user type invalid username. <br />e.g: User not found", $convo_short_name),
				"id" => $convo_short_name."_not_user_message",
				"type" => "textarea",
				"std" => "User not found"),
				
		 array( 	"name" => __("Convo Delete Conversation", $convo_short_name),
		  		"desc" => __("Type a message here, it will be shown when user delete conversation(s). <br />e.g: Conversation(s) deleted successfully", $convo_short_name),
				"id" => $convo_short_name."_delete_message",
				"type" => "textarea",
				"std" => "Conversation(s) deleted successfully"),	
		
		
		array( "type" => "close"),
		
		
		array( 	"name" => __("Email Notification Settings", $convo_short_name),	
				"type" => "section"),	
		array( "type" => "open"),
		
		
		array(  "name" => __("Subject", $convo_short_name),
				"desc" => __("This subject will be used when new conversation is started", $convo_short_name),
				"id" => $convo_short_name."_email_new_subject",
				"type" => "textarea",
				"std"	=> ''),

		
				
		array(  "name" => __("Message Body", $convo_short_name),
				"desc" => __("This message will be sent to user on when new conversation is started. User shortcodes e.g:<br />
[sendername]<br />
[receivername]<br />
[subject]<br />
[convourl]", $convo_short_name),
				"id" => $convo_short_name."_email_new_message",
				"type" => "textarea",
				"std"	=> ''),
				
		
		array(  "name" => __("Subject", $convo_short_name),
				"desc" => __("This subject will be used when message will be replied. User shortcodes e.g:<br />
[sendername]<br />
[receivername]<br />
[convourl]", $convo_short_name),
				"id" => $convo_short_name."_email_reply_subject",
				"type" => "text",
				"std"	=> ''),
				
		array(  "name" => __("Message Body", $convo_short_name),
				"desc" => __("This message will be sent to user on when his message will be replied by other user", $convo_short_name),
				"id" => $convo_short_name."_email_reply_message",
				"type" => "textarea",
				"std"	=> ''),
				
				
		array(  "name" => __("Message Footer", $convo_short_name),
				"desc" => __("It will be append to each message sent to user", $convo_short_name),
				"id" => $convo_short_name."_email_footer",
				"type" => "textarea",
				"std"	=> ''),
				
		
				
		array( "type" => "close"),
		


);	//end of nm_convo_options array
											
											

function nm_convo_add_admin() {

    global $convo_plugin_name, $convo_short_name, $nm_convo_options, $convo_option_name;
	
    if ( @$_GET['page'] == $convo_option_name ) {
    
        if ( 'save' == $_REQUEST['action'] ) {

                foreach ($nm_convo_options as $value) {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }

                foreach ($nm_convo_options as $value) {
                    if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }

                header("Location: plugins.php?page=$convo_option_name&saved=true");
                die;

        } else if( 'reset' == $_REQUEST['action'] ) {

            foreach ($nm_convo_options as $value) {
                delete_option( $value['id'] ); }

            header("Location: plugins.php?page=$convo_option_name&reset=true");
            die;

        } 
    }

 
	add_menu_page( 	$convo_plugin_name, 	
					"Nmedia Convo", 
					'edit_plugins', 
					$convo_option_name, 
					'nm_convo_admin', 
					plugin_dir_url(__FILE__ ).'images/option.png');
					
	add_submenu_page( $convo_option_name,
					  'Convo List', 
					  'Convo List', 
					  'manage_options', 
					  'nm-convo-list', 
					  array('nmMemberConvo', 'listUserConvos'));

}


function nm_convo_add_init() {
  	wp_register_style('nm_convo_option_style', plugins_url('options.css', __FILE__));
	wp_enqueue_style( 'nm_convo_option_style');
	
	wp_enqueue_script("nm_convo_script", plugins_url('js/nm_plugin_option.js', __FILE__), false, "1.0"); 
	
}


function nm_convo_admin() {

    global $convo_plugin_name, $convo_short_name, $nm_convo_options, $nm_bgs;
	//print_r($nm_convo_options);
	

    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$convo_plugin_name.' '.__('Settings saved.',$convo_short_name).'</strong></p></div>';
    if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$convo_plugin_name.' '.__('Settings reset.',$convo_short_name).'</strong></p></div>';
    if ( $_REQUEST['reset_widgets'] ) echo '<div id="message" class="updated fade"><p><strong>'.$convo_plugin_name.' '.__('Widgets reset.',$convo_short_name).'</strong></p></div>';
    
?>
<div class="wrap rm_wrap">
<h2><?php echo $convo_plugin_name; ?> Settings</h2>

<div class="rm_opts">
<form method="post">

<?php 
foreach ($nm_convo_options as $value) {
switch ( $value['type'] ) {

case "open":
?>

<?php break;

case "close":
?>

</div>
</div>
<br />

<?php break;

case "title":
?>

<?php break;

case 'text':
?>

<div class="rm_input rm_text">
	<label for="<?php echo $value['id']; ?>"><?php _e($value['name'], $convo_short_name) ?></label>
 	<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id'])  ); } else { echo $value['std']; } ?>" />
 <small><?php _e($value['desc'], $convo_short_name) ?></small><div class="clearfix"></div>

 </div>
<?php
break;

case 'textarea':
?>

<div class="rm_input rm_textarea">
	<label for="<?php echo $value['id']; ?>"><?php _e($value['name'], $convo_short_name) ?></label>
 	<textarea name="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo $value['std']; } ?></textarea>
 <small><?php _e($value['desc'], $convo_short_name) ?></small><div class="clearfix"></div>

 </div>

<?php
break;

case 'bgs'		//custom field set by Najeeb
?>

<div class="rm_input">
	<div style="float:left; width:200px;">
	<label for="<?php echo $value['id']; ?>"><?php _e($value['name'], $convo_short_name) ?></label>
    </div>
    <div class="nm_bgs">
    <?php foreach($nm_bgs as $bg => $val):
	$bg_img_name = 'images/'.$val;
	?>
    <div class="item">
        	<img src="<?php echo plugins_url($bg_img_name, __FILE__)?>" alt="<?php echo $bg ?>" width="75" /><br />
			<input type="radio" name="<?php echo $value['id']; ?>" value="<?php echo $val?>" <?php if (get_option( $value['id'] ) == $val) { echo 'checked="checked"'; } ?> />
            <?php echo $bg ?>
        </div>
    <?php endforeach;?>
        
        <div class="clearfix"></div>
        </div>
 
    <small><?php _e($value['desc'], $convo_short_name) ?></small>
 	<div class="clearfix"></div>

 </div>

<?php
break;

case 'select':
?>

<div class="rm_input rm_select">
	<label for="<?php echo $value['id']; ?>"><?php _e($value['name'], $convo_short_name) ?></label>

<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
<?php foreach ($value['nm_convo_options'] as $option) { ?>
		<option <?php if (get_option( $value['id'] ) == $option) { echo 'selected="selected"'; } ?>><?php echo $option; ?></option><?php } ?>
</select>

	<small><?php _e($value['desc'], $convo_short_name) ?></small><div class="clearfix"></div>
</div>
<?php
break;

case "checkbox":
?>

<div class="rm_input rm_checkbox">
	<label for="<?php echo $value['id']; ?>"><?php _e($value['name'], $convo_short_name) ?></label>

<?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />

	<small><?php _e($value['desc'], $convo_short_name) ?></small><div class="clearfix"></div>
 </div>
<?php break;
case "section":

$i++;
?>

<div class="rm_section">
<div class="rm_title"><h3><img src="<?php plugins_url('css/images/trans.gif', __FILE__)?>" class="inactive" alt="""><?php _e($value['name'], $convo_short_name) ?></h3><span class="submit"><input name="save<?php echo $i; ?>" type="submit" value="<?php _e('Save Changes', $convo_short_name)?>" />
</span><div class="clearfix"></div></div>
<div class="rm_options">

<?php break;

}
}
?>

<input type="hidden" name="action" value="save" />
</form>
<form method="post">
<p class="submit">
<input name="reset" type="submit" value="<?php _e('Reset', $convo_short_name)?>" />
<input type="hidden" name="action" value="reset" />
</p>
</form>
<div style="font-size:9px; margin-bottom:10px;">2012 © <a href="http://www.najeebmedia.com">Nmedia</a></div>
 </div> 

<?php
// get company ad
$file = dirname(__FILE__).'/nmedia-ad.php';
include($file);
}
/*add_action('admin_menu', 'mytheme_add_admin');*/
add_action('admin_init', 'nm_convo_add_init');
add_action('admin_menu' , 'nm_convo_add_admin');
