<?php
/*
** This is main temlate for loading covosersation, 
do not change until you are like me (ceo@najeebmedia.com)
*/


$process = dirname(__FILE__).'/process.php';
include($process);

//rendering box if unread convo
nmMemberConvo::unreadConvo();
?>

<div id="convo-wrapper">
	<div id="left-area">
    	<ul>
            <li id="inbox"><a href="#"><?php _e('Inbox', nmMemberConvo::$short_name)?> 
            <?php echo nmMemberConvo::$total_convos?></a></li>
            <li id="compose"><a href="#"><?php _e('Start Convo', nmMemberConvo::$short_name)?></a></li>
        </ul>
    </div>
    
  <div id="right-area">
  <div id="inbox-panel">
  <?php
 	if(count($arrConvo) == 0):
  		echo '<p class="nm-notification">'.__("Inbox is empty", nmMemberConvo::$short_name).'</p>';
	else:
	?>
  <h2><?php _e('Inbox', nmMemberConvo::$short_name)?></h2>
  <form id="frm-new-convo" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI'])?>">
     
    <ul id="nm-convo-top">
    	<li>
        	<ul>
            	
                <li class="buddies"><input name="nm-delete-convo" type="submit" value="<?php _e('Deleted selected', nmMemberConvo::$short_name)?>" /></li>
                
            </ul>
        </li>
    </ul>
    
    <div id="nm-convo-container">
    <ul>        
        <?php
		$convo_row_count = 0;
		foreach($arrConvo as $convo)
		{
			$convo_row_count++;
			
			$parties = nmMemberConvo::convoParties( $convo -> convo_thread, $current_user->user_login);
			$title = nmMemberConvo::convoTitle($convo -> subject, $convo -> convo_thread);
			$page_number = ceil($convo_row_count / nmMemberConvo::$convo_per_page);
			
			$unread_class = '';
			if($convo -> last_sent_by != $current_user->ID and $convo -> read_by != $current_user->ID)
				$unread_class = 'unread';
			
			//$page_next = nmMemberConvo::getNextPage(
		?>
        <li style="display:none" class="nm-c-p-<?php echo $page_number?>">
        	<ul class="convo-row" id="convo-<?php echo $convo-> convo_id?>">
            	<li class="check"><input type="checkbox" name="convos[]" value="<?php echo $convo-> convo_id?>"/></li>
                <li class="buddies <?php echo $unread_class?>" onclick="loadConvoHistory(<?php echo $convo-> convo_id?>)"><?php echo $parties?></li>
                <li class="title" onclick="loadConvoHistory(<?php echo $convo-> convo_id?>)"><?php echo $title?></li>
                <li class="time" onclick="loadConvoHistory(<?php echo $convo-> convo_id?>)"><?php echo date('M-d,y i:s', strtotime($convo -> sent_on))?></li>
            </ul>
        </li>
        <?php
		}
	 	?>
  	</ul>
    
    <div class="fix_height"></div>
    </div>
        
       <ul id="nm-convo-bottom">
        
        <li>
        	<ul>
            	<li id="prev-page"><a href="javascript:loadConvoPagePrev()">&laquo; Prev</a></li>
                <li id="page-count">2 of 11</li>
                <li id="next-page"><a href="javascript:loadConvoPageNext()">Next &raquo;</a></li>
            </ul>
        </li>  
        </ul>
		<script type="text/javascript">
			total_pages = <?php echo nmMemberConvo::$total_pages?>;
			setPagination();
			
		</script>
    </form>
    <?php endif; //27: if(nmMemberConvo::$total_convos == 0): ?>
    </div>	<!-- inobox-panel -->
 
 
  

  <div id="compose-panel">
  <h2><?php _e('Write New Message', nmMemberConvo::$short_name)?></h2>
  	<form id="frm-new-convo" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI'])?>">
  	<?php wp_nonce_field('nm-convo-nonce-new');?>
    	<table width="100%" border="0">
          <tr>
            <td><?php _e('Sent to', nmMemberConvo::$short_name)?>:</td>
            <td>&nbsp;</td>
            <td><input type="text" name="started_with" id="tags" /><br />
				<span class="error" id="start_with_err"><?php _e('Required', nmMemberConvo::$short_name)?></span></td>
          </tr>
          <tr>
            <td><?php _e('Subject', nmMemberConvo::$short_name)?>:</td>
            <td>&nbsp;</td>
            <td><input type="text" name="subject" id="subject" /><br />
            <span class="error" id="subject_err"><?php _e('Required', nmMemberConvo::$short_name)?></span></td>
          </tr>
          <tr>
            <td><?php _e('Message', nmMemberConvo::$short_name)?>:</td>
            <td>&nbsp;</td>
            <td><textarea name="message" id="message" cols="45" rows="5"></textarea><br />
				<span class="error" id="message_err"><?php _e('Required', nmMemberConvo::$short_name)?></span>
                </td>
          </tr>
          
          
          <!-- file attachement -->
          <?php
          if(get_option('nmconvo_allow_attachment'))
		  {		  
		  ?>
           <tr>
            <td><?php _e('Attach file', nmMemberConvo::$short_name)?>:</td>
            <td>&nbsp;</td>
            <td><input id="file_upload" name="file_upload" type="file" />
            	<input type="hidden" name="file-name" id="file-name">
			    <span id="upload-response"><?php echo $max_file_size?></span>
            </td>
          </tr>
          
          <?php
		  }
		  ?>
          
          
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><input type="submit" name="nm-new-convo" value="<?php _e('Send', nmMemberConvo::$short_name)?>" onclick="return validateCompose();" /></td>
            <td>&nbsp;</td>
          </tr>
        </table>

    </form>
  </div>		<!-- compose-panel -->
  
  
  <!-- convo detail -->
  <div id="convo-history-panel">
  	<h2 id="history-heading"></h2>
    <p><a class="back-to-convo" href="javascript:loadConvoCurrentPage()">&laquo; <?php _e('Back to Conversations', nmMemberConvo::$short_name)?></a></p>
    <p id="convo-detail-container">
    <img src="<?php echo plugins_url('images/loading.gif', __FILE__)?>" alt="Wait..." />
    </p>
    
    <p>    
    <h3><?php _e('Reply', nmMemberConvo::$short_name)?>:</h3>
    <form action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI'])?>" method="post">
    <?php wp_nonce_field('nm-convo-nonce-reply');?>
    <input type="hidden" name="reply-c-id" id="reply-c-id" value="" />
    <textarea name="nm-reply" id="nm-reply" rows="4" cols="60"></textarea><br />
    <span class="error" id="reply_err"><?php _e('Required', nmMemberConvo::$short_name)?></span><br />
    
	<?php
    if(get_option('nmconvo_allow_attachment'))
	{
	?>
    <input id="file_upload_reply" name="file_upload_reply" type="file" />
   	<input type="hidden" name="file-name-reply" id="file-name-reply">
    <span id="upload-response-reply"><?php echo $max_file_size?></span><br />

	
    <?php		
	}
	?>

	<input type="submit" value="<?php _e('Send', nmMemberConvo::$short_name)?>" name="reply-convo" onclick="return validateReply();" />
    </form>
    </p>
  </div>
  <!-- convo detail -->
 </div>
    
<div class="fix_height"></div>
</div>
