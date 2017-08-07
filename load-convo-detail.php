<?php
/*
**This is load convo detail
*/

//print_r($_POST);

/* Loading wp core files */
$wp_path = $_POST['dirpath'];
require $wp_path . 'wp-load.php';
global $wpdb;

$nmConvo = new nmMemberConvo();
$convoDetail = $nmConvo -> getConvoDetail($_POST['cid']);
//print_r($convoDetail);

$arrConvo = json_decode($convoDetail -> convo_thread);
/*echo "<pre>";
print_r($arrConvo);
echo "</pre>";*/

$upload_dir = wp_upload_dir();
$path_folder = $upload_dir['baseurl'].'/user_uploads/';
?>

<ul class="nm-convo-detail">
<?php 
	foreach($arrConvo as $c):
	$title = $c -> username . __(' wrote on ').date('M-d,Y', $c->senton);
	
?>
    	<li class="convo-head"><?php echo stripslashes($title)?></li>
        <li class="convo-text"><?php echo stripslashes($c -> message)?></li>
        
        
        <?php
		if($c -> files)
		{
			$files = explode(',', $c -> files);
			echo '<li class="convo-attachment">
				<strong>['.count($files).'] Files Attachment:</strong><br />';
			foreach($files as $f):
				$file_path = $path_folder . $c -> username .'/'.$f;
				echo '<a href="'.$file_path.'" target="_blank">'.$f.'</a>';
				echo "<br />";
			endforeach;
			echo '</li>';
		}
		?>
        	
<?php 
	endforeach;
?>  
</ul>