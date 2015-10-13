<?php
	include_once('modules/000_user_interface/user.php');
	$is_ie = ui_user_user_agent('is_ie');
	$is_mozilla = ui_user_user_agent('is_mozilla');
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
				case 'myavatar':
					$.post("<?php echo $this->basepath; ?>" + "ui/user/avatar_editor/ajax", {}, function(data){
						$("#detail_container").html(data);
					});
					break;
					
				case 'play':
					$.post("<?php echo $this->basepath; ?>" + "ui/user/play/ajax", {}, function(data){
						$("#detail_container").html(data);
					});
					break;
				
//				case 'myquest':
//					$.post("<?php echo $this->basepath; ?>" + "quest/user/default", {}, function(data){
//						$("#detail_container").html(data);
//					});
//					break;
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
				case 'myfriends':
					$.post("<?php echo $this->basepath; ?>" + "friend/user", {}, function(data){
						$("#detail_container").html(data);
					});
					break;
				
				case 'myanimation':
					$.post("<?php echo $this->basepath; ?>" + "avatar/user/animation", {}, function(data){
						$("#detail_container").html(data);
					});
					break;
				
//				case 'myaccountproperties':
//					$.post("<?php echo $this->basepath; ?>" + "user/user/properties", {}, function(data){
//						$("#detail_container").html(data);
//					});
//					break;
				
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
    <h3 class="ui-accordion-header"><a href="#">My Game</a></h3>
    <div class="ui-accordion-content transparent_70" style="text-align:center;">
      <a class="main_menu" id="myavatar" style="cursor:pointer;">
      	<img src="modules/000_user_interface/images/main_menu_icons/avatar.png" title="Customize your avatar...!" />
      </a>
      <a class="main_menu" id="play" style="cursor:pointer;">
      	<img src="modules/000_user_interface/images/main_menu_icons/game.png" title="Continue your game...!" />
      </a>
      <a class="main_menu" id="myquest" style="cursor:pointer;" href="<?php echo $this->basepath; ?>quest/user/default">
      	<img src="modules/000_user_interface/images/main_menu_icons/quest.png" title="View your Quest..." />
      </a>
      <a class="main_menu" id="myquiz" style="cursor:pointer;">
      	<img src="modules/000_user_interface/images/main_menu_icons/quiz.png" title="Manage your Quiz..." />
      </a>
      <a class="main_menu" id="mystatistics" style="cursor:pointer;">
      	<img src="modules/000_user_interface/images/main_menu_icons/statistics.png" title="View your statistics..." />
      </a>
    </div>
    <h3 class="ui-accordion-header"><a href="#">My Friends and Groups</a></h3>
    <div class="ui-accordion-content transparent_70">
      <a class="main_menu" id="myfriends" style="cursor:pointer;">
      	<img src="modules/000_user_interface/images/main_menu_icons/friend.png" title="Manage your friend list..." />
      </a>
    </div>
    <h3 class="ui-accordion-header"><a href="#">My Warehouse</a></h3>
    <div class="ui-accordion-content transparent_70">
      <a class="main_menu" id="myanimation" style="cursor:pointer;">
      	<img src="modules/000_user_interface/images/main_menu_icons/animation.png" title="Your animation..." />
      </a>
      <a class="main_menu" id="mywarehouse" style="cursor:pointer;">
      	<img src="modules/000_user_interface/images/main_menu_icons/warehouse.png" title="Manage items in your warehouse..." />
      </a>
    </div>
    <h3 class="ui-accordion-header"><a href="#">Messages</a></h3>
    <div class="ui-accordion-content transparent_70">
      <a class="main_menu" id="mymessages" style="cursor:pointer;">
      	<img src="modules/000_user_interface/images/main_menu_icons/message.png" title="Open your messages..." />
      </a>
      <a class="main_menu" id="mychat" style="cursor:pointer;">
      	<img src="modules/000_user_interface/images/main_menu_icons/chat.png" title="Manage your chat..." />
      </a>
    </div>
    <h3 class="ui-accordion-header"><a href="#">My Account</a></h3>
    <div class="ui-accordion-content transparent_70" style="min-height:160px;">
      <a class="main_menu" id="myaccountproperties" style="cursor:pointer;" href="<?php echo $this->basepath; ?>user/user/properties">
      	<img src="modules/000_user_interface/images/main_menu_icons/account.png" title="Your Account Settings..." />
      </a>
      <a class="main_menu" id="mypassword" style="cursor:pointer;">
      	<img src="modules/000_user_interface/images/main_menu_icons/password.png" title="Change Your Password..." />
      </a>
    </div>
  </div>
  
  <div id="detail_container" class="transbg" style="display:none;">
		&nbsp;
  </div>
  
  <!--[/login | sign-in form]-->
</div>

