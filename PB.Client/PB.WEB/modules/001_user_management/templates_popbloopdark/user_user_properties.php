<div class="withjs">

<link href="<?php echo $this->basepath ?>libraries/js/valums-file-uploader/client/fileuploader.css" rel="stylesheet" type="text/css" /> 
<script src="<?php echo $this->basepath ?>libraries/js/valums-file-uploader/client/fileuploader.js" type="text/javascript" ></script>


<div class="container_12 pop_20_spacer">
</div>

<script src="<?php echo $this->basepath; ?>libraries/js/jquery_placeholder.js"></script>
<script type="text/javascript" src="<?php echo $this->basepath; ?>libraries/jquery_upload_crop/js/jquery.imgareaselect-0.3.min.js"></script>

<?php
	// dapatkan ukuran profile image
	//$image_size = getimagesize($this->basepath . "user_generated_data/profile_picture/" . $this->property_data['profile_picture']);
	//$image_width = $image_size[0];
	//$image_height = $image_size[1];
?>

<style>
input[type="file"][name="file"]
{
	width:285px !important;
	height:60px !important;
	cursor: pointer !important;
	font-size: 12px !important;

	background-color: #fff !important;
}

input, textarea, select{
	border: solid 1px #ffffff;
	border-radius: 4px;
}
input:focus, textarea:focus, select:focus{
	border: solid 1px #333333/*#525151*/;
	border-radius: 4px;
	-moz-box-shadow:    0 0 15px #868686;
	-webkit-box-shadow: 0 0 15px #868686;
	box-shadow:         0 0 15px #868686;
}


</style>

<script language="javascript">

	$(document).ready(function(){
		
		//$("input[type='file']").each(function(){
		//	//alert($(this).val());
		//	$(this).css('width', '200px');
		//	$(this).css('height', '200px');
		//});
		
		var uploader_image = new qq.FileUploader({
			// pass the dom node (ex. $(selector)[0] for jQuery users)
			element: document.getElementById('file-uploader'),
			// path to server-side upload script
			action: '<?php echo $this->basepath; ?>user/user/properties_image_upload',
			allowedExtensions: ['jpg', 'jpeg', 'gif', 'png', 'bmp'],
			params: {
				file_type: 'skybox'
			},
			
			template: '<div class="qq-uploader" style="padding: 20px 0 0 0;">' + 
                '<div class="qq-upload-drop-area" style="display:none;"><span>Drop files here to upload</span></div>' +
                '<div class="qq-upload-button" style="background-color:#05aafc; display: table-cell; color: #fff; width:102px; height:32px; border: 0; font-size:11px; padding: 8px 0;">Upload an image</div>' +
                '<ul class="qq-upload-list" style="display:none;"></ul>' + 
             '</div>',
	
			onComplete: function(){
				$('.loading_div').show();
				setTimeout(function(){
					$.post("<?php echo $this->basepath; ?>user/user/get_profile_picture", {}, function(data){
						if($.trim(data) != ''){
							$('#profile_picture, .top_profile_picture').attr('src', "<?php echo $this->basepath; ?>user_generated_data/profile_picture/" + data);
						}
					});
					$('.loading_div').hide();
				}, 10000);
			},
	
		}); 

	
	
		// $('#crop_div').hide();
		
		$('#submit_form').live('click', function(){
			$('#profile_form').submit();
		});


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
		

	});
</script>

<form id="profile_form" method="post" enctype="multipart/form-data" action="<?php echo $this->basepath; ?>user/user/properties_update2">
<div class="container_12">
	<div class="grid_12" style="font-size: 26px; text-align: left; color: #fff; background-color: #333;">
		<div class="grid_12 alpha" style="margin-left: 35px; margin-top: 24px; font-weight: bold;">Account Setting</div>
		<div class="clear"></div>
		
		<div class="grid_12 alpha" style="padding-top: 10px;">
			<div class="grid_3 alpha">&nbsp;</div>
			<div class="grid_6" style="font-size: 12px; color: #fff;">
				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">Email</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 40px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">
						<input readonly="readonly" type="text" style="width: 203px; height: 26px; color: #333; font-size: 12px; padding: 2px 10px;" value="<?php echo $this->account_data['email']; ?>" />
					</div>
				</div>
				<div class="clear"></div>
				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 5px; ">
					<div style="display: table-cell; vertical-align: middle;">&nbsp;</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 5px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 5px; ">
					<div style="display: table-cell; vertical-align: middle;">
						&nbsp;
					</div>
				</div>
				<div class="clear"></div>



				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">Full Name</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 40px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">
						<input name="properties_x_fullname" id="properties_x_fullname" type="text" style="width: 203px; height: 26px; color: #333; font-size: 12px; padding: 2px 10px;" value="<?php echo $this->property_data['fullname']; ?>" />
					</div>
				</div>
				<div class="clear"></div>
				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 20px; ">
					<div style="display: table-cell; vertical-align: middle;">&nbsp;</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 20px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 20px; ">
					<div style="display: table-cell; vertical-align: middle; font-size: 10px;">
						Enter your real name, so people you know can recognize you.
					</div>
				</div>
				<div class="clear"></div>



				<div style="width: 135px; float: left; text-align: right; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">Avatar Name</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 40px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">
						<input name="properties_x_avatarname" id="properties_x_avatarname" type="text" style="width: 203px; height: 26px; color: #333; font-size: 12px; padding: 2px 10px;" value="<?php echo $this->property_data['avatarname']; ?>" />
					</div>
				</div>
				<div class="clear"></div>
				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 20px; ">
					<div style="display: table-cell; vertical-align: middle;">&nbsp;</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 20px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 20px; ">
					<div style="display: table-cell; vertical-align: middle; font-size: 10px;">
						Enter your avatar name.
					</div>
				</div>
				<div class="clear"></div>
				

				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">Gender</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 40px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">
						<select name="properties_x_sex" id="properties_x_sex" style="height: 20px; color: #333; font-size: 12px; width: 120px; padding: 2px 10px;">
							<option value="male" <?php if(strtolower($this->property_data['sex']) == 'male'){ ?>selected="selected"<?php } ?>>Male</option>
							<option value="female" <?php if(strtolower($this->property_data['sex']) == 'female'){ ?>selected="selected"<?php } ?>>Female</option>
						</select>
					</div>
				</div>
				<div class="clear"></div>

				
				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">Birthday</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 40px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">
						<input name="properties_x_birthday_dd" value="<?php echo $this->property_data['birthday_dd']; ?>" id="properties_x_birthday_dd" type="text" maxlength="2" size="2" style="width: 24px; height: 20px; padding: 2px 10px; " />
						<span style="font-size: 24px; color: #999; font-weight: bold; font-family: monospace !important; padding: 0 4px;">/</span>
						<input name="properties_x_birthday_mm" value="<?php echo $this->property_data['birthday_mm']; ?>" id="properties_x_birthday_mm" type="text" maxlength="2" size="2" style="width: 24px; height: 20px; padding: 2px 10px; " />
						<span style="font-size: 24px; color: #999; font-weight: bold; font-family: monospace !important; padding: 0 4px;">/</span>
						<input name="properties_x_birthday_yy" value="<?php echo $this->property_data['birthday_yy']; ?>" id="properties_x_birthday_yy" type="text" maxlength="4" size="4" style="width: 36px; height: 20px; padding: 2px 10px; " />
					</div>
				</div>
				<div class="clear"></div>

				<div style="width: 135px; float: left; text-align: right; display: table; height: 20px; ">
					<div style="display: table-cell; vertical-align: middle;">&nbsp;</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 20px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 20px; ">
					<div style="display: table-cell; vertical-align: middle; font-size: 10px;">
						Input your birthday in dd / mm / yyyy format.
					</div>
				</div>
				<div class="clear"></div>


				
				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">Location</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 40px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">
						<input name="properties_x_location" id="properties_x_location" type="text" style="width: 203px; height: 26px; color: #333; font-size: 12px; padding: 2px 10px;" value="<?php echo $this->property_data['location']; ?>" />
					</div>
				</div>
				<div class="clear"></div>
				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 20px; ">
					<div style="display: table-cell; vertical-align: middle;">&nbsp;</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 20px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 20px; ">
					<div style="display: table-cell; vertical-align: middle; font-size: 10px;">
						Enter your location.
					</div>
				</div>
				<div class="clear"></div>




				
				
				
				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">Twitter</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 40px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">
						<input name="properties_x_twitter" id="properties_x_twitter" type="text" style="width: 203px; height: 26px; color: #333; font-size: 12px; padding: 2px 10px;" value="<?php echo $this->property_data['twitter']; ?>" />
					</div>
				</div>
				<div class="clear"></div>
				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 20px; ">
					<div style="display: table-cell; vertical-align: middle;">&nbsp;</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 20px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 20px; ">
					<div style="display: table-cell; vertical-align: middle; font-size: 10px;">
						You will be mentioned when you win a quest :)
					</div>
				</div>
				<div class="clear"></div>





				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">Handphone</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 40px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">
						<input name="properties_x_handphone" id="properties_x_handphone" type="text" style="width: 203px; height: 26px; color: #333; font-size: 12px; padding: 2px 10px;" value="<?php echo $this->property_data['handphone']; ?>" />
					</div>
				</div>
				<div class="clear"></div>
				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 20px; ">
					<div style="display: table-cell; vertical-align: middle;">&nbsp;</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 20px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 20px; ">
					<div style="display: table-cell; vertical-align: middle; font-size: 10px;">
						We'll contact you when you win a prize.
					</div>
				</div>
				<div class="clear"></div>



				
				


				<div style="width: 135px; float: left; text-align: right; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">About</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 40px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">
						<textarea name="properties_x_description" id="properties_x_description" style="width: 200px; height: 40px; padding: 10px 10px; color: #000; font-size: 11px;"><?php echo trim($this->property_data['description']); ?></textarea>
					</div>
				</div>
				<div class="clear"></div>
				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 20px; ">
					<div style="display: table-cell; vertical-align: middle;">&nbsp;</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 20px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 20px; ">
					<div style="display: table-cell; vertical-align: middle; font-size: 10px;">
						Describe yourself in fewer than 140 characters.
					</div>
				</div>
				<div class="clear"></div>


				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">State of Mind</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 40px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">
						<textarea name="properties_x_state_of_mind" id="properties_x_state_of_mind" style="width: 200px; height: 40px; padding: 10px 10px; color: #000; font-size: 11px;"><?php echo trim($this->property_data['state_of_mind']); ?></textarea>
					</div>
				</div>
				<div class="clear"></div>
				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 20px; ">
					<div style="display: table-cell; vertical-align: middle;">&nbsp;</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 20px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 20px; ">
					<div style="display: table-cell; vertical-align: middle; font-size: 10px;">
						140 characters.
					</div>
				</div>
				<div class="clear"></div>


				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">Profile Picture</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 40px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">
						<div style="width: 110px; height: 110px; float: left;"><img id="profile_picture" style="max-height: 110px; max-width:110px;" src="<?php echo $this->basepath; ?>user_generated_data/profile_picture/<?php echo trim($this->property_data['profile_picture']) != '' ? trim($this->property_data['profile_picture']) : 'default.png'; ?>" /></div>
						<div style="width: 180px; height: 110px; float: left; padding-left: 10px; display: table;">
							<div style="display: table-cell; vertical-align: middle;">
								<div style="" id="file-uploader"></div>
								<div style="font-size: 9px; padding-bottom: 5px; padding-top: 8px;">
									Maximum size of 300kb. JPG, GIF, PNG.<br />
									Need help uploading a profile image?<br />
									<a style="text-decoration: none; color: #05aafc; cursor: pointer;">Learn more.</a><br />
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="clear"></div>
				
				
				<style>
					.btn_link{
						background-color: #525151;
						border-radius: 2px;
						color: #eee;
					}
					.btn_link:hover{
						background-color: #5a5858;
					}
					.btn_link:active{
						background-color: #484747;
					}
				</style>
				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 42px; ">
					<div style="display: table-cell; vertical-align: middle; padding-top: 4px;">Email Setting</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 42px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 42px; ">
					<a href="<?php echo $this->basepath; ?>myprofile-email" style="text-decoration: none; cursor: pointer;">
					<div class="btn_link" style="display: table-cell; vertical-align: middle; height: 32px; width: 174px; float:left; margin-top: 8px; margin-bottom: 2px;">
						<div style="display: table; width: 100%; height: 100%;">
							<div style="display: table-cell; vertical-align: middle; width: 100%; height: 100%; text-align: center;">
								Change Email Setting
							</div>
						</div>
					</div>
					</a>
				</div>
				<div class="clear"></div>
				
				
				
				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 42px; ">
					<div style="display: table-cell; vertical-align: middle;">Change Password</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 42px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 42px; ">
					<a href="<?php echo $this->basepath; ?>myprofile-password" style="text-decoration: none; cursor: pointer;">
					<div class="btn_link" style="display: table-cell; vertical-align: middle; height: 32px; width: 174px; float:left; margin-top: 5px; margin-bottom: 5px;">
						<div style="display: table; width: 100%; height: 100%;">
							<div style="display: table-cell; vertical-align: middle; width: 100%; height: 100%; text-align: center;">
								Change Password
							</div>
						</div>
					</div>
					</a>
				</div>
				<div class="clear"></div>
				
				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 39px; ">
					<div style="display: table-cell; vertical-align: middle;">Delete Account</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 39px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 39px; ">
					<a href="<?php echo $this->basepath; ?>myprofile-deactivate" style="text-decoration: none; cursor: pointer;">
					<div class="btn_link" style="display: table-cell; vertical-align: middle; height: 32px; width: 174px; float:left; margin-top: 2px; margin-bottom: 5px;">
						<div style="display: table; width: 100%; height: 100%;">
							<div style="display: table-cell; vertical-align: middle; width: 100%; height: 100%; text-align: center;">
								Delete Account
							</div>
						</div>
					</div>
					</a>
				</div>
				<div class="clear"></div>
				
				
				
				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 75px; ">
					<div style="display: table-cell; vertical-align: middle;">&nbsp;</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 75px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 50px; margin-top: 25px;">
					<a id="submit_form" style="text-decoration: none; cursor: pointer;">
					<div style="display: table-cell; vertical-align: middle; height: 32px; width: 174px; background-color: #05aafc; float:left; margin-top: 2px; margin-bottom: 5px; color: #000; font-size: 14px; font-weight: bold;">
						<div style="display: table; width: 100%; height: 100%;">
							<div style="display: table-cell; vertical-align: middle; width: 100%; height: 100%; text-align: center;">
								Save Profile
							</div>
						</div>
					</div>
					</a>
				</div>
				<div class="clear"></div>
				
				
			</div>
			<div class="grid_3 omega">&nbsp;</div>
		</div>
		
		
	</div>
	<div class="clear"></div>


	<div class="grid_3">
		
	</div>
	<div class="grid_6">
		
	</div>
	<div class="grid_3">
		
	</div>
	<div class="clear"></div>
	
	
</div>
</form>




</div><!--[ end withjs ]-->