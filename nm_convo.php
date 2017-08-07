<?php
/*
 Plugin Name: Nmedia Member Private Conversation
Plugin URI: http://www.najeebmedia.com
Description: This Plugin is developed by NajeebMedia.com
Version: 1.7
Author: Najeeb Ahmad
Author URI: http://www.najeebmedia.com/
*/

/*ini_set('display_errors',1);
 error_reporting(E_ALL);*/

//loading main convo class
include_once ("class.convo.php");


register_activation_hook(__FILE__, array('nmMemberConvo','nmconvo_install'));

//add_action('admin_menu', array('nmMemberConvo', 'set_up_admin_page'));



function convo_front_script() {

	wp_enqueue_script( 'jquery' );


	wp_register_script( 'nmconvo_jquery_ui', plugins_url('js/ui/jquery-ui-1.8.16.custom.min.js', __FILE__), 'jquery');
	wp_enqueue_script('nmconvo_jquery_ui');
	
	/* for uploadify */
	wp_register_script('nmswf_script', plugins_url('js/uploadify/swfobject.js', __FILE__));
	wp_enqueue_script('nmswf_script');
	
	 wp_register_script('file_attachment_script', plugins_url('js/uploadify/jquery.uploadify.v2.1.4.min.js', __FILE__));
	 wp_enqueue_script('file_attachment_script');

	/* wp_register_script('file_attachment_script', plugins_url('js/uploadify-v3.1/jquery.uploadify-3.1.min.js', __FILE__));
	wp_enqueue_script('file_attachment_script'); */
	/* for uploadify */

	wp_register_script( 'nmconvo_template_custom_script', plugins_url('js/template_custom.js', __FILE__), '_jquery_ui');
	wp_enqueue_script('nmconvo_template_custom_script');

	$nonce= wp_create_nonce  ('convo-nonce');
	
	
	global $user_login;
	get_currentuserinfo();
	
	
	wp_enqueue_script( 'convo_ajax', plugin_dir_url( __FILE__ ) . 'js/ajax.js', array( 'jquery' ) );
	wp_localize_script( 'convo_ajax', 'convo_vars', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'convo_token'	=> $nonce,
			'convo_plugin_url' => plugin_dir_url( __FILE__ ),
			'current_user'	=> $user_login,
			'file_attached_message'	=> __(' attached successfully', nmMemberConvo::$short_name)
	) );
}

// activate textdomain for translations
add_action('init', array('nmMemberConvo', 'wpp_textdomain'));

add_shortcode( 'nm-wp-convo', array('nmMemberConvo', 'renderUserArea'));

//unread box alert
add_shortcode( 'nm-convo-alertbox', array('nmMemberConvo', 'renderAlertBox'));
add_filter('widget_text', 'do_shortcode');


add_action('wp_print_styles', array('nmMemberConvo', 'nmConvoStyle'));

add_action('wp_enqueue_scripts', 'convo_front_script');


function convo_post_file(){
	
	nmMemberConvo::uploadFile($_REQUEST['username']);
	
	die(0);
}

add_action( 'wp_ajax_nopriv_convo_file', 'convo_post_file' );
add_action( 'wp_ajax_convo_file', 'convo_post_file' );


function convo_post_ajax(){
	
	$nonce = $_REQUEST['convo_token'];
	 
	if ( ! wp_verify_nonce( trim($nonce), 'convo-nonce' ) )
		//die ( 'Busted! '.$nonce); 
		wp_nonce_field('convo-nonce');

	nmMemberConvo::markAsRead(intval($_POST['convo_id']));
	
	die(0);
}
add_action( 'wp_ajax_nopriv_convo_action', 'convo_post_ajax' );
add_action( 'wp_ajax_convo_action', 'convo_post_ajax' );


$options_file = dirname(__FILE__).'/convo-plugin-options.php';
include ($options_file);


?>
