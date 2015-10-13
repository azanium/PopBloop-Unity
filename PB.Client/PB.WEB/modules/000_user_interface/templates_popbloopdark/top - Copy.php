<?php
	include_once('modules/000_user_interface/guest.php');
	include_once('modules/000_user_interface/user.php');
	
	// recaptcha
	require_once('libraries/recaptcha/recaptchalib.php');
	$publickey = '6Lc4rc0SAAAAABnStfbcMto4QuRhJzMPU4Hq5UfV';
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

<script language="javascript" src="<?php print($this->basepath); ?>libraries/js/jquery.ui/js/jquery-ui-1.8.14.custom.min.js"></script>

<link rel="stylesheet" type="text/css" href="<?php print($this->basepath); ?>libraries/js/jquery.ui/css/custom-theme/jquery-ui-1.8.14.custom.css" />

<link rel="stylesheet" type="text/css" media="screen" href="<?php print($this->basepath); ?>modules/000_user_interface/css/default_popbloopdark.css" />

<!-- Include the below script, Copyright 2010, Brandon Aaron (http://brandonaaron.net/) for scrollwheel support. -->
<script type="text/javascript" src="<?php print($this->basepath); ?>tide/js/mylibs/slidedeck/jquery-mousewheel/jquery.mousewheel.min.js"></script>

<link rel="stylesheet" type="text/css" href="<?php print($this->basepath); ?>tide/js/mylibs/slidedeck/slidedeck.skin.css" media="screen" />
<!-- Styles for the skin that only load for Internet Explorer -->
<!--[if lte IE 8]>
<link rel="stylesheet" type="text/css" href="js/mylibs/slidedeck/slidedeck.skin.ie.css" media="screen,handheld" />
<![endif]-->


<!-- Include the SlideDeck jQuery script. -->
<script type="text/javascript" src="<?php print($this->basepath); ?>tide/js/mylibs/slidedeck/slidedeck.jquery.lite.pack.js"></script>


<!--[ Include the jScrollPane jQuery script & CSS. ]-->

<!-- styles needed by jScrollPane - include in your own sites -->
<link type="text/css" href="<?php print($this->basepath); ?>libraries/js/jScrollPane/style/jquery.jscrollpane.css" rel="stylesheet" media="all" />

<!-- the mousewheel plugin -->
<script type="text/javascript" src="<?php print($this->basepath); ?>libraries/js/jScrollPane/script/jquery.mousewheel.js"></script>
<!-- the jScrollPane script -->
<script type="text/javascript" src="<?php print($this->basepath); ?>libraries/js/jScrollPane/script/jquery.jscrollpane.min.js"></script>


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
		
		$('.slidedeck').slidedeck({
			autoPlay: true,
			cycle: true, 
			autoPlayInterval: 5000 
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
			var username = $("#login_form_username").val();
			var password = $("#login_form_password").val();
			
			$.post("<?php echo $this->basepath; ?>" + "user/guest/login", {username: username, password: password}, function(data){
				if(data == "NOAUTH" || data == "0" || data == ""){
					alert("Data yang Anda masukkan salah: " + data);
					return false;
				} else {
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
		}
	?>


	});
</script>





<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=353789864649141";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>


<script type="text/javascript">
	var RecaptchaOptions = {
		theme : 'custom',
		custom_theme_widget: 'recaptcha_widget'
	};
</script>


<div class="container_12 pop_header">
  <div class="grid_6">
  &nbsp;
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
  	<div class="grid_3" style="text-align:right;">
    	<div style="margin-top:10px;">
      	<a style="text-decoration:none; color:#FFF; font-weight:bold; font-size:+2;" href="<?php print($this->basepath); ?>profile"><?php echo $this->user_property->fullname; ?></a><br />
      	<a style="text-decoration:none; color:#FFF;" href="<?php print($this->basepath); ?>messages"><?php echo (int)$this->user_property->count_unread_msg . " unread message(s)"; ?></a><br />
      	<a style="text-decoration:none; color:#FFF;" href="<?php print($this->basepath); ?>achievement"><?php echo (int)$this->user_property->power . " power"; ?></a><br />
      </div>
    </div>
    <a href="<?php print($this->basepath); ?>profile">
  	<div class="grid_2 omega" style="display:table-cell; vertical-align:bottom; height:100px; text-align:center;">
			<img style="max-width:140px; max-height:80px; margin-top:10px;" src="<?php echo $this->basepath; ?>user_generated_data/profile_picture/<?php echo $this->user_property->profile_picture; ?>" />
    </div>
    </a>
  
	<?php
		}
	?>
	</div>
  <div class="clear"></div>
</div>

<div class="container_12 pop_main_menu">
  <div class="grid_8 pop_main_menu_left">
		<a href="<?php print($this->basepath); ?>home" style="text-decoration:none; color:#CCC;"><div class="pop_main_menu_item">Home</div></a>
    
    <?php if(isset($_SESSION['user_id'])){ ?>
			<a href="<?php print($this->basepath); ?>myavatar" style="text-decoration:none; color:#CCC;"><div class="pop_main_menu_item">Avatar</div></a>
    <?php } ?>
    
		<a href="<?php print($this->basepath); ?>play" style="text-decoration:none; color:#CCC;"><div class="pop_main_menu_item">Play</div></a>
		<a href="<?php print($this->basepath); ?>shop" style="text-decoration:none; color:#CCC;"><div class="pop_main_menu_item">Shop</div></a>
		<a href="<?php print($this->basepath); ?>support" style="text-decoration:none; color:#CCC;"><div class="pop_main_menu_item">Support</div></a>
	</div>
  <div class="grid_4 pop_main_menu_right">
		&nbsp;
	</div>
  <div class="clear"></div>
</div>

<?php
if(count($_SESSION['pop_error_msg'])){
?>
<div class="container_12 pop_error_msg" style="padding-top:20px;">
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

<div class="container_12 pop_status_msg" style="padding-top:20px;">
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
</div><!--[ end withjs ]-->
