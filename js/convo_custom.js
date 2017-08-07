// JavaScript Document

String.prototype.trim = function() {
    return this.replace(/^\s+|\s+$/g,"");
}



jQuery(function(){
	
	jQuery(".green, .red").fadeOut(7000);
	jQuery("table#user-file tr:odd").css("background-color", "#f1f1f1");	
});



/*
uploadify
*/
function callUploadify(pluginURL, uploadPath)
{
	jQuery('#file_upload').uploadify({
    'uploader'  : pluginURL + '/js/uploadify/uploadify.swf',
    'script'    : pluginURL + '/js/uploadify/uploadify.php',
    'cancelImg' : pluginURL + '/js/uploadify/cancel.png',
    'folder'    : uploadPath,
    'auto'      : true,
	'onComplete'  : function(event, ID, fileObj, response, data) {
      //alert('There are ' + fileObj.name);
	  jQuery("#file-name").attr("value", fileObj.name);
	  jQuery("#upload-response").html(fileObj.name + ' loaded, Please now click \'Upload\' to upload the file').fadeIn(200);
    }
  });
}

/*
validate me
*/

function validate()
{
	jQuery("#working-area").show();
	
	var upload_file_name	= jQuery("#nm-upload-name").val();
	var file_name			= jQuery("#file-name").val();
	var notes				= jQuery("#nm-notes").val();
	
	var notices 			= jQuery("#notices");
	notices.html('');
	
	var vFlag				= false;
	 
	if(upload_file_name == '')
	{
		notices.append('File Name cannot be empty<br>');
		vFlag = true;
	}
	
	if(file_name == '')
	{
		notices.append('Select any file first<br>');
		vFlag = true;
	}
	
	if(notes == '')
	{
		notices.append('Files Notes cannot be empty<br>');
		vFlag = true;
	}
	
	
	if(vFlag)
	{
		jQuery("#working-area").hide();
		return false;
		
	}
	else
	{
		return true;
	}
}
		
function confirmFirst(url)
{
	var a = confirm('Are you sure to delete this file?');
	if(a)
	{
		window.location = url;
	}
}


function showDetail(id)
{
	jQuery(".detail-all").hide();
	jQuery("#detail-all-"+id).fadeIn(1000);
	
}



