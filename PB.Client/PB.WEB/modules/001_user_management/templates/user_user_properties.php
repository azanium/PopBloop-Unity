<script src="<?php echo $this->basepath; ?>libraries/js/jquery_placeholder.js"></script>
<script language="javascript">
	$(document).ready(function(){
		$('input[placeholder],textarea[placeholder]').placeholder();
		$('#properties_x_birthday').datepicker({ dateFormat: 'dd-mm-yy' });
	});
</script>
<form method="post" id="login_form" enctype="multipart/form-data" action="<?php echo $this->basepath; ?>user/user/properties_update">
<div class="centered transparent_70" style="width:960px; border:none;">
	<div style="float:left; width:960px; height:80px;">

  </div>
	<div style="float:left; width:960px; background-color:#FFF;" class="transparent_70">
    <table style="width:100%; text-align:left;">
      <tr>
        <th>Username</th>
        <td><input type="text" class="light_shadow transparent_70" value="<?php echo $this->account_data['username']; ?>" disabled="disabled" /></td>
        <th>Profile Picture</th>
        <td>
          <?php
          if(trim($this->property_data['profile_picture']) != ''){
          ?>
            <img src="<?php echo $this->basepath; ?>user_generated_data/profile_picture/<?php echo $this->property_data['profile_picture']; ?>" />
            <br />
          <?php
          }
          ?>
          <input type="file" class="light_shadow transparent_70" id="properties_x_profile_picture" name="properties_x_profile_picture" />
        </td>
      </tr>
      <tr>
        <th>Full Name</th>
        <td><input type="text" class="light_shadow transparent_70" id="properties_x_fullname" name="properties_x_fullname" placeholder="Full Name" value="<?php echo $this->property_data['fullname']; ?>" /></td>
        <th>Email</th>
        <td><input type="text" class="light_shadow transparent_70" id="account_x_email" name="account_x_email" placeholder="Email" value="<?php echo $this->account_data['email']; ?>" /></td>
      </tr>
			<tr>
				<th>Birthday</th>
				<td><input type="text" class="light_shadow transparent_70" id="properties_x_birthday" name="properties_x_birthday" placeholder="01-01-2001" value="<?php echo $this->property_data['birthday']; ?>" /></td>
				<th>Sex</th>
				<td>
					<select name="properties_x_sex" id="properties_x_sex" class="light_shadow transparent_70">
						<option value="male" <?php if(strtolower($this->property_data['sex']) == 'male'){ ?>selected="selected"<?php } ?>>Male</option>
						<option value="female" <?php if(strtolower($this->property_data['sex']) == 'female'){ ?>selected="selected"<?php } ?>>Female</option>
					</select>
				</td>
			</tr>
      <tr>
        <td colspan="4" align="center">
          <input class="light_shadow transparent_70" type="submit" value="Save" />&nbsp;<input class="light_shadow transparent_70" type="reset" value="Reset" />
        </td>
      </tr>
    </table>
  </div>

  <?php print_r($this->account_data); ?>
  <?php print_r($this->property_data['profile_picture']); ?>

</div>
</form>
