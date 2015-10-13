<?php
if($this->logged_in){
?>
<script language="JavaScript">
$(document).ready(function(){
	$("#help_dialog").dialog({
		autoOpen: false, 
		minWidth: 540, 
		minHeight: 200
	});
	
	$("#wishlist_dialog").dialog({
		autoOpen: false, 
		minWidth: 540, 
		minHeight: 200
	});
	
	$('#right_bar_help').click(function(){
		$("#help_dialog").dialog('open');
	});
	
	$('#right_bar_wishlist').click(function(){
		$("#wishlist_dialog").dialog('open');
	});
	
	$('.float_div_right').live('click', function(){
//		$(this).animate({'left':'+=72'});
//		alert($(this).position().left);
	});
	

});
</script>

<div class="float_div_right shadow transparent_70"><br />
	<a title="Help" id="right_bar_help">
		<img src="<?php print($this->basepath); ?>modules/000_user_interface/images/icons/help.48x48.png" />
	</a>
  <br />
	<a title="Settings">
		<img src="<?php print($this->basepath); ?>modules/000_user_interface/images/icons/setting.48x48.png" />
	</a>
	<br />
	<a title="Your wishlists" id="right_bar_wishlist">
		<img src="<?php print($this->basepath); ?>modules/000_user_interface/images/icons/notes.48x48.png" />
	</a>
  <!-- Place this tag in your head or just before your close body tag -->
  <!--[*script type="text/javascript" src="https://apis.google.com/js/plusone.js"*]-->
  <!--script type="text/javascript" src="<?php print($this->basepath); ?>/libraries/js/plusone.js">
  //{lang: 'id'}
  </script-->
  
  <!-- Place this tag where you want the +1 button to render -->
  <!-- g:plusone size="tall"></g:plusone -->

</div>


<div style="width: auto; min-height: 200px; height: auto; min-width:540px; " class="ui-dialog-content ui-widget-content" id="help_dialog" 
	title="Help">
</div>

<div style="width: auto; min-height: 200px; height: auto; min-width:540px; " class="ui-dialog-content ui-widget-content" id="wishlist_dialog" 
	title="Your Wishlist">
</div>

<?php
}
?>
