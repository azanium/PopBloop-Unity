<div class="withjs">

<div class="container_12 pop_20_spacer">
</div>

<script src="<?php echo $this->basepath; ?>libraries/js/jquery_placeholder.js"></script>
<script type="text/javascript" src="<?php echo $this->basepath; ?>libraries/jquery_upload_crop/js/jquery.imgareaselect-0.3.min.js"></script>

<?php
	// dapatkan ukuran profile image
	$image_size = getimagesize($this->basepath . "user_generated_data/profile_picture/" . $this->property_data['profile_picture']);
	$image_width = $image_size[0];
	$image_height = $image_size[1];
?>

<script language="javascript">
	$(document).ready(function(){

		// $('#crop_div').hide();


		// testing mulai
		$('#properties_x_fullname').keydown(function(event){
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


		$('input[placeholder],textarea[placeholder]').placeholder();
		$('#properties_x_birthday').datepicker({ dateFormat: 'dd-mm-yy' });
		
		$('#login_form').submit(function(){
			var password = $('#account_x_password').val();
			var confirm_password = $('#confirm_password').val();
			if(password != confirm_password){
				alert("The password you entered did not match!");
				return false;
			}
		});

	});
</script>
<form method="post" id="login_form" enctype="multipart/form-data" action="<?php echo $this->basepath; ?>user/user/properties_update">

<div class="container_12">
  <div class="grid_4">
		<?php
    if(trim($this->property_data['profile_picture']) != ''){
    ?>
      <img style="max-height:240px; max-width:240px;" src="<?php echo $this->basepath; ?>user_generated_data/profile_picture/<?php echo $this->property_data['profile_picture']; ?>" />
      <br />
      <?php /* ?>
      [<a id="crop_image">Crop Image</a>]
      <?php */ ?>
    <?php
    }
    ?>
    <input type="file" class="" id="properties_x_profile_picture" name="properties_x_profile_picture" accept="image/*" />
  </div>
  <div class="grid_8">
  	<div class="grid_1 alpha">Username</div>
  	<div class="grid_3"><strong><?php echo $this->account_data['username']; ?></strong></div>
    <div class="grid_1">&nbsp;</div>
    <div class="grid_3 omega">&nbsp;</div>
    
		<div class="grid_8 pop_20_spacer"></div>

  	<div class="grid_1 alpha">Full Name</div>
  	<div class="grid_3"><input type="text" class="" id="properties_x_fullname" name="properties_x_fullname" placeholder="Full Name" value="<?php echo $this->property_data['fullname']; ?>" maxlength="40" /></div>
  	<div class="grid_1">Email</div>
  	<div class="grid_3 omega"><input type="text" class="" id="account_x_email" name="account_x_email" placeholder="Email" value="<?php echo $this->account_data['email']; ?>" /></div>

		<div class="grid_8 pop_20_spacer"></div>

  	<div class="grid_1 alpha">Birthday</div>
  	<div class="grid_3"><input type="text" class="" id="properties_x_birthday" name="properties_x_birthday" placeholder="01-01-2001" value="<?php echo $this->property_data['birthday']; ?>" /></div>
  	<div class="grid_1">Sex</div>
  	<div class="grid_3 omega">
      <select name="properties_x_sex" id="properties_x_sex" class="">
        <option value="male" <?php if(strtolower($this->property_data['sex']) == 'male'){ ?>selected="selected"<?php } ?>>Male</option>
        <option value="female" <?php if(strtolower($this->property_data['sex']) == 'female'){ ?>selected="selected"<?php } ?>>Female</option>
      </select>
    </div>

		<div class="grid_8 pop_20_spacer"></div>
    
  	<div class="grid_1 alpha">Describe Yourself</div>
  	<div class="grid_7 omega"><textarea maxlength="200" style="width:540px; max-width:540px; max-height:30px; height:30px;" name="properties_x_description" id="properties_x_description" placeholder=""><?php echo $this->property_data['description']; ?></textarea></div>

		<div class="grid_8 pop_20_spacer"></div>

  	<div class="grid_1 alpha">Password *</div>
  	<div class="grid_3"><input type="password" class="" id="account_x_password" name="account_x_password" placeholder="Password" value="" /></div>
  	<div class="grid_1">Confirm *</div>
  	<div class="grid_3 omega"><input type="password" class="" id="confirm_password" name="confirm_password" placeholder="Password" value="" /></div>

		<div class="grid_8 pop_20_spacer"></div>
    
		<div class="grid_8 alpha">
	    * Left these fields empty if you don't want to change your password.
    </div>
    
		<div class="grid_8 pop_20_spacer"></div>

    <?php //print_r($this->account_data); ?>
    <?php //print_r($this->property_data['profile_picture']); ?>

	</div>
</div>

<div class="container_12">
	<div class="grid_4" style="text-align:center">
  &nbsp;
	</div>
	<div class="grid_8" style="text-align:left">
		<input class="" type="submit" value="Save" style="width:120px;" />&nbsp;&nbsp;<input class="" type="reset" value="Reset" style="width:120px;" />
	</div>
  <div class="clear"></div>
</div>

</form>


</div><!--[ end withjs ]-->