<?php

global $wpdb;


/*
delete file
*/
if(isset($_GET['fid']))
{
	if(nmFileUploader::deleteFile($_GET['fid']))
	{
		echo "<div class=\"red\">". get_option('nm_file_deleted_msg') ."</div>";
	}
	
}

/*$wpdb->show_errors();
$wpdb->print_error(); */


$arrFiles = nmFileUploader::getUserFiles();
?>
<div style="margin:5px;padding:5px;border:1px solid #CCC; background-color:#f5f5f5">

<div class="user-uploaded-files">
<h2>Uploaded Files</h2>
<table width="100%" border="0" id="user-files" class="wp-list-table widefat fixed posts">
<thead>
	<tr>
        <th width="233" align="left" valign="middle"><strong>File Name</strong></th>
        <th width="516" align="left" valign="middle"><strong>Notes</strong></th>
        <th width="71" align="center" valign="middle"><strong>Date</strong></th>
        <th width="97" align="center" valign="middle"><strong>Action</strong></th>
      </tr>
</thead>


<tbody>
<?php foreach($arrFiles as $file):
//print_r(parse_url($_SERVER['HTTP_REFERER']));
$urlQuertyString = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);

if($urlQuertyString == '')
	$urlDelete = str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'?fid='.$file -> fileID;
else
	$urlDelete = str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'&fid='.$file -> fileID;


$uploads = wp_upload_dir();
$urlDwnld = $uploads['baseurl'] . '/user_uploads/' . $file -> fileName;
?>
  <tr>
    <td><?= $file -> fileName?></td>
    <td><?= $file -> fileDescription?></td>
    <td width="71" align="center"><?= date('d-M,y', strtotime($file -> fileUploadedOn))?></td>
    <td width="97" align="center">
    <a href="<?= $urlDwnld?>">
	<?php echo "<img border=\"0\" src=".plugins_url( 'images/down_16.png' , __FILE__)." />";	?>
    </a>
     | 
    <a href="javascript:confirmFirst('<?= $urlDelete?>')">
	<?php echo "<img border=\"0\" src=".plugins_url( 'images/delete_16.png' , __FILE__)." />";	?>
    </a></td>
  </tr>
<?php endforeach;?>
  
</tbody>
</table>
</div>
<div style="clear:both"></div>
</div>


<script type="text/javascript">
	function confirmFirst(url)
	{
		var a = confirm('Are you sure to delete this file?');
		if(a)
		{
			window.location = url;
		}
	}

</script>