<!--[<?php echo 'q: ' .  $_REQUEST['q']; ?>]-->

<?php
	include_once('modules/000_user_interface/guest.php');
	include_once('modules/000_user_interface/user.php');
	
	// recaptcha
	require_once('libraries/recaptcha/recaptchalib.php');
	$publickey = '6Lc4rc0SAAAAABnStfbcMto4QuRhJzMPU4Hq5UfV';
	
	// count page views
	include_once('modules/009_log/guest.php');
?>

<noscript>
	<style>
  	.withjs {display:none;}
  </style>
  <font face="Palatino Linotype, Book Antiqua, Palatino, serif" size="+2">
		Enable JavaScript in your browser to access this site properly!
  </font>
</noscript>

<div class="withjs">

<script type="text/javascript" language="javascript" src="<?php print($this->basepath); ?>libraries/js/SimplejQueryDropdowns/js/hoverIntent.js"></script>
<script type="text/javascript" language="javascript" src="<?php print($this->basepath); ?>libraries/js/SimplejQueryDropdowns/js/jquery.dropdown.js"></script>

<link rel="stylesheet" type="text/css" media="screen, projection" href="<?php print($this->basepath); ?>libraries/js/SimplejQueryDropdowns/css/style.css"/>
<!--[if lte IE 7]>
  <link rel="stylesheet" type="text/css" href="<?php print($this->basepath); ?>libraries/js/SimplejQueryDropdowns/css/ie.css" media="screen" />
<![endif]-->


<!--script language="javascript" src="<?php print($this->basepath); ?>libraries/js/jquery.ui/js/jquery-ui-1.8.14.custom.min.js"></script-->
<script language="javascript" src="<?php print($this->basepath); ?>libraries/js/jquery.ui.popbloop.dark/js/jquery-ui-1.8.17.custom.min.js"></script>

<!--link rel="stylesheet" type="text/css" href="<?php print($this->basepath); ?>libraries/js/jquery.ui/css/custom-theme/jquery-ui-1.8.14.custom.css" /-->
<!--link rel="stylesheet" type="text/css" href="<?php print($this->basepath); ?>libraries/js/jquery.ui/css/custom-theme/jquery-ui-1.8.14.custom.dark.css" /-->
<link rel="stylesheet" type="text/css" href="<?php print($this->basepath); ?>libraries/js/jquery.ui.popbloop.dark/css/custom-theme/jquery-ui-1.8.17.custom.css" />

<link rel="stylesheet" type="text/css" media="screen" href="<?php print($this->basepath); ?>modules/000_user_interface/css/default_popbloopdark.css" />


<?php /* ?>
<!--[ Include the jScrollPane jQuery script & CSS. ]-->

<!-- styles needed by jScrollPane - include in your own sites -->
<link type="text/css" href="<?php print($this->basepath); ?>libraries/js/jScrollPane/style/jquery.jscrollpane.css" rel="stylesheet" media="all" />

<!-- the mousewheel plugin -->
<script type="text/javascript" src="<?php print($this->basepath); ?>libraries/js/jScrollPane/script/jquery.mousewheel.js"></script>
<!-- the jScrollPane script -->
<script type="text/javascript" src="<?php print($this->basepath); ?>libraries/js/jScrollPane/script/jquery.jscrollpane.min.js"></script>
<?php */ ?>


<script language="javascript">
	$(document).ready(function(){

//		$('.feature_box').live('click', function(){
//			var _id = $(this).attr('id');
//			var _id_split = _id.split('_');
//			switch(_id_split[1]){
//				case '1':
//					$('.pop_webplayer').html('Join yuk...');
//				break;
//			}
//		});

		$('.pop_status_msg, .pop_error_msg').live('click', function(){
			$(this).hide();
		});
		

	<?php
  	if(!isset($_SESSION['user_id'])){
	?>

		// testing mulai
		$('#new_username, #new_fullname').keydown(function(event){
			// Allow: backspace, delete, tab and escape 
			if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 ||  event.keyCode == 32 ||  
				 // Allow: Ctrl+A 
				(event.keyCode == 65 && event.ctrlKey === true) ||  
				 // Allow: home, end, left, right 
				(event.keyCode >= 35 && event.keyCode <= 39) || (48 <= event.keyCode && event.keyCode <= 57) || (65 <= event.keyCode && event.keyCode <= 90)) { 
					 // let it happen, don't do anything 
					 return; 
			} 
			else { 
				// Ensure that it is a number and stop the keypress 
				if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) { 
					event.preventDefault();  
				}    
			} 
		});
		// testing selesai


		$('#login_box_top_sign_in').live('click', function(){
			var visible = $('#login_box_bottom_sign_in').is(":visible");
			$('#login_box_bottom_sign_up').hide();
			$('#login_box_bottom_sign_in').slideToggle();
			
			if(visible){
				$('#sign_in_arrow').css("background-image","url(<?php print($this->basepath); ?>tide/images/page-images/loginbar/toggle_down_light.png)");
				$('#login_box_top_sign_in').css("background-color","");
			} else {
				$('#sign_in_arrow').css("background-image","url(<?php print($this->basepath); ?>tide/images/page-images/loginbar/toggle_up_light.png)");
				$('#login_box_top_sign_in').css("background-color","#00CCFF");
			}
			$('#sign_up_arrow').css("background-image","url(<?php print($this->basepath); ?>tide/images/page-images/loginbar/toggle_down_light.png)");
			$('#login_box_top_sign_up').css("background-color","");
		});

		$('#login_box_top_sign_up').live('click', function(){
			var visible = $('#login_box_bottom_sign_up').is(":visible");
			$('#login_box_bottom_sign_in').hide();
			$('#login_box_bottom_sign_up').slideToggle();
			
			if(visible){
				$('#sign_up_arrow').css("background-image","url(<?php print($this->basepath); ?>tide/images/page-images/loginbar/toggle_down_light.png)");
				$('#login_box_top_sign_up').css("background-color","");
			} else {
				$('#sign_up_arrow').css("background-image","url(<?php print($this->basepath); ?>tide/images/page-images/loginbar/toggle_up_light.png)");
				$('#login_box_top_sign_up').css("background-color","#00CCFF");
			}
			$('#sign_in_arrow').css("background-image","url(<?php print($this->basepath); ?>tide/images/page-images/loginbar/toggle_down_light.png)");
			$('#login_box_top_sign_in').css("background-color","");
		});

		function logMeIn(){
			$('.loading_div').show();
			var username = $("#login_form_username").val();
			var password = $("#login_form_password").val();
			
			$.post("<?php echo $this->basepath; ?>" + "user/guest/login", {username: username, password: password}, function(data){
				if(data == "NOAUTH" || data == "0" || data == ""){
					alert("Data yang Anda masukkan salah: " + data);
					$('.loading_div').hide();
					return false;
				} else {
					$('.loading_div').hide();
					window.location.replace("<?php echo $this->basepath; ?>");
				}
	
			});
		}

		$("#login_form_username, #login_form_password").live('keydown', function(e){
			if(e.keyCode == 13){
				logMeIn();
			}
		});

		$("#login_form_submit_button").live('click', function(){
			logMeIn();
		});
		
		$("#login_form").submit(function(){
			return false;
		});


		function signMeUp(){
			$('.loading_div').show();
			var new_email = $("#new_email").val();
			var new_username = $("#new_username").val();
			var new_password = $("#new_password").val();
			var new_fullname = $("#new_fullname").val();
			var new_confirm_password = $("#new_confirm_password").val();
			
			var recaptcha_response_field = $("#recaptcha_response_field").val();
			var recaptcha_challenge_field = $("#recaptcha_challenge_field").val();
			
			// alert("rrf: " + recaptcha_response_field + ", rcf: " + recaptcha_challenge_field);
			
			if($.trim(new_email) == ""){
				alert("Email should not empty.");
				$("#new_email").focus();
				$('.loading_div').hide();
				return;
			}
			
			if($.trim(new_username) == ""){
				alert("Username should not empty.");
				$("#new_username").focus();
				$('.loading_div').hide();
				return;
			}
			
			// new_fullname
			if($.trim(new_fullname) == ""){
				alert("Full Name should not empty.");
				$("#new_fullname").focus();
				$('.loading_div').hide();
				return;
			}

			if(new_username.length < 4){
				alert("Use at least four characters for your username.");
				$("#new_username").focus();
				$('.loading_div').hide();
				return;
			}

			if($.trim(new_password) == ""){
				alert("Password should not empty.");
				$("#new_password").focus();
				$('.loading_div').hide();
				return;
			}
			
			if(new_password.length < 6){
				alert("Use at least six characters for your password.");
				$("#new_password").focus();
				$('.loading_div').hide();
				return;
			}
			
			if(new_password != new_confirm_password){
				alert("Your Confirm Password doesn't match with your Password!");
				$("#new_confirm_password").focus();
				$('.loading_div').hide();
				return;
			}
			
			if($.trim(recaptcha_response_field) == ''){
				alert('Kecapnya diisi dong Boss...');
				$('.loading_div').hide();
				return;
			}

			var email_used = redundancy_check('email', new_email);
			var password_used = redundancy_check('username', new_username);

			// kirim ke server
			$.post(	"<?php echo $this->basepath; ?>user/guest/add_user", 
						 	{'username':new_username,'fullname':new_fullname,'password':new_password,'email':new_email,'automate_login':'1', 'recaptcha_response_field':recaptcha_response_field, 'recaptcha_challenge_field':recaptcha_challenge_field}, 
						 	function(data){
								if($.trim(data) == "OK"){
									$('.loading_div').hide();
									window.location.replace("<?php echo $this->basepath; ?>");
								} else {
									alert("Server: " + data);
									Recaptcha.reload();
									$('.loading_div').hide();
									return;
								}
							}
			);
		}


		$("#signin_form_submit_button").live('click', function(){
			signMeUp();
		});
		
		$(".signup_data").live('keydown', function(e){
			if(e.keyCode == 13){
				signMeUp();
			}
		});

		$("#signin_form").submit(function(){
			return false;
		});

		$("#new_email").change(function(){
			var new_email = $("#new_email").val();
			var used = redundancy_check('email', new_email);
		});
		
		$("#new_username").change(function(){
			var new_username = $("#new_username").val();
			var used = redundancy_check('username', new_username);
		});
		
		function redundancy_check(check, value){
			var used = 0;
			switch(check){
				case 'email':
					$.post("<?php print($this->basepath); ?>user/guest/redundancy_check/email/" + value, {}, function(data){
						if(data != "0"){
							alert("Email " + value + " sudah terdaftar. Gunakan email lain.");
							$("#new_email").val("");
							$("#new_email").focus();
						}
					});
					break;
				case 'username':
					$.post("<?php print($this->basepath); ?>user/guest/redundancy_check/username/" + value, {}, function(data){
						if(data != "0"){
							alert("Username " + value + " sudah terdaftar. Gunakan username lain.");
							$("#new_username").val("");
							$("#new_username").focus();
						}
					});
					break;
			}
		}

	<?php
		} else {
			//	fungsi2 untuk user yg sudah login
	?>
		//
		//$("#message_dialog").dialog({
		//	autoOpen: false, 
		//	minWidth: 800, 
		//	minHeight: 400,
		//	modal: true
		//});

//
//		$('.top_profile_icon_msg').live('click', function(){
////			// 380 x 456
////			var cur_height = $('#unityPlayer').css('height');
////			
////			if(parseInt(cur_height) < 456){
////				$('#unityPlayer').css('height', '456px');
////				$('#unityPlayer').css('width', '380px');
////			} else {
////				$('#unityPlayer').css('height', '0.1px');
////				$('#unityPlayer').css('width', '380px');
////			}
////			
//			$.post("<?php print($this->basepath); ?>message/user/inboxdialog", {}, function(data){
//				$('#message_dialog').html(data);
//			});
//
//			$('#message_dialog').dialog('open');
//			
////			$('#div_webplayer').css('width', 0);
////			alert(cur_width);
//		});

		$('.top_profile_icon_logout_new').live('click', function(){
			$('.loading_div').show();
			if(!confirm('Are you sure to logout?')){
				$('.loading_div').hide();
				return;
			}
			
			window.location.replace("<?php print($this->basepath); ?>user/user/logout");
			
			//$.post("<?php print($this->basepath); ?>user/user/logout", {}, function(data){
			//	if(data == "1"){
			//		$('.loading_div').hide();
			//		window.location.replace("<?php echo $this->basepath; ?>");
			//	} else {
			//		$('.loading_div').hide();
			//		alert('Logout process failed.');
			//	}
			//});
		});


	<?php
		}
	?>


	});
</script>




<?php /* ?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=353789864649141";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<?php */ ?>


<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '353789864649141', // App ID
      channelUrl : '<?php echo $this->basepath; ?>/channel.html', // Channel File
      status     : true, // check login status
      cookie     : true, // enable cookies to allow the server to access the session
      xfbml      : true  // parse XFBML
    });

    // Additional initialization code here
  };

  // Load the SDK Asynchronously
  (function(d){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     ref.parentNode.insertBefore(js, ref);
   }(document));
</script>


<script type="text/javascript">
	var RecaptchaOptions = {
		theme : 'custom',
		custom_theme_widget: 'recaptcha_widget'
	};
</script>


<style>
	.whatis_normal{
		background: url(<?php print($this->basepath); ?>modules/011_articles/images_popbloopdark/whatis/button_what_is_normal.png) center left no-repeat;
	}
	.whatis_hover{
		background: url(<?php print($this->basepath); ?>modules/011_articles/images_popbloopdark/whatis/button_what_is_hover.png) center left no-repeat;
	}
	.whatis_click{
		background: url(<?php print($this->basepath); ?>modules/011_articles/images_popbloopdark/whatis/button_what_is_click.png) center left no-repeat;
	}
	#whatis_image{
		cursor: pointer;
		height: 38px;
	}
</style>


<script type="text/javascript">
$(document).ready(function(){
  $('#whatis_image').live({
    mousedown: function(){
      $(this).removeClass('whatis_normal');
      $(this).removeClass('whatis_hover');
      $(this).addClass('whatis_click');
    },
    mouseup: function(){
      $(this).removeClass('whatis_normal');
      $(this).removeClass('whatis_click');
      $(this).addClass('whatis_hover');
    },
    mouseenter: function(){
      $(this).removeClass('whatis_normal');
      $(this).removeClass('whatis_click');
      $(this).addClass('whatis_hover');
    },
    mouseleave: function(){
      $(this).removeClass('whatis_hover');
      $(this).removeClass('whatis_click');
      $(this).addClass('whatis_normal');
    }

  });

		
});
</script>

<div class="image_cache" style="display: none">
	<img src="<?php print($this->basepath); ?>modules/011_articles/images_popbloopdark/whatis/button_what_is_normal.png" />
	<img src="<?php print($this->basepath); ?>modules/011_articles/images_popbloopdark/whatis/button_what_is_hover.png" />
	<img src="<?php print($this->basepath); ?>modules/011_articles/images_popbloopdark/whatis/button_what_is_click.png" />
	
	
	
	<img src="<?php print($this->basepath); ?>modules/000_user_interface/images_popbloopdark/icons/notification.hover.png" />
	<img src="<?php print($this->basepath); ?>modules/000_user_interface/images_popbloopdark/icons/friend.hover.png" />
	<img src="<?php print($this->basepath); ?>modules/000_user_interface/images_popbloopdark/icons/msg.hover.png" />
	
</div>

<div class="container_12 pop_header">
  <div class="grid_6" style="height: 138px;">
		<div style="float: right; height: 138px; width: 175px;">
			<div style="height: 52px; width: 175px;">&nbsp;</div>
			<a href="<?php print($this->basepath); ?>whatis"><div style="height: 38px; width: 96px; float: left;" id="whatis_image" class="whatis_normal"></div></a>
			<div style="height: 38px; width: 79px; float: left;"></div>
		</div>
	</div>
  <div class="grid_6">
	<?php
  	if(!isset($_SESSION['user_id'])){
	?>
		<div id="login_box_top">
    	<div id="login_box_top_sign_in">Sign in <i id="sign_in_arrow"></i></div>
    	<div id="login_box_top_sign_up">Sign up <i id="sign_up_arrow"></i></div>
    </div>
		<div id="login_box_bottom">
    	<div id="login_box_bottom_sign_in" style="height:118px;">
      
      
      	<form>
      	<div class="sign_content" style="width:100%; height:12px;">&nbsp;</div>
      	<div class="sign_content" style="width:25%;">Username</div>
      	<div class="sign_content" style="width:75%;"><input type="text" name="login_form_username" id="login_form_username" title="Username" value="" maxlength="30" /></div>
      	<div class="sign_content" style="width:25%;">Password</div>
      	<div class="sign_content" style="width:75%;"><input type="password" name="login_form_password" id="login_form_password" title="Password" value="" /></div>
        
      	<div class="sign_content" style="width:25%;">&nbsp;</div>
      	<div class="sign_content" style="width:0%; text-align:left;"><!--[*<input type="checkbox" name="remember_me" id="remember_me" />&nbsp;<label for="remember_me">Remember me</label>*]-->&nbsp;</div>
      	<div class="sign_content" style="width:75%; text-align:center;"><input type="button" name="submit" id="login_form_submit_button" value="Login" style="max-width:120px;" /></div>

      	<div class="sign_content" style="width:100%; text-align:center;"><a href="#" style="text-decoration:none; cursor:pointer; color:#FFF;">Forgot your password?</a></div>
        </form>
        <!--[*
        <div style="text-align:center">
        	<div class="fb-login-button">Login with Facebook</div>
        </div>
        *]-->
      </div>
    	<div id="login_box_bottom_sign_up" style="height:300px;">

      
      	<form>
      	<div class="sign_content" style="width:100%; height:12px;">&nbsp;</div>
      	<div class="sign_content" style="width:42%;">Email</div>
      	<div class="sign_content" style="width:58%;"><input type="text" class="signup_data" name="new_email" id="new_email" value="" title="Email" /></div>
      	<div class="sign_content" style="width:42%;">Username</div>
      	<div class="sign_content" style="width:58%;"><input type="text" class="signup_data" name="new_username" id="new_username" value="" title="Username" maxlength="30" /></div>
      	<div class="sign_content" style="width:42%;">Fullname</div>
      	<div class="sign_content" style="width:58%;"><input type="text" class="signup_data" name="new_fullname" id="new_fullname" value="" title="Fullname" maxlength="40" /></div>
      	<div class="sign_content" style="width:42%;">Password</div>
      	<div class="sign_content" style="width:58%;"><input type="password" class="signup_data" name="new_password" id="new_password" value="" title="Password" /></div>
      	<div class="sign_content" style="width:42%;">Confirm Password</div>
      	<div class="sign_content" style="width:58%;"><input type="password" class="signup_data" name="new_confirm_password" id="new_confirm_password" value="" title="Confirm Password" /></div>
        
        <div class="sign_content" style="width:100%; height:120px;">
          <div id="recaptcha_widget" style="display:none">
          
            <div id="recaptcha_image" style="width:100px;"></div>
            <div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect please try again</div>
            <div style="width:42%; float:left;" class="recaptcha_only_if_image sign_content">Enter the words</div>
            <div style="width:42%; float:left;" class="recaptcha_only_if_audio sign_content">Enter the numbers</div>
            
            <div style="width:58%; float:left;" class="sign_content"><input type="text" id="recaptcha_response_field" name="recaptcha_response_field" /></div>
            
            <div title="Get another CAPTCHA" style="width:50%; float:left"><a style="text-decoration:none; color:#FFF; cursor:pointer;" href="javascript:Recaptcha.reload()">Reload</a></div>
            
            <!--[
            <div title="Get an audio CAPTCHA" class="recaptcha_only_if_image" style="width:35%; float:left"><a style="text-decoration:none; color:#FFF; cursor:pointer;" href="javascript:Recaptcha.switch_type('audio')">Audio</a></div>
            <div title="Get an image CAPTCHA" class="recaptcha_only_if_audio" style="width:35%; float:left"><a style="text-decoration:none; color:#FFF; cursor:pointer;" href="javascript:Recaptcha.switch_type('image')">Image</a></div>
            ]-->
            
            <div title="Help" style="width:50%; float:left"><a style="text-decoration:none; color:#FFF; cursor:pointer;" href="javascript:Recaptcha.showhelp()">Help</a></div>
            
          </div>
            
          <script type="text/javascript"
          src="http://www.google.com/recaptcha/api/challenge?k=<?php echo $publickey; ?>">
          </script>

          <noscript>
            <iframe src="http://www.google.com/recaptcha/api/noscript?k=<?php echo $publickey; ?>"
            height="200" width="250" frameborder="0"></iframe><br>
            <textarea name="recaptcha_challenge_field" id="recaptcha_challenge_field" rows="3" cols="40">
            </textarea>
            <input type="hidden" name="recaptcha_response_field"
            value="manual_challenge">
          </noscript>

        </div>
        
        
      	<div class="sign_content" style="width:42%;">&nbsp;</div>
      	<div class="sign_content" style="width:58%;"><input type="button" name="submit" id="signin_form_submit_button" value="Register" style="max-width:120px;" /></div>
        </form>
      </div>
    </div>
	<?php
		} else {
	?>
  
  	<div class="grid_1 alpha">&nbsp;</div>
  	<div class="grid_5 omega" style="display:table-cell; vertical-align:bottom; height:138px; text-align:right;">
    
      <div style="margin:34px 0 0 0; float:right;">
        <a href="<?php print($this->basepath); ?>profile/<?php echo $this->user_property->username; ?>">
          <img class="top_profile_picture" src="<?php echo $this->basepath; ?>user_generated_data/profile_picture/<?php echo $this->user_property->profile_picture; ?>" />
        </a>
      </div>
    	<div style="margin:34px 10px 0 0; float:right;">
      	<!--[*
        <a style="text-decoration:none; color:#FFF; font-size:16px;" href="<?php print($this->basepath); ?>myprofile"><?php echo $this->user_property->fullname; ?></a>
        <a style="text-decoration:none; color:#FFF;" href="<?php print($this->basepath); ?>messages"><?php echo (int)$this->user_property->count_unread_msg . " unread message(s)"; ?></a>
        <a style="text-decoration:none; color:#FFF;" href="<?php print($this->basepath); ?>achievement"><?php echo (int)$this->user_property->power . " power"; ?></a>
        *]-->
      	
        <div style="height:16px; padding:0px 10px 7px 0;"><a style="text-decoration:none; color:#b0c836; font-size:16px;" href="<?php print($this->basepath); ?>profile/<?php echo $this->user_property->username; ?>"><?php echo $this->user_property->fullname; ?></a></div>
        <!--[*
        <div class="top_profile_icon top_profile_icon_logout">
        </div>
        *]-->
        <div class="top_profile_icon top_profile_icon_notification">
        	<div class="top_profile_notification_text" style="display: none;"><?php //kalo blm ada notifikasi, di-hide dulu ?>
        	&nbsp;
        	<?php // masukkan jumlah notifikasi disini: mis 123 ?>
          </div>
        </div>
        <div class="top_profile_icon  top_profile_icon_msg">
        	<?php
          if((int)$this->user_property->count_unread_msg != 0){
					?>
        	<div class="top_profile_notification_text">
        	<?php echo (int)$this->user_property->count_unread_msg; ?>
          </div>
        	<?php
					}
					?>
        </div>
      	<div class="top_profile_icon top_profile_icon_friend">
        	<div class="top_profile_notification_text" style="display: none;"><?php //kalo blm ada notifikasi, di-hide dulu ?>
        	&nbsp;
					<?php // masukkan jumlah notifikasi disini: mis 123 ?>
          </div>
        </div>
        
        <div class="clear"></div>
        
        <div style="width:100%; height: 24px; color:#fff;">
          <div class="top_profile_icon_logout_new_" style="float:right; width: 60px; font-size:12px; padding:3px 10px 0 10px; cursor: pointer;"><a style="text-decoration:none" href="<?php print($this->basepath); ?>user/user/logout">sign out</a></div>
          <div style="float:right; width: 100px; font-size:12px; padding:3px 0 0 10px;"><a href="<?php print($this->basepath); ?>myprofile" style="text-decoration:none; cursor:pointer; color:#fff;">my account</a></div>
        </div>
        
      </div>
    </div>
  
	<?php
		}
	?>
	</div>
  <div class="clear"></div>
</div>

<div class="container_12 pop_main_menu">
  <div class="grid_8 pop_main_menu_left">
  <!--[*
		<a href="<?php print($this->basepath); ?>home" style="text-decoration:none; color:#CCC;"><div class="pop_main_menu_item">Home</div></a>
    
    <?php if(isset($_SESSION['user_id'])){ ?>
			<a href="<?php print($this->basepath); ?>myavatar" style="text-decoration:none; color:#CCC;"><div class="pop_main_menu_item">Avatar</div></a>
    <?php } ?>
    
		<a href="<?php print($this->basepath); ?>play" style="text-decoration:none; color:#CCC;"><div class="pop_main_menu_item">Play</div></a>
		<a href="<?php print($this->basepath); ?>shop" style="text-decoration:none; color:#CCC;"><div class="pop_main_menu_item">Shop</div></a>
		<a href="<?php print($this->basepath); ?>support" style="text-decoration:none; color:#CCC;"><div class="pop_main_menu_item">Support</div></a>
    
  *]-->

			<?php
				//$hide_child = ($_GET['q'] == 'play' && isset($_SESSION['user_id'])) || $_GET['q'] == 'ui/user/play';
				//$hide_child = $hide_child || ($_GET['q'] == 'myavatar' && isset($_SESSION['user_id'])) || $_GET['q'] == 'ui/user/avatar_editor';
				
				// sementara di-hide semua
				$hide_child = true;
				
			?>
  
        <ul class="dropdown" style="z-index:1000; height:100%">
        	<li <?php if($_REQUEST['q'] == 'message/user/status'){ ?>style="background-color:#ff00aa;"<?php } ?>><a href="<?php print($this->basepath); ?>home">Home</a></li>
        	<li <?php if($_REQUEST['q'] == 'ui/user/play'){ ?>style="background-color:#ff00aa;"<?php } ?>><a href="<?php print($this->basepath); ?>play">Hangout</a></li>


        	<li <?php if($_REQUEST['q'] == 'user/user/properties' || $_REQUEST['q'] == 'user/guest/property/'. $_SESSION['username'] || $_REQUEST['q'] == 'friend/user/people' || $_REQUEST['q'] == 'friend/user/list' || $_REQUEST['q'] == 'ui/user/avatar_editor_categorized'){ ?>style="background-color:#ff00aa;"<?php } ?>><a href="<?php print($this->basepath); ?>profile/<?php echo $_SESSION['username']; ?>">Social</a>
          
    			<?php if(isset($_SESSION['user_id'])){ ?>
          
						<?php
            if(!$hide_child){
            ?>
              <ul class="sub_menu">
                 <!--li><a href="social">Status</a></li-->
                 <li><a href="<?php print($this->basepath); ?>myprofile">Profile</a></li>
                 <li><a href="<?php print($this->basepath); ?>people">People</a></li>
                 <li><a href="<?php print($this->basepath); ?>myavatar">Avatar Editor</a></li>
                 <!--li><a href="<?php print($this->basepath); ?>howto">How To</a></li-->
              </ul>
            <?php
            }
            ?>
            </li>

			    <?php } ?>

          
    			<?php /*if(isset($_SESSION['user_id'])){ ?>
        	<li><a href="<?php print($this->basepath); ?>store">Store</a></li>
			    <?php }*/ ?>
          
        	<li <?php if($_REQUEST['q'] == 'article/guest/read/faq' || $_REQUEST['q'] == 'article/guest/read/howto' || $_REQUEST['q'] == 'article/guest/read/troubleshooting'){ ?>style="background-color:#ff00aa;"<?php } ?>><a href="<?php print($this->basepath); ?>support">Support</a>
          <?php
          if(!$hide_child){
					?>
        		<ul class="sub_menu">
        			 <li><a href="<?php print($this->basepath); ?>faq">FAQ</a></li>
               <li><a href="<?php print($this->basepath); ?>howto">How To</a></li>
        			 <li><a href="<?php print($this->basepath); ?>troubleshooting">Troubleshooting</a></li>
        		</ul>
          <?php
					}
					?>
        	</li>
        </ul>

  
  
  
	</div>
  
	<style>
		.search_input{
			width:180px; height:26px; text-align:center; background-color:#666; color:#1f1f1f; border:0; font-family:inherit; font-weight:normal; font-size:13px;
		}
	</style>
	
	<script type="text/javascript">
	$(document).ready(function(){
		$('.search_button, .search_input').live({
			focus: function(){
				$('.search_button, .search_input').css('background-color', '#a1a1a1');
				$('.search_button, .search_input').css('color', '#434343');
			},
			blur: function(){
				$('.search_button, .search_input').css('background-color', '#666666');
				$('.search_button, .search_input').css('color', '#1f1f1f');
			}
		});
	});
	</script>
	
  <div class="grid_4 pop_main_menu_right">
  	<!--[*
		<div style="width:70%; height:100%; float:left; text-align:center; background:url(<?php print($this->basepath); ?>/tide/popbloop.img/search.box.bg.png) center no-repeat;"></div>
		<div style="width:30%; height:100%; float:left; text-align:center; background:url(<?php print($this->basepath); ?>/tide/popbloop.img/fb.t.png) center no-repeat;"></div>
    *]-->
    <div style="width:210px; height:40px; padding:6px 0; float:left;">
      <div style="width:180px; float:left;">
        <input type="text" class="round_left_gajadi search_input" placeholder="Search" style="" />
      </div>
      <div style="width:30px; float:left; text-align:center; height:28px; cursor:pointer; background:url(<?php print($this->basepath); ?>/tide/popbloop.img/search.box.button.png) left no-repeat #666;" class="round_right_gajadi search_button">&nbsp;
      </div>
    </div>
    <div style="width:90px; height:100%; float:left; background:url(<?php print($this->basepath); ?>/tide/popbloop.img/fb.t.png) center no-repeat;">
			<div style="float: left; height: 100%; width: 20px;">&nbsp;</div>
			<a target="_blank" title="Join our Facebook Page..." href="http://www.facebook.com/PopBloop"><div style="float: left; height: 100%; width: 20px;">&nbsp;</div></a>
			<div style="float: left; height: 100%; width: 10px;">&nbsp;</div>
			<a target="_blank" title="Follow PopBloop on Twitter..." href="https://twitter.com/popbloop"><div style="float: left; height: 100%; width: 20px;">&nbsp;</div></a>
			<div style="float: left; height: 100%; width: 20px;">&nbsp;</div>
    </div>

	</div>
  <div class="clear"></div>
</div>

<?php
if(count($_SESSION['pop_error_msg'])){
?>
<div class="container_12 pop_error_msg" style="padding-top:20px; cursor:pointer;" title="Click to dismiss.">
  <div class="grid_12 transparent_70">
  	<?php
    	foreach($_SESSION['pop_error_msg'] as $error){
				print("&bull;&nbsp;" . $error . "<br />\n\r");
			}
		?>
	</div>
  <div class="clear"></div>
</div>

<?php
	unset($_SESSION['pop_error_msg']);
}
?>



<?php
if(count($_SESSION['pop_status_msg'])){
?>

<div class="container_12 pop_status_msg" style="padding-top:20px; cursor:pointer;" title="Click to dismiss.">
  <div class="grid_12 transparent_70">
  	<?php
    	foreach($_SESSION['pop_status_msg'] as $status){
				print("&bull;&nbsp;" . $status . "<br />\n\r");
			}
		?>
	</div>
  <div class="clear"></div>
</div>

<?php
	unset($_SESSION['pop_status_msg']);
}
?>

<?php
//print("<pre>".print_r($this, true)."</pre>");
?>


	<?php
  	/*if(isset($_SESSION['user_id'])){
	?>

  <div style="width: auto; min-height: 400px; height: auto; min-width:800px; " class="ui-dialog-content ui-widget-content" id="message_dialog" 
    title="Messages">
    
	</div>
  
  <?php
		}*/
	?>


<style>
	.blue_box{
		width: 213px;
		height: 127px;
		background-color: #1f1f1f;
		float: left;
		text-align: center;
		display: table;
	}
	
	.left_box{
		margin: 0 14px 0 0;
	}
	.center_box{
		margin: 0 15px 0 15px;
	}
	.right_box{
		margin: 0 0 0 14px;
	}
	
	
	.box_img{
		display: table-cell;
		vertical-align: middle;
		cursor: pointer;
	}
	
</style>

<script type="text/javascript">
$(document).ready(function(){
	$('.box_img_btn').live({
		mouseenter: function(){
			// dapatkan current image
			var cur_img = $(this).attr('src');
			var cur_img_split = cur_img.split('/getting.started/');
			var img_file = cur_img_split[1];
			// alert(img_file);return;
			var img_file_split = img_file.split('.');
			// alert(img_file_split[0]);return;
			
			// hilangkan semua '_hover' dan '_click' dari img_file_split[0]
			img_file_split[0] = img_file_split[0].replace('_click', '');
			img_file_split[0] = img_file_split[0].replace('_hover', '');
			
			var new_src = cur_img_split[0] + '/getting.started/' + img_file_split[0] + '_hover' + '.' + img_file_split[1];
			//alert(new_src);
			$(this).attr('src', new_src);
		},
		mouseleave: function(){
			// dapatkan current image
			var cur_img = $(this).attr('src');
			var cur_img_split = cur_img.split('/getting.started/');
			var img_file = cur_img_split[1];
			// alert(img_file);return;
			var img_file_split = img_file.split('.');
			// alert(img_file_split[0]);return;
			
			// hilangkan semua '_hover' dan '_click' dari img_file_split[0]
			img_file_split[0] = img_file_split[0].replace('_click', '');
			img_file_split[0] = img_file_split[0].replace('_hover', '');
			
			var new_src = cur_img_split[0] + '/getting.started/' + img_file_split[0] + '.' + img_file_split[1];
			//alert(new_src);
			$(this).attr('src', new_src);
		},
		mouseup: function(){
			// dapatkan current image
			var cur_img = $(this).attr('src');
			var cur_img_split = cur_img.split('/getting.started/');
			var img_file = cur_img_split[1];
			// alert(img_file);return;
			var img_file_split = img_file.split('.');
			// alert(img_file_split[0]);return;
			
			// hilangkan semua '_hover' dan '_click' dari img_file_split[0]
			img_file_split[0] = img_file_split[0].replace('_click', '');
			img_file_split[0] = img_file_split[0].replace('_hover', '');
			
			var new_src = cur_img_split[0] + '/getting.started/' + img_file_split[0] + '_hover' + '.' + img_file_split[1];
			//alert(new_src);
			$(this).attr('src', new_src);
		},
		mousedown: function(){
			// dapatkan current image
			var cur_img = $(this).attr('src');
			var cur_img_split = cur_img.split('/getting.started/');
			var img_file = cur_img_split[1];
			// alert(img_file);return;
			var img_file_split = img_file.split('.');
			// alert(img_file_split[0]);return;
			
			// hilangkan semua '_hover' dan '_click' dari img_file_split[0]
			img_file_split[0] = img_file_split[0].replace('_click', '');
			img_file_split[0] = img_file_split[0].replace('_hover', '');
			
			var new_src = cur_img_split[0] + '/getting.started/' + img_file_split[0] + '_click' + '.' + img_file_split[1];
			//alert(new_src);
			$(this).attr('src', new_src);
		}
	});
});
</script>

<?php /* ?>
<div class="container_12">
	<div class="grid_12" style="height: 13px;">&nbsp;
	</div>
	<div class="clear"></div>
	<div class="grid_12">
		<div class="blue_box left_box"><a href="<?php echo $this->basepath; ?>myprofile"><div class="box_img"><img class="box_img_btn" id="complete_prof" src="<?php echo $this->basepath; ?>images/getting.started/complete_prof.jpg" /></div></a></div>
		<div class="blue_box center_box"><a href="<?php echo $this->basepath; ?>myavatar"><div class="box_img"><img class="box_img_btn" id="custom_avatar" src="<?php echo $this->basepath; ?>images/getting.started/custom_avatar.jpg" /></div></a></div>
		<div class="blue_box center_box"><a href="<?php echo $this->basepath; ?>play"><div class="box_img"><img class="box_img_btn" id="start_hangout" src="<?php echo $this->basepath; ?>images/getting.started/start_hangout.jpg" /></div></a></div>
		<div class="blue_box right_box"><a href="<?php echo $this->basepath; ?>support"><div class="box_img"><img class="box_img_btn" id="need_help" src="<?php echo $this->basepath; ?>images/getting.started/need_help.jpg" /></div></a></div>
	</div>
	<div class="clear"></div>
	<div class="grid_12" style="height: 10px;">&nbsp;
	</div>
	<div class="clear"></div>
</div>


<div class="image_loader" style="display: none;">
	<img src="<?php echo $this->basepath; ?>images/getting.started/complete_prof_hover.jpg" />
	<img src="<?php echo $this->basepath; ?>images/getting.started/custom_avatar_hover.jpg" />
	<img src="<?php echo $this->basepath; ?>images/getting.started/start_hangout_hover.jpg" />
	<img src="<?php echo $this->basepath; ?>images/getting.started/need_help_hover.jpg" />
	
	<img src="<?php echo $this->basepath; ?>images/getting.started/complete_prof_click.jpg" />
	<img src="<?php echo $this->basepath; ?>images/getting.started/custom_avatar_click.jpg" />
	<img src="<?php echo $this->basepath; ?>images/getting.started/start_hangout_click.jpg" />
	<img src="<?php echo $this->basepath; ?>images/getting.started/need_help_click.jpg" />
</div>

<?php */ ?>

</div><!--[ end withjs ]-->


