<?php
//$this->level_detail;
?>

<link href="<?php echo $this->basepath ?>libraries/js/valums-file-uploader/client/fileuploader.css" rel="stylesheet" type="text/css" /> 
<script src="<?php echo $this->basepath ?>libraries/js/valums-file-uploader/client/fileuploader.js" type="text/javascript" ></script>


<script language="javascript">
$(document).ready(function(){

	var uploader_skybox = new qq.FileUploader({
		// pass the dom node (ex. $(selector)[0] for jQuery users)
		element: document.getElementById('file-uploader'),
		// path to server-side upload script
		action: '<?php echo $this->basepath; ?>asset/admin/level_update_file',
    allowedExtensions: ['unity3d'],
		params: {
			lilo_id: '<?php echo $this->level_detail['lilo_id']; ?>',
			file_type: 'skybox'
		},

	}); 
	
	var uploader_audio = new qq.FileUploader({
		// pass the dom node (ex. $(selector)[0] for jQuery users)
		element: document.getElementById('audio-file-uploader'),
		// path to server-side upload script
		action: '<?php echo $this->basepath; ?>asset/admin/level_update_file',
    allowedExtensions: ['ogg', 'mp3', 'wav'],
		params: {
			lilo_id: '<?php echo $this->level_detail['lilo_id']; ?>',
			file_type: 'audio'
		},

	})


	$('.editskybox').click(function(){
		var html_ = $.trim($(this).html());
		if(html_ == 'Cancel'){
			$('.updateskybox').hide();
			$('.skyboxoriginalname').show();
			$('.editskybox').html('Edit');
		} else if(html_ == 'Edit'){
			$('.skyboxoriginalname').hide();
			$('.updateskybox').show();
			$('.editskybox').html('Cancel');
		}
		
	});
	
	$('.editaudio').click(function(){
		var html_ = $.trim($(this).html());
		if(html_ == 'Cancel'){
			$('.updateaudio').hide();
			$('.audiooriginalname').show();
			$('.editaudio').html('Edit');
		} else if(html_ == 'Edit'){
			$('.audiooriginalname').hide();
			$('.updateaudio').show();
			$('.editaudio').html('Cancel');
		}
		
	});
	$("#edit_brand").val('<?php echo $this->level_detail['brand']; ?>');
	
});
</script>

<form method="post" enctype="multipart/form-data" action="<?php echo $this->basepath; ?>asset/admin/level_update">

<input type="hidden" name="lilo_id" id="lilo_id" value="<?php echo $this->level_detail['lilo_id']; ?>" />
<table class="input_form" style="width:95%">
  <tr>
  	<td colspan="3">Name</td>
    <td colspan="3"><input type="text" style="width:90%" name="level_detail_name" id="level_detail_name" value="<?php echo $this->level_detail['name']; ?>" /></td>
  </tr>
  <tr>
          <td colspan="3">Brand Name</td>
          <td colspan="3">
            	<select name="edit_brand" id="edit_brand">
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
  	<td colspan="3">Tags</td>
    <td colspan="3"><?php echo $this->level_detail['tags']; ?></td>
  </tr>
  
  <tr>
    <td colspan="3">Server Address</td>
    <td colspan="3">
      <input type="text" size="15" name="server_ip" id="server_ip"  value="<?php echo $this->level_detail['server_ip']; ?>" />
      &nbsp;:&nbsp;
      <input type="text" size="5" name="server_port" id="server_port"  value="<?php echo $this->level_detail['server_port']; ?>" />
    </td>
  </tr>

  <tr>
    <td colspan="3">Number of Channels</td>
    <td colspan="3"><input type="text" size="4" name="channel_number" id="channel_number"  value="<?php echo $this->level_detail['channel_number']; ?>" /></td>
  </tr>

  <tr>
    <td colspan="3">Max CCU per Channel</td>
    <td colspan="3">
    	<input type="text" size="4" name="max_ccu_per_channel" id="max_ccu_per_channel"  value="<?php echo $this->level_detail['max_ccu_per_channel']; ?>" />
    </td>
  </tr>

  <tr>
    <td colspan="3">World Size</td>
    <td colspan="3">
      <input type="text" size="4" name="world_size_x" id="world_size_x"  value="<?php echo $this->level_detail['world_size_x']; ?>" />
      &nbsp;
      <input type="text" size="4" name="world_size_y" id="world_size_y"  value="<?php echo $this->level_detail['world_size_y']; ?>" />  
    </td>
  </tr>

  <tr>
    <td colspan="3">Interest Area</td>
    <td colspan="3">
      <input type="text" size="4" name="interest_area_x" id="interest_area_x"  value="<?php echo $this->level_detail['interest_area_x']; ?>" />
      &nbsp;
      <input type="text" size="4" name="interest_area_y" id="interest_area_y"  value="<?php echo $this->level_detail['interest_area_y']; ?>" />  
    </td>
  </tr>

	<tr>
  	<td colspan="3">Audio</td>
    <td colspan="2">
    	<span class="audiooriginalname">
				<a href="<?php echo $this->basepath . $this->level_detail['audio_file']?>"><?php echo $this->level_detail['audio_file_originalname']?></a>
      </span>
      <div class="updateaudio" style="display:none">
        <span id="audio-file-uploader">       
          <noscript>          
          <p>Please enable JavaScript to use file uploader.</p>
          <!-- or put a simple form for upload here -->
          </noscript>         
        </span>

      </div>
    </td>
    <td colspan="1" style="text-align:center"><a class="editaudio">Edit</a></td>
  </tr>

  <tr>
    <td colspan="3">Skybox</td>
    <td colspan="2">
    	<span class="skyboxoriginalname">
				<a href="<?php echo $this->basepath . $this->level_detail['skybox_file']?>"><?php echo $this->level_detail['skybox_file_originalname']?></a>
      </span>
      <div class="updateskybox" style="display:none">
        <span id="file-uploader">       
          <noscript>          
          <p>Please enable JavaScript to use file uploader.</p>
          <!-- or put a simple form for upload here -->
          </noscript>         
        </span>

      </div>
    </td>
    <td colspan="1" style="text-align:center"><a class="editskybox">Edit</a></td>
  </tr>
  
  <tr>
  	<td colspan="6">Assets</td>
  </tr>
  <tr>
  	<td>Index</td>
  	<td>Object Name</td>
  	<td colspan="2">Position</td>
  	<td>Rotation</td>
  	<td>Tag</td>
  </tr>
  
	<?php
  foreach($this->level_detail['assets'] as $key => $val){
  ?>

  <tr>
  	<td style="width:10px;"><?php echo $key; ?></td>
  	<td><?php echo $val['objectName']; ?></td>
    <td colspan="2"><?php echo $val['position']; ?></td>
    <td><?php echo $val['rotation']; ?></td>
    <td><?php echo $val['tag']; ?></td>
  </tr>
  
  <?php
  }
  ?>
  
  <tr>
  	<td colspan="6">Render Settings</td>
  </tr>
  <tr>
  	<td colspan="2">fogActive</td>
  	<td><input type="text" size="4" name="fogActive" id="fogActive"  value="<?php echo $this->level_detail['fogActive']; ?>" /></td>
  	<td colspan="2">fogColor</td>
  	<td><input type="text" size="8" name="fogColor" id="fogColor"  value="<?php echo $this->level_detail['fogColor']; ?>" /></td>
  </tr>
  <tr>
  	<td colspan="2">fogDensity</td>
  	<td><input type="text" size="4" name="fogDensity" id="fogDensity"  value="<?php echo $this->level_detail['fogDensity']; ?>" /></td>
  	<td colspan="2">fogStartDistance</td>
  	<td><input type="text" size="8" name="fogStartDistance" id="fogStartDistance"  value="<?php echo $this->level_detail['fogStartDistance']; ?>" /></td>
  </tr>
  <tr>
  	<td colspan="2">fogEndDistance</td>
  	<td><input type="text" size="4" name="fogEndDistance" id="fogEndDistance"  value="<?php echo $this->level_detail['fogEndDistance']; ?>" /></td>
  	<td colspan="2">fogMode</td>
  	<td><input type="text" size="8" name="fogMode" id="fogMode"  value="<?php echo $this->level_detail['fogMode']; ?>" /></td>
  </tr>
  
  <tr>
  	<td colspan="6">Light Maps</td>
  </tr>

  <tr>
  	<td>Index</td>
  	<td>Near</td>
  	<td>Far</td>
  	<td>Index</td>
  	<td>Near</td>
  	<td>Far</td>
  </tr>
  
	<?php
  for($idx = 0; $idx < $this->level_detail['lightmaps']['lightmapsCount']; $idx = $idx + 2){
	?>
  <tr>
  	<td><?php echo $idx; ?></td>
  	<td><?php echo str_replace($this->level_detail['directory'] . '/', '', $this->level_detail['lightmaps']['near_' . $idx]); ?></td>
  	<td><?php echo str_replace($this->level_detail['directory'] . '/', '', $this->level_detail['lightmaps']['far_' . $idx]); ?></td>
    <?php $idx_ = $idx + 1; ?>
  	<td><?php echo $idx_; ?></td>
  	<td><?php echo str_replace($this->level_detail['directory'] . '/', '', $this->level_detail['lightmaps']['near_' . $idx_]); ?></td>
  	<td><?php echo str_replace($this->level_detail['directory'] . '/', '', $this->level_detail['lightmaps']['far_' . $idx_]); ?></td>
  </tr>
  <?php
  }
  ?>

  <tr>
  	<td colspan="2">Directory</td>
  	<td colspan="4"><?php echo $this->level_detail['directory']; ?></td>
  </tr>
  
</table>

</form>