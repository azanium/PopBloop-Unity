<?php
	include_once('modules/000_user_interface/user.php');
	$is_ie = ui_user_user_agent('is_ie');
	$is_mozilla = ui_user_user_agent('is_mozilla');
?>

<script src="<?php echo $this->basepath; ?>libraries/js/jquery.cycle.all.latest.js"></script>
<script src="<?php echo $this->basepath; ?>libraries/js/jquery_placeholder.js"></script>

<script language="javascript">

	$(document).ready(function(){
		$("#login_form_submit_button").click(function(){
			var username = $("#login_form_username").val();
			var password = $("#login_form_password").val();
			
			$.get("<?php echo $this->basepath; ?>" + "user/user/login/" + username + "/" + password, {}, function(data){
				if(data == "NOAUTH" || data == "0" || data == ""){
					alert("Data yang Anda masukkan salah: " + data);
					return false;
				} else {
					window.location.replace("<?php echo $this->basepath; ?>");
				}
	
			});
			
		});
		
		$("#login_form").submit(function(){
			return false;
		});
		
		// TODO: USE JQuery validation plugin!!!
		$("#signin_form_submit_button").click(function(){
			var new_email = $("#new_email").val();
			var new_username = $("#new_username").val();
			var new_password = $("#new_password").val();
			var new_confirm_password = $("#new_confirm_password").val();
			
			if($.trim(new_email) == ""){
				alert("Email should not empty.");
				$("#new_email").focus();
				return;
			}
			
			if($.trim(new_username) == ""){
				alert("Username should not empty.");
				$("#new_username").focus();
				return;
			}

			if(new_username.length < 4){
				alert("Use at least four characters for your username.");
				$("#new_username").focus();
				return;
			}

			if($.trim(new_password) == ""){
				alert("Password should not empty.");
				$("#new_password").focus();
				return;
			}
			
			if(new_password.length < 6){
				alert("Use at least six characters for your password.");
				$("#new_password").focus();
				return;
			}
			
			if(new_password != new_confirm_password){
				alert("Your Confirm Password doesn't match with your Password!");
				$("#new_confirm_password").focus();
				return;
			}

			var email_used = redundancy_check('email', new_email);
			var password_used = redundancy_check('username', new_username);

			// kirim ke server
			$.post(	"<?php echo $this->basepath; ?>user/user/add_user", 
						 	{'username':new_username,'password':new_password,'email':new_email,'automate_login':'1'}, 
						 	function(data){
								if($.trim(data) == "OK"){
									window.location.replace("<?php echo $this->basepath; ?>");
								} else {
									alert("Server: " + data);
									return;
								}
							}
			);

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
					$.post("<?php print($this->basepath); ?>user/user/redundancy_check/email/" + value, {}, function(data){
						if(data != "0"){
							alert("Email " + value + " sudah terdaftar. Gunakan email lain.");
							$("#new_email").val("");
							$("#new_email").focus();
						}
					});
					break;
				case 'username':
					$.post("<?php print($this->basepath); ?>user/user/redundancy_check/username/" + value, {}, function(data){
						if(data != "0"){
							alert("Username " + value + " sudah terdaftar. Gunakan username lain.");
							$("#new_username").val("");
							$("#new_username").focus();
						}
					});
					break;
			}
		}

	});

<?php
// jquery cycle agak2 tricky utk IE dan Firefox
//if($is_ie){
if($is_mozilla){
?>
	$(document).ready(function() {
		$('#gallery').cycle({
			slideExpr: 'img',
			fx: 'fade'
		});
	});
/*
	var imagesRemaining = 2; // the number of images in the slideshow div

	$(document).ready(function() {
		// To activate on all tags with a 'data-watermark' attribute
		$('input[placeholder],textarea[placeholder]').placeholder();

		$('#gallery > img').bind('load', function(e) {
			imagesRemaining = imagesRemaining - 1;
			if (imagesRemaining == 0) {
				// I'm doing some other stuff when initializing cycle
				startCycle();
				// My images all start with visibility:hidden so they don't show
				// before cycle hides them in a 'stack', so ...
				$('#gallery > img').css('visibility', 'visible');
			}
		});
	});
	
//	function onBefore(curr, next, opts) {
//	}
	
	function startCycle() {
		$('#gallery').cycle({
			slideExpr: 'img',
			fx: 'fade'
//			fx: 'scrollRight, scrollLeft'
//			fx: 'turnDown, fade'
		});
	}
*/
<?php
} else {
?>
	$(document).ready(function() {
		$('input[placeholder],textarea[placeholder]').placeholder();
		$('#gallery').cycle({
			slideExpr: 'img',
			fx: 'fade'
		});
	});

<?php
}
?>



</script>

<div class="centered transbg" style="width:960px; height:540px; border:none;">
	<div style="float:left; width:960px; height:80px;">

  </div>

	<!--[slideshow]-->
	<div style="float:left; width:630px; height:375px;">
    <div id="gallery" style="width:630px; height:375px;">
      <img width="600px" height="375px" src="<?php echo $this->basepath; ?>modules/000_user_interface/images/slideshow.001.png" alt="LiloCity is now on Facebook!" title="LiloCity is now on Facebook!">
      <img width="600px" height="375px" src="<?php echo $this->basepath; ?>modules/000_user_interface/images/slideshow.002.png" alt="3D Chat, Build Your Own Tribe, Create Your Unique Avatar on LiloCity!" title="3D Chat, Build Your Own Tribe, Create Your Unique Avatar on LiloCity!">
    </div>
  </div>
	<!--[/slideshow]-->
  
  <!--[login | sign-in form]-->
  
	<div style="float:left; width:330px; text-align:left">
    <div class="label_text">Lilo Citizen? Login here...</div>
    <form id="login_form">
      <div><input type="text" name="login_form_username" id="login_form_username" title="Username" placeholder="Username" class="light_shadow transparent_70" /></div>
      <div><input type="password" name="login_form_password" id="login_form_password" title="Password" placeholder="Password" class="light_shadow transparent_70" /></div>
      <div style="text-align:left"><input type="submit" value="Login" id="login_form_submit_button" style="width:100px;" class="light_shadow transparent_70" /></div>
    </form>
		
    <div class="label_text"><br />New to the City?<br />Be a Lilo Citizen Now!</div>
    <form id="signin_form">
      <div><input type="text" name="new_email" id="new_email" title="Email" placeholder="Email" class="light_shadow transparent_70" /></div>
      <div><input type="text" name="new_username" id="new_username" title="Username" placeholder="Username" class="light_shadow transparent_70" /></div>
      <div><input type="password" name="new_password" id="new_password" title="Password" placeholder="Password" class="light_shadow transparent_70" /></div>
      <div><input type="password" name="new_confirm_password" id="new_confirm_password" title="Confirm Password" placeholder="Confirm Password" class="light_shadow transparent_70" /></div>
      <div style="text-align:left"><input type="submit" value="Register" id="signin_form_submit_button" style="width:100px;" class="light_shadow transparent_70" /></div>
    </form>

  </div>
  
  <!--[/login | sign-in form]-->
</div>
