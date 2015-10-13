<div class="withjs">

<script type="text/javascript">
	$(document).ready(function(){
		$('#submit_form').live('click', function(){
			$('#profile_form').submit();
		});

	});
	
</script>

<div class="container_12 pop_20_spacer">
</div>

<form id="profile_form" method="post" enctype="multipart/form-data" action="<?php echo $this->basepath; ?>user/user/properties_update2_password">
<div class="container_12">
	<div class="grid_12" style="font-size: 26px; text-align: left; color: #fff; background-color: #333;">
		<div class="grid_12 alpha" style="margin-left: 35px; margin-top: 24px; font-weight: bold;">Change Password</div>
		<div class="clear"></div>
		
		<div class="grid_12 alpha notification" style="height: 32px; display: none;">
			<div style="background-color: #97b402; width: 870px; height: 32px; margin-left: 35px; margin-right: 35px; margin-top: 10px; color: #333; font-size: 12px; text-align: center; display: table;">
				<div style="display: table-cell; vertical-align: middle">Changes saved</div>
			</div>
		</div>
		
		<div class="grid_12 alpha" style="padding-top: 10px;">
			<div class="grid_3 alpha">&nbsp;</div>
			<div class="grid_6" style="font-size: 12px; color: #fff;">
				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">Current Password</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 40px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">
						<input type="password" name="current_password" id="current_password" style="width: 203px; height: 28px; border-radius: 4px; color: #333; font-size: 12px; border: 0; padding: 2px 10px;" />
					</div>
				</div>
				<div class="clear"></div>

				<div style="width: 135px; float: left; text-align: right; display: table; height: 20px; ">
					<div style="display: table-cell; vertical-align: middle;">&nbsp;</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 20px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 20px; ">
					<div style="display: table-cell; vertical-align: middle; font-size: 10px;">
						Forgot your password?
					</div>
				</div>
				<div class="clear"></div>
				
				
				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">New Password</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 40px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">
						<input type="password" name="new_password" id="new_password" style="width: 203px; height: 28px; border-radius: 4px; color: #333; font-size: 12px; border: 0; padding: 2px 10px;" />
					</div>
				</div>
				<div class="clear"></div>
				
				
				
				<div style="width: 135px; float: left; text-align: right; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">Verify Password</div>
				</div>
				<div style="width: 20px; float: left; text-align: center; height: 40px; ">&nbsp;</div>
				<div style="width: 300px; float: left; text-align: left; display: table; height: 40px; ">
					<div style="display: table-cell; vertical-align: middle;">
						<input type="password" name="new_password_confirm" id="new_password_confirm" style="width: 203px; height: 28px; border-radius: 4px; color: #333; font-size: 12px; border: 0; padding: 2px 10px;" />
					</div>
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
								Save Setting
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