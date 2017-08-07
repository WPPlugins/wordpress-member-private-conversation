<?php
$arrUsers = get_users();
$usersByIDs = nmMemberConvo::setUsersByID($arrUsers);

$arrConvos = nmMemberConvo::getAllConvos();

?>

<h2><?php _e('User Conversations Statistics', nmMemberConvo::$short_name)?></h2>
<div style="margin:5px;padding:5px;border:1px solid #CCC; background-color:#f5f5f5">
<h3><?php _e('Total Conversations', nmMemberConvo::$short_name)?>: <?php echo count($arrConvos)?></h3>
<div class="user-uploaded-files">
<table width="100%" border="0" id="user-files" class="wp-list-table widefat fixed posts">
<thead>
	<tr>
        <th width="20%" align="left" valign="middle"><strong><?php _e('Subject', nmMemberConvo::$short_name)?></strong></th>
        <th width="15%" align="left" valign="middle"><strong><?php _e('Users', nmMemberConvo::$short_name)?></strong></th>
        <th width="50%" align="left" valign="middle"><strong><?php _e('Detail', nmMemberConvo::$short_name)?></strong></th>
        <th width="15%" align="left" valign="middle"><strong><?php _e('Date', nmMemberConvo::$short_name)?></strong></th>
      </tr>
</thead>


<tbody>
<?php foreach($arrConvos as $convo): ?>
  <tr>
    <td><?php echo $convo -> subject?></td>
    <td><strong>
	<?php echo $usersByIDs[$convo -> started_by]->display_name . ', ' . 
				$usersByIDs[$convo->started_with] -> display_name?></strong>
    </td>
    <td><a href="javascript:getPro()">View Detail</a></td>
    <td align="left"><?= date('d-M,y', strtotime($convo -> sent_on))?></td>
  </tr>
<?php endforeach;?>
  
</tbody>
</table>
</div>
<div style="clear:both"></div>
</div>

<script type="text/javascript">
function getPro()
{
	var a = confirm('It is Pro Feature, want more info?');
	if(a)
	{
		window.location = 'http://www.najeebmedia.com/nmedia-wordpress-conversation-plugin-pro/';
	}
}
</script>

