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
				
				case 'avatar':
					window.location.replace("<?php echo $this->basepath; ?>asset/admin/avatar");
					break;
				
				case 'inventory':
					window.location.replace("<?php echo $this->basepath; ?>asset/admin/inventory");
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
    <div class="ui-accordion-content transparent_70">
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
    </div>

    <h3 class="ui-accordion-header"><a href="#">User Management</a></h3>
    <div class="ui-accordion-content transparent_70" style="text-align:center;">
      <a class="main_menu" id="user_management" style="cursor:pointer;">
      	<img src="<?php echo $this->basepath; ?>modules/000_user_interface/images/admin.user.management.png" />
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
    </div>
    
    
    <h3 class="ui-accordion-header"><a href="#">Statistics</a></h3>
    <div class="ui-accordion-content transparent_70">
      <p>
			...
      </p>
    </div>
    <h3 class="ui-accordion-header"><a href="#">Settings</a></h3>
    <div class="ui-accordion-content transparent_70">
      <p>
			...
      </p>
    </div>
    <h3 class="ui-accordion-header"><a href="#">My Account</a></h3>
    <div class="ui-accordion-content transparent_70">
      <p>
			Change Password
      </p>
      <p>
			Change Password
      </p>
      <p>
			Change Password
      </p>
      <p>
			Change Password
      </p>
    </div>
  </div>
  
  <div id="detail_container" class="transbg" style="display:none;">
		&nbsp;
  </div>
  
  <!--[/login | sign-in form]-->
</div>

