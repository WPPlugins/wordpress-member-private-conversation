// JavaScript Document
var current_page = 1;
var total_pages;
var plugin_path;

/* pagination */
function loadConvoPageNext()
{
	jQuery("#nm-convo-container ul li.nm-c-p-"+current_page++).hide();
	jQuery("#nm-convo-container ul li.nm-c-p-"+current_page).show();
	setPagination();
}

function loadConvoPagePrev()
{
	jQuery("#nm-convo-container ul li.nm-c-p-"+current_page--).hide();
	jQuery("#nm-convo-container ul li.nm-c-p-"+current_page).show();
	setPagination();
}


function loadConvoCurrentPage()
{
	
	//showing inbox panel
  	jQuery("#inbox-panel").show();
	
	//hiding history/detail panel me
	jQuery("#convo-history-panel").hide();
	
	//loading current page
	
	jQuery("#nm-convo-container ul li.nm-c-p-"+current_page).show();
	setPagination();
	
	
}

function setPagination()
{
	if(total_pages == 1)
	{
		jQuery("#prev-page a").hide();
		jQuery("#next-page a").hide();
	}
	else if(total_pages == current_page)
	{
		jQuery("#next-page a").hide();
		jQuery("#prev-page a").show();
	}
	else if(total_pages > current_page && current_page > 1)
	{
		jQuery("#prev-page a").show();
	}
	else
	{
		jQuery("#prev-page a").hide();
		jQuery("#next-page a").show();
	}	
	
	//setting page couner lable
	jQuery("#page-count").html(current_page+" of "+total_pages);
	
	// uncheck top delete 
	jQuery("#convo-select-all").attr('checked', false);
}


/* new message validation */
function validateCompose(){
		error=0;
		if(jQuery("#tags").val()==""){
			 jQuery("#tags").css("border-color", "red");
			jQuery("#start_with_err").show().fadeOut(10000);
			error=1;
		} 

		if(jQuery("#subject").val()==""){
			jQuery("#subject").css("border-color", "red");
			jQuery("#subject_err").show().fadeOut(11000);
			error=1;
		} 
		
		if(jQuery("#message").val()==""){
			jQuery("#message").css("border-color", "red");
			jQuery("#message_err").show().fadeOut(12000);
			error=1;
		} 
		
		if(error==1) return false; else return true;
		
}


/* reply validation */
function validateReply(){
		error=0;
		
		if(jQuery("#nm-reply").val()==""){
			jQuery("#nm-reply").css("border-color", "red");
			jQuery("#reply_err").show().fadeOut(12000);
			error=1;
		} 
		
		if(error==1) return false; else return true;
		
}



/*
uploadify for reply convo
*/
function callUploadify_reply(pluginURL, userName, uploadMessage, buttonText, multiAllow, fileLimit, fileExt, sizeLimit)
{
	
	var user_name = "/"+userName;
	
	if(multiAllow == 'true')
		multi = true;
	else
		multi = false;
		
	jQuery('#file_upload_reply').uploadify({
    'uploader'  : pluginURL + '/js/uploadify/uploadify.allglyphs.swf',
    'script'    : pluginURL + '/doupload.php',
    'cancelImg' : pluginURL + '/js/uploadify/cancel.png',
    'folder'	: user_name,
	'buttonText': buttonText,
	'multi'		: multi,
	'queueSizeLimit' : fileLimit,
	'fileExt'	: fileExt,
	'fileDesc'    : 'Select Files',
    'auto'      : true,
	'sizeLimit'  : sizeLimit,
	'onComplete'  : function(event, ID, fileObj, response, data) {
      //alert('There are ' + fileObj.name);
	  oldVal = jQuery("#file-name-reply").attr("value");
	  if(oldVal != "") oldVal += ",";
	  newVal = oldVal+fileObj.name;
	  jQuery("#file-name-reply").attr("value", newVal);
	  jQuery("#upload-response-reply").append(fileObj.name + uploadMessage + "<br />").fadeIn(200);
    }
  });
}
