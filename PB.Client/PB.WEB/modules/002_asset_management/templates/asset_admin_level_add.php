<script src="<?php echo $this->basepath; ?>libraries/js/jquery_placeholder.js"></script>

<script language="javascript">
$(document).ready(function(){
	$('#submit_form').live('click', function(){
		var name = $.trim($("#asset_admin_level_add_name").val());

		$.post("<?php echo $this->basepath; ?>asset/admin/level_exist", {level_name: name}, function(data){
			if(data == 'EMPTYNAME'){
				alert('Nama tidak boleh kosong...');
				return false;
			} else if(data == '1'){	// name exists
				var override_confirm = confirm('Nama ' + name + ' sudah ada di server. Timpa dengan data yang baru?');
				if(!override_confirm){
					return false;
				} else {
					$("#level_add_form").submit();
				}
			} else {
				$("#level_add_form").submit();
			}
		});
	});
	
	
});
</script>
<?php
if($this->ajax == ''){
?>
<div class="centered transbg" style="width:960px; height:540px; border:none;">
	<div style="float:left; width:960px; height:80px;">

  </div>
<?php
}
?>
	<div style="float:left; width:960px;">
  	<?php
    	if(count($_SESSION['pop_status_msg'])){
				// print "<div>".trim($_SESSION['pop_status_msg'])."</div>";
				foreach($_SESSION['pop_status_msg'] as $psm){
					print("<div>$psm</div>");
				}
				unset($_SESSION['pop_status_msg']);
			}
		?>
    <form id="level_add_form" class="input_form" method="post" enctype="multipart/form-data" action="<?php echo $this->basepath; ?>asset/admin/level_add">
      <input type="hidden" name="submitted" value="1" />
      <table style="width:95%">
        <tr>
          <td>Level Name</td>
          <td><input type="text" size="40" name="asset_admin_level_add_name" id="asset_admin_level_add_name" class="light_shadow transparent_70" placeholder="Level Name" /></td>
        </tr>
        <tr>
          <td>Brand Name</td>
          <td>
            	<select name="level_add_brand" id="level_add_brand">
                    <option value=''>&nbsp;</option>
                    <?php
                        foreach($this->brand as $result)
                        {
                          echo "<option value='".$result['brand_id']."'>".$result['name']."</option>";
                        }
                    ?>
                  </select>
            </td>
        </tr>
        <tr>
          <td>File Level</td>
          <td><input type="file" name="asset_admin_level_add_file_level" id="asset_admin_level_add_file_level" class="light_shadow transparent_70" placeholder="File Level" /></td>
        </tr>
        <tr>
          <td>File Skybox</td>
          <td><input type="file" name="asset_admin_level_add_file_skybox" id="asset_admin_level_add_file_skybox" class="light_shadow transparent_70" placeholder="File Skybox" /></td>
        </tr>
        <tr>
          <td>File Audio</td>
          <td><input type="file" name="asset_admin_level_add_file_audio" id="asset_admin_level_add_file_audio" class="light_shadow transparent_70" placeholder="File Audio" /></td>
        </tr>
        <tr>
          <td>Level Preview Image</td>
          <td><input type="file" name="asset_admin_level_add_file_preview_level" id="asset_admin_level_add_file_preview_level" class="light_shadow transparent_70"  placeholder="File Preview Level" /></td>
        </tr>
        <tr>
        	<td>Tags</td>
          <td><input type="text" size="40" name="asset_admin_level_add_tag" id="asset_admin_level_add_tag" class="light_shadow transparent_70" placeholder="Tags" /></td>
        </tr>

        <tr>
        	<td>Server Address</td>
          <td>
          <input type="text" size="15" name="asset_admin_level_add_server_ip" id="asset_admin_level_add_server_ip" class="light_shadow transparent_70" placeholder="IP Address" />
          &nbsp;:&nbsp;
          <input type="text" size="5" name="asset_admin_level_add_server_port" id="asset_admin_level_add_server_port" class="light_shadow transparent_70" placeholder="Port" />
          </td>
        </tr>

        <tr>
        	<td>Number of Channels</td>
          <td><input type="text" size="4" name="asset_admin_level_add_channel_number" id="asset_admin_level_add_channel_number" class="light_shadow transparent_70" placeholder="No" /></td>
        </tr>

        <tr>
        	<td>Max CCU per Channel</td>
          <td><input type="text" size="4" name="asset_admin_level_add_max_ccu_per_channel" id="asset_admin_level_add_max_ccu_per_channel" class="light_shadow transparent_70" placeholder="CCU" /></td>
        </tr>

        <tr>
        	<td>World Size</td>
          <td>
          	<input type="text" size="4" name="asset_admin_level_add_world_size_x" id="asset_admin_level_add_world_size_x" class="light_shadow transparent_70" placeholder="X" />
          	&nbsp;
            <input type="text" size="4" name="asset_admin_level_add_world_size_y" id="asset_admin_level_add_world_size_y" class="light_shadow transparent_70" placeholder="Y" />  
          </td>
        </tr>

        <tr>
        	<td>Interest Area</td>
          <td>
          	<input type="text" size="4" name="asset_admin_level_add_interest_area_x" id="asset_admin_level_add_interest_area_x" class="light_shadow transparent_70" placeholder="X" />
          	&nbsp;
            <input type="text" size="4" name="asset_admin_level_add_interest_area_y" id="asset_admin_level_add_interest_area_y" class="light_shadow transparent_70" placeholder="Y" />  
          </td>
        </tr>

        
        <tr>
          <td colspan="2" style="text-align:center">
            <input type="button" id="submit_form" value="Submit" class="light_shadow transparent_70" />&nbsp;<input type="reset" value="Reset" />
          </td>
        </tr>
        
      </table>
    </form>
  </div>
<?php
if($this->ajax == ''){
?>
</div>
<?php
}
?>
