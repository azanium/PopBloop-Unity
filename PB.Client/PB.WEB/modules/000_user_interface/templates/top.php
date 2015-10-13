<?php
if($this->is_admin){
?>
<script language="javascript">
$(document).ready(function(){
	
	$('#dialog_link, ul#icons li').hover(
		function() { $(this).addClass('ui-state-hover'); }, 
		function() { $(this).removeClass('ui-state-hover'); }
	);
	
	$("#top_menu_home").click(function(){
		window.location.replace("<?php echo $this->basepath; ?>ui/admin");
	});
	
	$("#top_menu_logout").click(function(){
		if(confirm('Are you sure want to logout?')){
			$.post("<?php echo $this->basepath; ?>user/user/logout", {}, function(data){
				if($.trim(data) == '1'){
					window.location.replace("<?php echo $this->basepath; ?>");
				} else {
					alert('Well Dude, sometimes it happens.');
				}
			});
		}
	});
	
});
</script>

<div class="float_div_top shadow">
	<div style="height:40px; top:0; position:relative; float:left; text-align:left; width:70%;">
    <ul id="icons" class="ui-widget ui-helper-clearfix">
      <li class="ui-state-default ui-corner-all" title="Home" id="top_menu_home"><span class="ui-icon ui-icon-home"></span></li>
      <li class="ui-state-default ui-corner-all" title="Logout" id="top_menu_logout"><span class="ui-icon ui-icon-power"></span></li>
    </ul>
	</div>
	<div style="height:40px; top:0; position:relative; float:left; text-align:left; width:30%; color:#0CF; overflow-x:hidden; overflow-y:hidden;">
		
		<div style="float:left;">
			<img id="top_profile_picture" src="<?php echo $this->basepath; ?>user_generated_data/profile_picture/<?php echo $this->user_property->profile_picture; ?>" />
		</div>
		
		<div style="margin:12px 10px 8px 10px; font-size:14px; float:left;"><strong><?php echo $this->user_property->fullname; ?></strong></div>

		<div style="float:left;">
			<?php
			if(intval($this->user_property->count_unread_msg) == 0){
			?>
			<img class="top_notification" id="top_notification_msg" src="<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/_icon.msg.40x20.png" />
			<?php
			} else {
			?>
			<img class="top_notification" id="top_notification_msg" src="<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/_icon.msg.40x20.notif.png" />
			<?php
			}
			?>
		</div>
		<div style="margin:13px 10px 9px 2px; font-size:12px; float:left;">(<?php echo intval($this->user_property->count_unread_msg); ?>)</div>

		<div style="float:left;">
			<img class="top_notification" id="top_notification_power" src="<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/_icon.power.40x20.png" />
		</div>
		<div style="margin:13px 10px 9px 2px; font-size:12px; float:left;">(<?php echo intval($this->user_property->power); ?>)</div>


	</div>
<!--[
	<div style="height:22px; top:0; position:relative; float:left;">
    <ul id="navlist">
      <li id="home"><a id="top_menu_dashboard" style="cursor:pointer">Dashboard</a></li>
      <li id="prev"><a id="top_menu_previous" style="cursor:pointer"></a></li>
      <li id="next"><a id="top_menu_next" style="cursor:pointer"></a></li>
      <li id="logout"><a id="top_menu_logout" style="cursor:pointer">Logout</a></li>
    </ul>
  </div>
]-->
</div>

<?php
} else if($this->logged_in){
?>

<script language="javascript">
$(document).ready(function(){

	$('#dialog_link, ul#icons li').hover(
		function() { $(this).addClass('ui-state-hover'); }, 
		function() { $(this).removeClass('ui-state-hover'); }
	);
	
	$("#top_menu_home").click(function(){
		window.location.replace("<?php echo $this->basepath; ?>");
	});

	$("#top_menu_account").click(function(){
//		window.location.replace("<?php echo $this->basepath; ?>");
		alert('Tampilkan halaman edit account...');
	});

	$("#top_menu_logout").click(function(){
		if(confirm('Are you sure want to logout?')){
			$.post("<?php echo $this->basepath; ?>user/user/logout", {}, function(data){
				if($.trim(data) == '1'){
					window.location.replace("<?php echo $this->basepath; ?>");
				} else {
					alert('Well Dude, sometimes it happens.');
				}
			});
		}
	});
	
	$("#msg_dialog").dialog({
		autoOpen: false, 
		minWidth: 600, 
		minHeight: 320,
	});
	
	$('#top_notification_msg').live('click', function(){
		$("#msg_dialog").dialog('open');
	});
	
});
</script>

<div class="float_div_top shadow">
	<div style="height:40px; top:0; position:relative; float:left; text-align:left; width:70%;">
    <ul id="icons" class="ui-widget ui-helper-clearfix">
      <li class="ui-state-default ui-corner-all" title="Home" id="top_menu_home"><span class="ui-icon ui-icon-home"></span></li>
      <li class="ui-state-default ui-corner-all" title="Logout" id="top_menu_logout"><span class="ui-icon ui-icon-power"></span></li>
    </ul>
	</div>
	<div style="height:40px; top:0; position:relative; float:left; text-align:left; width:30%; color:#0CF; overflow-x:hidden; overflow-y:hidden;">
		
		<div style="float:left;">
			<img id="top_profile_picture" src="<?php echo $this->basepath; ?>user_generated_data/profile_picture/<?php echo $this->user_property->profile_picture; ?>" />
		</div>
		
		<div style="margin:12px 10px 8px 10px; font-size:14px; float:left;"><strong><?php echo $this->user_property->fullname; ?></strong></div>

		<div style="float:left;">
			<?php
			if(intval($this->user_property->count_unread_msg) == 0){
			?>
			<img class="top_notification" id="top_notification_msg" src="<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/_icon.msg.40x20.png" />
			<?php
			} else {
			?>
			<img class="top_notification" id="top_notification_msg" src="<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/_icon.msg.40x20.notif.png" />
			<?php
			}
			?>
		</div>
		<div style="margin:13px 10px 9px 2px; font-size:12px; float:left;">(<?php echo intval($this->user_property->count_unread_msg); ?>)</div>

		<div style="float:left;">
			<img class="top_notification" id="top_notification_power" src="<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/_icon.power.40x20.png" />
		</div>
		<div style="margin:13px 10px 9px 2px; font-size:12px; float:left;">(<?php echo intval($this->user_property->power); ?>)</div>


	</div>
<!--[
	<div style="height:22px; top:0; position:relative; float:left;">
    <ul id="navlist">
      <li id="home"><a id="top_menu_dashboard" style="cursor:pointer">Dashboard</a></li>
      <li id="prev"><a id="top_menu_previous" style="cursor:pointer"></a></li>
      <li id="next"><a id="top_menu_next" style="cursor:pointer"></a></li>
      <li id="logout"><a id="top_menu_logout" style="cursor:pointer">Logout</a></li>
    </ul>
  </div>
]-->
</div>


<div style="width: auto; min-height: 320px; height: auto; min-width:600px;" class="ui-dialog-content ui-widget-content transparent_70" id="msg_dialog" 
	title="Messages">

</div>

<?php
}
?>
