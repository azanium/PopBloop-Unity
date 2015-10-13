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


<script language="javascript" src="<?php print($this->basepath); ?>libraries/js/jquery.ui.popbloop.dark/js/jquery-ui-1.8.17.custom.min.js"></script>

<link rel="stylesheet" type="text/css" href="<?php print($this->basepath); ?>libraries/js/jquery.ui.popbloop.dark/css/custom-theme/jquery-ui-1.8.17.custom.css" />

<link rel="stylesheet" type="text/css" media="screen" href="<?php print($this->basepath); ?>modules/000_user_interface/css/default_popbloopdark.css" />

<?php  ?>
<script src="<?php echo $this->basepath; ?>libraries/js/jquery_placeholder.js"></script>
<style>
		label.placeholder {
		cursor: text;

		padding: 6px 4px 4px 14px;
		font-size: 12px;
		font-weight: normal;

		color: #999999;
	}

	input:placeholder, textarea:placeholder {
		color: #999999;
	}
	input::-webkit-input-placeholder, textarea::-webkit-input-placeholder {
		color: #999999;
	}

</style>

<script language="javascript">
	jQuery(document).ready(function(){
		jQuery('input[placeholder],textarea[placeholder]').placeholder();
	});
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
jQuery(document).ready(function(){
  jQuery('#whatis_image').live({
    mousedown: function(){
      jQuery(this).removeClass('whatis_normal');
      jQuery(this).removeClass('whatis_hover');
      jQuery(this).addClass('whatis_click');
    },
    mouseup: function(){
      jQuery(this).removeClass('whatis_normal');
      jQuery(this).removeClass('whatis_click');
      jQuery(this).addClass('whatis_hover');
    },
    mouseenter: function(){
      jQuery(this).removeClass('whatis_normal');
      jQuery(this).removeClass('whatis_click');
      jQuery(this).addClass('whatis_hover');
    },
    mouseleave: function(){
      jQuery(this).removeClass('whatis_hover');
      jQuery(this).removeClass('whatis_click');
      jQuery(this).addClass('whatis_normal');
    }

  });

		
});
</script>


<?php  ?>

<?php /* ?>
<!--[testing start]-->
<script src="<?php echo $this->basepath; ?>libraries/js/jquery.addplaceholder.clean.js"></script>
<script language="javascript">
	jQuery(document).ready(function(){
		jQuery('#signin_username').addPlaceholder();
		jQuery('#signin_password').addPlaceholder();
		
		jQuery('#signup_fullname').addPlaceholder();
		jQuery('#signup_email').addPlaceholder();
		jQuery('#signup_password').addPlaceholder();
	});
</script>
<!--[testing end]-->
<?php */ ?>

<script language="javascript">
	jQuery(document).ready(function(){
  
    jQuery('#btn_join').live('click', function(){
      jQuery('#signupform').submit();
    });
  
    jQuery('#signin_btn').live('click', function(){
      logMeIn();
    });
  
		function logMeIn(){
			jQuery('.loading_div').show();
			var username = jQuery("#signin_username").val();
			var password = jQuery("#signin_password").val();
			
			$.post("<?php echo $this->basepath; ?>user/guest/login", {username: username, password: password}, function(data){//alert('logmein! ' + username + ', ' + password + ', data: ' + data);return;
				if(data == "NOAUTH" || data == "0" || data == ""){
					alert("Data yang Anda masukkan salah: " + data);
					jQuery('.loading_div').hide();
					return false;
				} else {
					jQuery('.loading_div').hide();
					window.location.replace("<?php echo $this->basepath; ?>myavatar");
				}
	
			});
		}

		jQuery("#signin_username, #signin_password").live('keydown', function(e){
			if(e.keyCode == 13){
				logMeIn();
			}
		});

		jQuery("#login_form_submit_button").live('click', function(){
			logMeIn();
		});
		
		jQuery("#login_form").submit(function(){
			return false;
		});
		
		jQuery('#fb_logout').live('click', function(){
			$('.loading_div').show();
			if(!confirm('Are you sure to logout?')){
				$('.loading_div').hide();
				return;
			}
			$.post("<?php print($this->basepath); ?>user/user/logout", {}, function(data){
				if(data == "1"){
					$('.loading_div').hide();
					window.location.replace("<?php echo $this->basepath; ?>");
				} else {
					$('.loading_div').hide();
					alert('Logout process failed.');
				}
				
				window.location.href = "<?php echo $this->basepath; ?>";
				return false;
			});

		});

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


<style>
	input[type="text"], input[type="password"]{
		border: 1px solid #fff;

	-moz-border-radius: 4px;
	-khtml-border-radius: 4px;
	-webkit-border-radius: 4px;
	border-radius: 4px;

	}
	input[type="text"]:focus, input[type="password"]:focus{
		border: 1px solid #737373;
		-moz-box-shadow:    0 0 15px #737373;
		-webkit-box-shadow: 0 0 15px #737373;
		box-shadow:         0 0 15px #737373;

	}
	
	#signin_btn{
		background: url(<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/landing/button_sign_in_normal.png) no-repeat center transparent;
	}
	
	#btn_join{
		background: url(<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/landing/button_sign_up_normal.png) no-repeat center transparent;
	}
</style>


<script type="text/javascript">
	jQuery(document).ready(function(){
		// jquery live hover
		jQuery('#signin_btn').live({
			mouseenter: function(){
				jQuery(this).css('background', 'url(<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/landing/button_sign_in_hover.png) no-repeat center transparent');
			},
			mouseleave: function(){
				jQuery(this).css('background', 'url(<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/landing/button_sign_in_normal.png) no-repeat center transparent');
			},
			mousedown: function(){
				jQuery(this).css('background', 'url(<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/landing/button_sign_in_active.png) no-repeat center transparent');
			},
			mouseup: function(){
				jQuery(this).css('background', 'url(<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/landing/button_sign_in_hover.png) no-repeat center transparent');
			}
		});
		jQuery('#btn_join').live({
			mouseenter: function(){
				jQuery(this).css('background', 'url(<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/landing/button_sign_up_hover.png) no-repeat center transparent');
			},
			mouseleave: function(){
				jQuery(this).css('background', 'url(<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/landing/button_sign_up_normal.png) no-repeat center transparent');
			},
			mousedown: function(){
				jQuery(this).css('background', 'url(<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/landing/button_sign_up_active.png) no-repeat center transparent');
			},
			mouseup: function(){
				jQuery(this).css('background', 'url(<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/landing/button_sign_up_hover.png) no-repeat center transparent');
			}
		});
	});
</script>


<div style="width:100%; min-height:600px; background:url('<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/bg/landing.bg.jpg') top center no-repeat;">
  <div class="container_12" style=" min-height:580px;">
    <div class="grid_8" style="min-height:580px;">
			<div style="height: 31px;">&nbsp;</div>
			<div style="height: 420px; width: 419px; float: left;  background:url('<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/bg/popbloop_logo.png') center right no-repeat;">&nbsp;</div>
			<div style="height: 420px; width: 121px; float: left; margin-left: 15px;">
				<div style="height: 191px;">&nbsp;</div>
				<a href="<?php print($this->basepath); ?>whatis"><div id="whatis_image" class="whatis_normal" style="height: 38px; width: 96px; float: left;"></div></a>
			</div>
    </div>
    <div class="grid_4">
    
      <div style="margin:130px 0 0 0; text-align:left;">
        <div style="width: 260px; height:12px; padding: 2px 40px 5px 0px; margin-left:4px; color: #fff; font-size: 12px; text-align: left;">
					Already have an account? Sign In
        </div>
        <div style="width: 260px; height:24px; padding: 2px 40px 3px 0px; margin-left:4px;">
          <input type="text" name="signin_username" id="signin_username" placeholder="Email" style="width:239px; padding-left:15px; height: 22px; color:#666;">
        </div>
        <div style="width: 260px; height:24px; padding: 2px 40px 3px 0px;">
          <div style="width:166px; float:left; margin-left:4px;">
            <input type="password" name="signin_password" id="signin_password" placeholder="Password" style="width:146px; padding-left:15px; height: 22px; color:#666;">
          </div>
					
					<div id="signin_btn" style="width: 90px; height: 26px; float: left; cursor: pointer;"></div>
					<?php /* 
          <div id="signin_btn" style="width:90px; float:left; cursor:pointer; background:url('<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/bg/green.btn.png') top center no-repeat #6e9407; color:#fff; height:22px; margin:1px 0; padding-top:2px; display:table-cell; text-align:center; vertical-align:middle; font-size:14px;">
            Sign In
          </div>
          */ ?>
        </div>
        
        <div class="clear"></div>

        <div style="width: 260px; height:24px; padding: 2px 40px 3px 0px; color: #fff; font-size:10px;">
          <div style="float:left; width:25px; height:20px; text-align:left;">
            <input type="checkbox" id="remember_me" name="remember_me" style="border:0;">
          </div>
          <div style="float:left; width:85px; height:20px; padding-top:3px;">
            <label for="remember_me" style="margin-bottom:6px;">Remember me</label>
          </div>
          <div style="float:left; width:85px; height:20px; padding-top:3px;">
            Forgot password
          </div>
        </div>

        <div class="clear"></div>
				

        <form method="post" id="signupform" action="<?php echo $this->basepath; ?>ui/guest/signupform">
        
        <div style="width: 260px; height:12px; padding: 15px 40px 5px 0px; margin-left:4px; color: #fff; font-size: 12px; text-align: left;">
					New to Popbloop? Sign Up
        </div>
				<?php /* ?>
        <div style="width: 260px; height:24px; padding: 2px 40px 3px 0px; margin-left:4px;">
          <input type="text" name="signup_fullname" id="signup_fullname" placeholder="Fullname" style="width:239px; padding-left:15px; height: 22px; color:#666;">
        </div>
				<?php */ ?>
        <div style="width: 260px; height:24px; padding: 2px 40px 3px 0px; margin-left:4px;">
          <input type="text" name="signup_email" id="signup_email" placeholder="Email" style="width:239px; padding-left:15px; height: 22px; color:#666;">
        </div>
        <div style="width: 260px; height:24px; padding: 2px 40px 3px 0px;">
          <div style="width:166px; float:left; margin-left:4px;">
            <input type="password" name="signup_password" id="signup_password" placeholder="Password" style="width:146px; padding-left:15px; height: 22px; color:#666;">
          </div>
					<div id="btn_join" style="width: 90px; height: 26px; float: left; cursor: pointer;"></div>
					<?php /* 
          <div id="btn_join" style="width:90px; float:left; cursor:pointer; background:url('<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/bg/blue.btn.png') top center no-repeat #6e9407; color:#fff; height:22px; margin:1px 0; padding-top:4px; display:table-cell; text-align:center; vertical-align:middle; font-size:12px;">
            Join PopBloop
          </div>
					*/ ?>
        </div>
        
        </form>
				<?php
				/*
					$config = array();
					$config['appId'] = '353789864649141';
					$config['secret'] = '9e066419bed7d9ff07f4475f26318aa8';
					$config['fileUpload'] = false; // optional
				
					$facebook = new Facebook($config);
					$me = null;
					try{
						$me = $facebook->api('/me');
					} catch (Exception $e){}
//					die("<pre>" . print_r($me, true) . "</pre>");
				*/
				?>
        <?php // if(!isset($_SESSION['fb_me'])){ ?>
        <?php /*if(!isset($me)){*/ ?>
				<div style="width: 260px; height: 24px; padding: 12px 40px 3px 5px; color: #fff; font-size: 10px;">
					<div style="display: table;">
						<!--fb:login-button size="large" registration-url="<?php echo $this->basepath; ?>ui/guest/signupform_facebook" onlogin="require('./log').info('onlogin callback')">Connect with Facebook</fb:login-button-->
						<!--div class="fb-login-button" data-show-faces="false" data-width="100" data-max-rows="1"></div-->
						<a href="<?php echo $_SESSION['fb_loginUrl']; ?>"><img src="<?php echo $this->basepath; ?>images/icons/facebook.connect.png" /></a>
						<?php /* ?>
						<a href="<?php echo $facebook->getLoginUrl(); ?>">Login with Facebook</a>
						<?php */ ?>
					</div>
				</div>
        <?php /*} else { ?>
				<div style="width: 260px; height: 24px; padding: 12px 40px 3px 5px; color: #fff; font-size: 10px;">
					<div style="display: table;">
						<!--fb:login-button size="large" registration-url="<?php echo $this->basepath; ?>ui/guest/signupform_facebook" onlogin="require('./log').info('onlogin callback')">Connect with Facebook</fb:login-button-->
						<!--div class="fb-login-button" data-show-faces="false" data-width="100" data-max-rows="1"></div-->
						<a id="fb_logout" href="<?php echo $_SESSION['fb_logoutUrl']; ?>">Logout from Facebook</a>
					</div>
				</div>
        <?php }*/ ?>
      </div>
      
    
    </div>
    <div class="clear"></div>
    <div class="grid_12">
      <hr style="height:2px; color:#999; background-color:#999;">
    </div>
  </div>
</div>


<div class="image_loader" style="display: none;">
	<img src="<?php print($this->basepath); ?>modules/011_articles/images_popbloopdark/whatis/button_what_is_hover.png" />
	<img src="<?php print($this->basepath); ?>modules/011_articles/images_popbloopdark/whatis/button_what_is_click.png" />
</div>

</div><!--[ end withjs ]-->
