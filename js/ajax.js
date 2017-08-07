var scriptAr = new Array(); // initializing the javascript array

jQuery(function() {
	// jQuery("a" ).click(function() { alert('hi'); return false; });

	/* JQ UI button */
	// jQuery( "input:submit, button" ).button();

	// hiding all convo but only page-1
	jQuery("#nm-convo-container ul li.nm-c-p-1").show();

	jQuery("#inbox, #compose").click(function() {

		/* hiding panel */
		jQuery("#inbox-panel, #compose-panel, #convo-history-panel").hide();

		/* what to show */
		jQuery("#" + this.id + "-panel").slideDown();

	});

	// select all
	jQuery("#convo-select-all").click(
			function() {
				// alert(current_page);
				jQuery("ul.nm-c-p-" + current_page).find(':checkbox').attr(
						'checked', this.checked);

			});

	/* auto complate */
	jQuery("#tags").autocomplete({
		source : scriptAr
	});

	/* auto complete using jq ui */

	/*
	 * uploadify version 3.1
	 */

	/*
	 * jQuery('#file_upload').uploadify({ 'swf' : convo_vars.convo_plugin_url +
	 * 'js/uploadify-v3.1/uploadify.swf', 'uploader' :
	 * convo_vars.convo_plugin_url + 'js/uploadify-v3.1/uploadify.php' // Put
	 * your options here });
	 */

	/*
	 * uploadify version 2.1.4
	 */

	// alert(convo_vars.convo_token);
	jQuery('#file_upload').uploadify(
			{
				'uploader' : convo_vars.convo_plugin_url
						+ 'js/uploadify/uploadify.allglyphs.swf',
				'script' : convo_vars.ajaxurl,
				'scriptData' : {
					'action' : 'convo_file',
					'username' : convo_vars.current_user
				},
				'cancelImg' : convo_vars.convo_plugin_url
						+ 'js/uploadify/cancel.png',
				'auto' : true,
				'onComplete' : function(event, ID, fileObj, response, data) {
					oldVal = jQuery("#file-name").attr("value");
					if (oldVal != "")
						oldVal += ",";
					newVal = oldVal + fileObj.name;
					jQuery("#file-name").attr("value", newVal);
					jQuery("#upload-response").append(
							fileObj.name + convo_vars.file_attached_message + "<br />")
							.fadeIn(200);
				}
			// Put your options here
			});

});

/*
 * This is loading message history
 */
function loadConvoHistory(c_id) {
	// alert(url_convo_detail);
	// hiding other stuff
	jQuery("#inbox-panel, #compose-panel").hide();

	// but showing me
	jQuery("#convo-history-panel").show();

	// setting title of pop up
	var t = jQuery("#convo-" + c_id).find("li.title").html();

	// binding convo id value to reply form hidden id field
	jQuery("#reply-c-id").val(c_id);

	jQuery("#convo-detail-container").load(url_convo_detail, {
		dirpath : dir_path,
		cid : c_id
	});
	jQuery("#history-heading").html(t);

	// mark as read
	markAsRead(c_id);
}

/* mark convo as read */
function markAsRead(cid) {
	var data = {
		action : 'convo_action',
		convo_token : convo_vars.convo_token,
		convo_id : cid
	};

	// since 2.8 ajaxurl is always defined in the admin header and points to
	// admin-ajax.php
	jQuery.post(convo_vars.ajaxurl, data, function(response) {
		// alert('Got this from the server: ' + response);
	});

}