<?php
	include_once('modules/000_user_interface/user.php');
//	$is_ie = ui_user_user_agent('is_ie');
//	$is_mozilla = ui_user_user_agent('is_mozilla');
?>

<link rel="stylesheet" type="text/css" href="<?php echo $this->basepath; ?>libraries/js/jquery.ui/css/custom-theme/jquery-ui-1.8.14.custom.css" />

<script src="<?php echo $this->basepath; ?>libraries/js/jquery.cycle.all.latest.js"></script>
<script src="<?php echo $this->basepath; ?>libraries/js/jquery_placeholder.js"></script>

<script language="javascript">

	$(document).ready(function() {
		$("#main-menu-accordion").accordion();
		$(".main_menu").click(function(){
			$("#main-menu-accordion").hide("fade", {}, 1);
			
			var element_id = $(this).attr('id');
			
			switch(element_id){	// myavatar, myquest, myquiz, mystatistics
				case 'user_management':
					$.post("<?php echo $this->basepath; ?>" + "user/admin/user_list", {}, function(data){
						$("#detail_container").html(data);
					});
					break;
				
				case 'current_player':
					window.location.replace("<?php echo $this->basepath; ?>user/admin/current_player");
					break;
				case 'level_data_upload':
					window.location.replace("<?php echo $this->basepath; ?>asset/admin/level");
					break;
				case 'myquiz':
					$.post("<?php echo $this->basepath; ?>" + "ui/user/quiz", {}, function(data){
						$("#detail_container").html(data);
					});
					break;
				case 'mystatistics':
					$.post("<?php echo $this->basepath; ?>" + "ui/user/statistics", {}, function(data){
						$("#detail_container").html(data);
					});
					break;
				
				case 'npc':
					$.post("<?php echo $this->basepath; ?>" + "asset/admin/npc", {}, function(data){
						$("#detail_container").html(data);
					});
					break;
				
				case 'dialog':
					$.post("<?php echo $this->basepath; ?>" + "quest/admin/dialog", {}, function(data){
						$("#detail_container").html(data);
					});
					break;
				
				case 'quest':
					$.post("<?php echo $this->basepath; ?>" + "quest/admin/default", {}, function(data){
						$("#detail_container").html(data);
					});
					break;
				
				case 'quest_journal':
					window.location.replace("<?php echo $this->basepath; ?>quest/admin/questjournal");
					break;
				
				case 'avatar':
					window.location.replace("<?php echo $this->basepath; ?>asset/admin/avatar");
					break;
				
				case 'inventory':
					window.location.replace("<?php echo $this->basepath; ?>asset/admin/inventory");
					break;
				
				case 'inventoryicons':
					window.location.replace("<?php echo $this->basepath; ?>asset/admin/inventoryicons");
					break;
				
				
				
				case 'animation':
					window.location.replace("<?php echo $this->basepath; ?>asset/admin/animation");
					break;
				
				
			}
			
			$("#detail_container").show("fade", {}, 1000);
			

		});

		$("#top_menu_dashboard").click(function(){
			$("#detail_container").hide("fade", {}, 1);
			$("#main-menu-accordion").show("fade", {}, 1000);
		});

		$('#dropplayerinventory').live('click', function(){
			if(!confirm('Hapus semua data di table Game.PlayerInventory?')){
				return;
			}
			$.post("<?php echo $this->basepath; ?>" + "quest/admin/dropplayerinventory", {}, function(data){
				if($.trim(data) == 'OK'){
					alert('Data PlayerInventory berhasil dihapus');
				}
			});
		});
		

	});
		
</script>

<div class="centered transbg" style="width:960px; height:540px; border:none;">
	<div style="float:left; width:960px; height:30px;">

  </div>

	<!--[slideshow]-->
	&nbsp;
	<!--[/slideshow]-->
  
  <!--[login | sign-in form]-->
  <div id="main-menu-accordion" class="ui-accordion transbg">
    <h3 class="ui-accordion-header"><a href="#">Asset Management</a></h3>
    <div class="ui-accordion-content transparent_70" style="min-height: 130px;">
      <a class="main_menu" id="level_data_upload" style="cursor:pointer;">
      	<img src="<?php echo $this->basepath; ?>modules/000_user_interface/images/admin.level.png" />
      </a>
      <a class="main_menu" id="npc" style="cursor:pointer;">
      	<img src="<?php echo $this->basepath; ?>modules/000_user_interface/images/admin.npc.png" />
      </a>
      <a class="main_menu" id="avatar" style="cursor:pointer;">
      	<img src="<?php echo $this->basepath; ?>modules/000_user_interface/images/admin.avatar.png" />
      </a>
      <a class="main_menu" id="inventory" style="cursor:pointer;">
      	<img src="<?php echo $this->basepath; ?>modules/000_user_interface/images/admin.inventory.png" />
      </a>
      <a class="main_menu" id="animation" style="cursor:pointer;">
      	<img src="<?php echo $this->basepath; ?>modules/000_user_interface/images/admin.animation.png" />
      </a>
      <a class="bukan_main_menu" id="dropplayerinventory" style="cursor:pointer;">
      	<img src="<?php echo $this->basepath; ?>modules/000_user_interface/images/admin.inventory.drop.png" />
      </a>
			<br />
      <a class="main_menu" id="inventoryicons" style="cursor:pointer;">
      	<img src="<?php echo $this->basepath; ?>modules/000_user_interface/images/admin.inventory.icons.png" />
      </a>
      <a class="main_menu" style="cursor:pointer;" href="<?php echo $this->basepath; ?>asset/admin/brand">
          <img src="<?php echo $this->basepath; ?>modules/000_user_interface/images/brand-icon.png" width="120" />
      </a>
      <a class="main_menu" style="cursor:pointer;" href="<?php echo $this->basepath; ?>asset/admin/category">
          <img src="<?php echo $this->basepath; ?>modules/000_user_interface/images/gallery-icon.png" width="120" />
      </a>
    </div>

    <h3 class="ui-accordion-header"><a href="#">User Management</a></h3>
    <div class="ui-accordion-content transparent_70" style="text-align:center;">
      <a class="main_menu" id="user_management" style="cursor:pointer;">
      	<img src="<?php echo $this->basepath; ?>modules/000_user_interface/images/admin.user.management.png" />
      </a>
      <a class="main_menu" id="current_player" style="cursor:pointer;">
      	<img src="<?php echo $this->basepath; ?>modules/000_user_interface/images/admin.current.player.png" />
      </a>
			
			
      <a href="<?php echo $this->basepath; ?>user/admin/sessionimport" target="_blank"  id="session_log" style="cursor:pointer;">
      	<img src="<?php echo $this->basepath; ?>modules/000_user_interface/images/session.log.png" />
      </a>
			
      <a href="<?php echo $this->basepath; ?>user/admin/accountimport" target="_blank"  id="account_import" style="cursor:pointer;">
      	<img src="<?php echo $this->basepath; ?>modules/000_user_interface/images/account.import.png" />
      </a>
			
      <a href="<?php echo $this->basepath; ?>user/admin/roomplayerstat" id="room_player_stat" style="cursor:pointer;">
      	<img src="<?php echo $this->basepath; ?>modules/000_user_interface/images/room.player.stats.png" />
      </a>
			
    </div>
    
    <h3 class="ui-accordion-header"><a href="#">Dialog &amp; Quest</a></h3>
    <div class="ui-accordion-content transparent_70">
      <a class="main_menu" id="dialog" style="cursor:pointer;">
      	Dialog
      </a>
      <a class="main_menu" id="quest" style="cursor:pointer;">
      	Quest
      </a>
      <a class="main_menu" id="quest_journal" style="cursor:pointer;">
      	Report
      </a>
    </div>
    
    
    <h3 class="ui-accordion-header"><a href="#">Article &amp; Slideshow</a></h3>
    <div class="ui-accordion-content transparent_70">
      <a class="main_menu" style="text-decoration:none;" href="<?php echo $this->basepath; ?>article/admin" style="cursor:pointer;">
      	Article &amp; Slideshow
      </a>
    </div>
		
    <h3 class="ui-accordion-header"><a href="#">Settings</a></h3>
    <div class="ui-accordion-content transparent_70">
      <a id="lobby_setting" style="cursor:pointer;">
      	Lobby Setting
      </a>
    </div>
  </div>
  
  <div id="detail_container" class="transbg" style="display:none;">
		&nbsp;
  </div>
  
  <!--[/login | sign-in form]-->
</div>



<script type="text/javascript">
$(document).ready(function(){
	$("#set_lobby_dialog").dialog({
		autoOpen: false, 
		minWidth: 480, 
		minHeight: 160,
		modal: true,
		buttons: [
			{
				text: "Save",
				click: function() {
					var ip = $('#lobby_ip').val();
					var port = $('#lobby_port').val();
					var room_history = $('#lobby_room_history').val();
					// asset_admin_lobbysetting($op = null /* get | set */, $ip = null, $port = null, $ret_type = null /* array | json */)
					$.post("<?php echo $this->basepath; ?>asset/admin/lobbysetting/set/" + ip + "/" + port + "/0/" + room_history, {}, function(data){
						if(data == 'OK'){
							alert("Lobby updated.");
							$("#set_lobby_dialog").dialog("close");
						} else {
							alert("Data update failed. Data: " + data);
						}
					});
				}
			},
			
			{
				text: "Close",
				click: function() { $(this).dialog("close"); }
			},
			
		]
	});
	
	$('#lobby_setting').live('click', function(){
		$("#set_lobby_dialog").dialog('open');
		
		// asset_admin_lobbysetting($op = null /* get | set */, $ip = null, $port = null, $ret_type = null /* array | json */)
		$.post("<?php echo $this->basepath; ?>asset/admin/lobbysetting/get/0/0/json", {}, function(data){
			var data_ = eval('(' + data + ')');
			$('#lobby_ip').val(data_['ip']);
			$('#lobby_port').val(data_['port']);
			$('#lobby_room_history').val(data_['lobby_room_history']);
		});
	});
	
});
</script>

<div style="width: auto; min-height: 58.4px; max-height: 540px; min-width:320px; overflow-y:auto; " class="ui-dialog-content ui-widget-content" id="set_lobby_dialog" title="Set Lobby">
	<table style="width: 90%; border: 0; text-align: left;">
		<tr>
			<th>IP</th>
			<td><input type="text" size="20" name="lobbby_ip" id="lobby_ip" /></td>
		</tr>
		<tr>
			<th>Port</th>
			<td><input type="text" size="20" name="lobbby_port" id="lobby_port" /></td>
		</tr>
		<tr>
			<th>Enable Room History</th>
			<td>
				<select name="lobby_room_history" id="lobby_room_history">
					<option value="0">Disable</option>
					<option value="1">Enable</option>
				</select>
			</td>
		</tr>
	</table>
</div>
