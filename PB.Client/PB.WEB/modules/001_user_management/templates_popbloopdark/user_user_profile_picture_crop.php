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

		$('#crop_dialog').dialog({
			autoOpen: false, 
			minWidth: <?php echo $image_width + 300; ?>/*540*/, 
			minHeight:  <?php echo $image_height + 30; ?>/*200*/
		});

		$('#crop_image').live('click', function(){
			// $('#crop_dialog').html('<iframe src="http://localhost/lilo.beta/play" frameborder="0" width="500px" height="400px"></iframe>');
			$('#crop_dialog').dialog('open');
			// $('#crop_div').show('slow');
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
      [<a id="crop_image" href="<?php echo $this->basepath; ?>user/user/profile_picture_crop">Crop Image</a>]
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



<?php
$thumb_width = 100;
$thumb_height = 100;

//Only display the javacript if an image has been uploaded
if(strlen($this->property_data['profile_picture'])>0){
	$current_large_image_width = $image_width;
	$current_large_image_height = $image_height;?>
<script type="text/javascript">
function preview(img, selection) { 
	var scaleX = <?php echo $thumb_width;?> / selection.width; 
	var scaleY = <?php echo $thumb_height;?> / selection.height; 
//	alert("ScaleXY: " + scaleX + ", " + scaleY);
	$('#thumbnail_preview').css({ 
		width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px', 
		height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
		marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
		marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
	});
	$('#x1').val(selection.x1);
	$('#y1').val(selection.y1);
	$('#x2').val(selection.x2);
	$('#y2').val(selection.y2);
	$('#w').val(selection.width);
	$('#h').val(selection.height);
} 

$(document).ready(function () { 
	$('#save_thumb').click(function() {
		var x1 = $('#x1').val();
		var y1 = $('#y1').val();
		var x2 = $('#x2').val();
		var y2 = $('#y2').val();
		var w = $('#w').val();
		var h = $('#h').val();
		if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
			alert("You must make a selection first");
			return false;
		}else{
			alert("value: " + x1 + ", " + y1 + ", " + x2 + ", " + y2 + ", " + w + ", " + h);
			return true;
		}
	});
}); 

$(window).load(function () { 
	$('#thumbnail').imgAreaSelect({ aspectRatio: '1:1', onSelectChange: preview }); 
});

</script>
<?php }?>


<?php /* ?>
<div style="width: auto; min-height: <?php echo $image_height + 30; ?>px; height: auto; min-width:<?php echo $image_width + 300; ?>px; " class="ui-dialog-content ui-widget-content" id="crop_dialog"	title="Crop Your Profile Image">
<?php */ ?>
<div class="container_12">
	<div class="grid_12">

  <form name="thumbnail" action="<?php echo $this->basepath; ?>user/user/profile_thumbnail" method="post">
	<table style="width:95%; border:1; text-align:center">
  	<tr>
    	<td style="width:200px;">&nbsp;
        <div style="float:left; position:relative; overflow:hidden; width:<?php echo $thumb_width;?>px; height:<?php echo $thumb_height;?>px;">
          <img id="thumbnail_preview" src="<?php echo $this->basepath; ?>user_generated_data/profile_picture/<?php echo $this->property_data['profile_picture']; ?>" style="position: relative;" alt="Thumbnail Preview" />
        </div>
        <input type="submit" name="upload_thumbnail" value="Save Thumbnail" id="save_thumb" />
      </td>
    	<td>
        <input type="hidden" name="x1" value="" id="x1" />
        <input type="hidden" name="y1" value="" id="y1" />
        <input type="hidden" name="x2" value="" id="x2" />
        <input type="hidden" name="y2" value="" id="y2" />
        <input type="hidden" name="w" value="" id="w" />
        <input type="hidden" name="h" value="" id="h" />

			<img src="<?php echo $this->basepath; ?>user_generated_data/profile_picture/<?php echo $this->property_data['profile_picture']; ?>" style="float: left; margin-right: 10px;" id="thumbnail" alt="Create Thumbnail" />

      </td>
    </tr>
  </table>
  </form>
  </div>
</div>

</div><!--[ end withjs ]-->