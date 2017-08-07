<?php
/*
Default Template for Conversation
Author: Najeeb
Plugin: Nmedia Member Private Conversation Plugin
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Nmedia Conversation Plugin | Default Template</title>

<link rel="stylesheet" type="text/css" href="css/styles.css"/>


<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>

<!-- jquery ui -->
<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.16.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="js/jquery-ui-1.8.16.custom.min.js"></script>
<style>
/*.ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }*/
</style>

<!-- jquery ui -->

<script language="javascript">

scriptAr = new Array(); // initializing the javascript array
<?php
$arrUsers = array("name"	=> "najeeb",
					"name1"	=> "qsheeraz",
					"name2"	=> "naseer"
					);
					
					
//In the below lines we get the values of the php array one by one and update it in the script array.
foreach ($arrUsers as $k => $v)
{
echo "scriptAr.push(\"$v\" );"; // This line updates the script array with new entry
}
?>

</script>


<script type="text/javascript">
jQuery(function(){
	jQuery("#inbox, #compose").click(function(){
		/* hiding all */
		jQuery("#inbox-panel, #compose-panel").hide();
		
		/* what to show */
		jQuery("#"+this.id+"-panel").slideDown();
		
		/* JQ UI button */
		jQuery( "input:submit, button" ).button();
		//jQuery("a.orders" ).click(function() { return false; });

});
	
	
	
	/* auto complete using jq ui*/
		var availableTags = [
			"ActionScript",
			"AppleScript",
			"Asp",
			"BASIC",
			"C",
			"C++",
			"Clojure",
			"COBOL",
			"ColdFusion",
			"Erlang",
			"Fortran",
			"Groovy",
			"Haskell",
			"Java",
			"JavaScript",
			"Lisp",
			"Perl",
			"PHP",
			"Python",
			"Ruby",
			"Scala",
			"Scheme"
		];
		
		//alert(scriptAr);
		function split( val ) {
			return val.split( /,\s*/ );
		}
		function extractLast( term ) {
			return split( term ).pop();
		}

		jQuery( "#tags" )
			// don't navigate away from the field on tab when selecting an item
			.bind( "keydown", function( event ) {
				if ( event.keyCode === jQuery.ui.keyCode.TAB &&
						jQuery( this ).data( "autocomplete" ).menu.active ) {
					event.preventDefault();
				}
			})
			.autocomplete({
				minLength: 0,
				source: function( request, response ) {
					// delegate back to autocomplete, but extract the last term
					response( $.ui.autocomplete.filter(
						scriptAr, extractLast( request.term ) ) );
				},
				focus: function() {
					// prevent value inserted on focus
					return false;
				},
				select: function( event, ui ) {
					var terms = split( this.value );
					// remove the current input
					terms.pop();
					// add the selected item
					terms.push( ui.item.value );
					// add placeholder to get the comma-and-space at the end
					terms.push( "" );
					this.value = terms.join( ", " );
					return false;
				}
			});
		/* auto complete using jq ui*/
		
		
		
		jQuery("#dialog").dialog({ autoOpen: false, width:750, maxHeight:300, title:'Message Detail' });
});


/*
This is loading message history
*/
function loadConvoHistory(c_id)
{
	jQuery("#dialog").dialog('open');
}
</script>
</head>

<body>
<div id="dialog" title="Basic dialog" style="width:600px">
	<p>
    <ul class="nm-convo-detail">
    	<li class="convo-head">Alex wrote this on 5 Feb, 2012</li>
        <li class="convo-text">I am just fine but you didn't find me on style.</li>
        
        <li class="convo-head">Alex wrote this on 5 Feb, 2012</li>
        <li class="convo-text">I am just fine but you didn't find me on style.</li>
        
        <li class="convo-head">Alex wrote this on 5 Feb, 2012</li>
        <li class="convo-text">I am just fine but you didn't find me on style.</li>
        
        <li class="convo-head">Alex wrote this on 5 Feb, 2012</li>
        <li class="convo-text">I am just fine but you didn't find me on style.</li>
    </ul>
    
    <h3>Reply:</h3>
    <form action="" method="post">
    <textarea name="nm-reply" rows="9" cols="60"></textarea><br />
	<input type="submit" value="Send" />
    </form>
    </p>
</div>


<div id="convo-wrapper">
<h2>Nmedia Member Private Conversation Plugin</h2>
	<div id="left-area">
    	<ul>
        	<!--<li class="heading"><a href="#">Tools</a></li>-->
            
            <li id="inbox"><a href="#">Inbox (25)</a></li>
            <!--<li id="sent"><a href="#">Sent (11)</a></li>-->
            <li id="compose"><a href="#">Start Convo</a></li>
            
        </ul>
    </div>
    
  <div id="right-area">
  <div id="inbox-panel">
  <h2>Inbox</h2>
  <div id="tool-tray-inbox"></div>
    <!--<table cellpadding="0" class="F cf convo">
    <thead>
    <tr>
	    	<td class="chk"><input type="checkbox" /></td>
        	<td class="buddies"><input type="submit" value="Delete selected" /></td>
            <td align="right" class="message-hint"  colspan="2"><button>Order by Name</button> | <button>Order by Date</button>
            </td>
    </tr>
    </thead>
    <tbody>
    	<tr onclick="loadConvoHistory(1)">
        	<td class="chk"><input type="checkbox" /></td>
        	<td class="buddies">Najeeb, Kevin</td>
            <td class="message-hint">Website contact Message from Alex</td>
            <td class="message-time">2:56pm</td>
        </tr>
        <tr>
        	<td class="chk"><input type="checkbox" /></td>
        	<td class="buddies">Najeeb, Kevin</td>
            <td class="message-hint">Website contact Message from Alex</td>
            <td class="message-time">2:56pm</td>
        </tr>
        <tr>
        	<td class="chk"><input type="checkbox" /></td>
        	<td class="buddies">Najeeb, Kevin</td>
            <td class="message-hint">Website contact Message from Alex</td>
            <td class="message-time">2:56pm</td>
        </tr>
        <tr>
        	<td class="chk"><input type="checkbox" /></td>
        	<td class="buddies">Najeeb, Kevin</td>
            <td class="message-hint">Website contact Message from Alex</td>
            <td class="message-time">2:56pm</td>
        </tr>
        <tr>
        	<td class="chk"><input type="checkbox" /></td>
        	<td class="buddies">Najeeb, Kevin</td>
            <td class="message-hint">Website contact Message from Alex</td>
            <td class="message-time">2:56pm</td>
        </tr>
        <tr>
        	<td class="chk"><input type="checkbox" /></td>
        	<td class="buddies">Najeeb, Kevin</td>
            <td class="message-hint">Website contact Message from Alex</td>
            <td class="message-time">2:56pm</td>
        </tr>
       
    </tbody>
    </table>-->
    
    
    <ul id="nm-convo-container">
    	<li class="head">
        	<ul class="convo-row">
            	<li><input type="checkbox" /></li>
                <li class="buddies"><input type="button" value="Delete selected" /></li>
                <!--<li class="title">Title, with excerpt....</li>-->
                <li class="sorts"><button>Order by Name</button> | <button>Order by Date</button></li>
            </ul>
        </li>
        <li>
        	<ul class="convo-row">
            	<li class="check"><input type="checkbox" /></li>
                <li class="buddies">Two, Buddies</li>
                <li class="title">Title, with excerpt....</li>
                <li class="time">Feb-2, 2012</li>
            </ul>
        </li>
        
        <li>
        	<ul class="convo-row">
            	<li class="check"><input type="checkbox" /></li>
                <li class="buddies">Two, Buddies</li>
                <li class="title">Title, with excerpt....</li>
                <li class="time">Feb-2, 2012</li>
            </ul>
        </li>
        
        <li>
        	<ul class="convo-row">
            	<li class="check"><input type="checkbox" /></li>
                <li class="buddies">Two, Buddies</li>
                <li class="title">Title, with excerpt....</li>
                <li class="time">Feb-2, 2012</li>
            </ul>
        </li>
        
        <li>
        	<ul class="convo-row">
            	<li class="left-page">&laquo; Prev</li>
                <li class="page-count">2 of 11</li>
                <li class="right-page">Next &raquo;</li>
            </ul>
        </li>
    </ul>
    
    <!--<div class="nm-convo-pagi">
    
    </div>-->
    </div>	<!-- inobox-panel -->
    
    
    
    
    
  

  <!-- compose-panel -->  
  <div id="compose-panel">
  <h2>Write New Message</h2>
  	<form id="frm-new-convo" method="post">
    	<table width="100%" border="0">
          <tr>
            <td>Sent to:</td>
            <td>&nbsp;</td>
            <td><input type="text" name="sendto" id="tags" /></td>
          </tr>
          <tr>
            <td>Subject:</td>
            <td>&nbsp;</td>
            <td><input type="text" name="subject" /></td>
          </tr>
          <tr>
            <td>Message:</td>
            <td>&nbsp;</td>
            <td><textarea name="sendto" cols="45" rows="5"></textarea></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><input type="submit" value="Send" /></td>
          </tr>
        </table>

    </form>
  </div>		<!-- compose-panel -->
  </div>
    
<div class="fix_height"></div>
</div>
</body>
</html>
