<script language="javascript">
	$(document).ready(function(){
//		$('.tinyscrollbar_').tinyscrollbar();
		// Tabs
		$('#tabs').tabs();
		
		$("#dialogChannel").dialog({
			autoOpen: false, 
			minWidth: 820, 
			minHeight: 400,

			buttons: [
				{
					text: "Save",
					click: function() { 
						alert('save...'); 
						var level_detail_name			= $("#level_detail_name").val();
						
						var lilo_id 							= $("#lilo_id").val();
						var server_ip 						= $("#server_ip").val();
						var server_port 					= $("#server_port").val();
						var channel_number 				= $("#channel_number").val();
						var max_ccu_per_channel 	= $("#max_ccu_per_channel").val();
						var world_size_x 					= $("#world_size_x").val();
						var world_size_y 					= $("#world_size_y").val();
						var interest_area_x 			= $("#interest_area_x").val();
						var interest_area_y 			= $("#interest_area_y").val();
						
						alert(lilo_id);
						
						$.post("<?php echo $this->basepath; ?>asset/admin/level_update", 
									 	{lilo_id: lilo_id, server_ip: server_ip, server_port: server_port, channel_number: channel_number, max_ccu_per_channel: max_ccu_per_channel, world_size_x: world_size_x, world_size_y: world_size_y, interest_area_x: interest_area_x, interest_area_y: interest_area_y, level_detail_name: level_detail_name}, 
									 	function(data){
											if(data == "1"){
												alert("Update data berhasil.");
												window.location.replace("<?php echo $this->basepath; ?>asset/admin/level");
											}
										}
						);
						
					}
				},
				
				{
					text: "Cancel",
					click: function() { $(this).dialog("close"); }
				},
				
			]

		});
		
		$("#dialog").dialog({
			autoOpen: false, 
			minWidth: 820, 
			minHeight: 400,

			buttons: [
				{
					text: "Save",
					click: function() { 
						alert('save...'); 
						var level_detail_name			= $("#level_detail_name").val();
						
						var lilo_id 							= $("#lilo_id").val();
						var server_ip 						= $("#server_ip").val();
						var server_port 					= $("#server_port").val();
						var channel_number 				= $("#channel_number").val();
						var max_ccu_per_channel 	= $("#max_ccu_per_channel").val();
						var world_size_x 					= $("#world_size_x").val();
						var world_size_y 					= $("#world_size_y").val();
						var interest_area_x 			= $("#interest_area_x").val();
						var interest_area_y 			= $("#interest_area_y").val();
						var brand 			= $("#edit_brand").val();
						alert(lilo_id);
						
						$.post("<?php echo $this->basepath; ?>asset/admin/level_update", 
									 	{lilo_id: lilo_id, server_ip: server_ip, edit_brand:brand,server_port: server_port, channel_number: channel_number, max_ccu_per_channel: max_ccu_per_channel, world_size_x: world_size_x, world_size_y: world_size_y, interest_area_x: interest_area_x, interest_area_y: interest_area_y, level_detail_name: level_detail_name}, 
									 	function(data){
											if(data == "1"){
												alert("Update data berhasil.");
												window.location.replace("<?php echo $this->basepath; ?>asset/admin/level");
											}
										}
						);
						
					}
				},
				
				{
					text: "Cancel",
					click: function() { $(this).dialog("close"); }
				},
				
			]

		});

		$(".level_detail").click(function(){
			var _id = $(this).attr('id');
			var _id_split = _id.split('_');
			var level_id = _id_split[2];
			
			$.post("<?php echo $this->basepath; ?>asset/admin/level_detail/" + level_id, {}, function(data){
				$('#dialog').html(data);
				$('#dialog').dialog('open');
				return false;
			});
			
		});
		
		$(".channel_detail").click(function(){
			var _id = $(this).attr('id');
			var _id_split = _id.split('_');
			var level_id = _id_split[2];

			// http://localhost/lilo3a/asset/admin/channel_list/4e48c240c1b4ba7c09000000
			$.post("<?php echo $this->basepath; ?>asset/admin/channel_editor/" + level_id, {}, function(data){
				$('#dialogChannel').html(data);
				$('#dialogChannel').dialog('open');
				return false;
			});
			

//			$('#dialogChannel').html("Ahhhahiiii " + level_id);
//			$('#dialogChannel').dialog('open');
		});
		
		$(".level_delete").click(function(){
			if(confirm('Anda yakin untuk menghapus level ini?')){
				// dapatkan id
				var _id = $(this).attr('id');
	//			alert(_id);
				var _id_split = _id.split('_');
				var level_id = _id_split[2];
				$.post("<?php echo $this->basepath; ?>asset/admin/level_delete/" + level_id, {}, function(data){
					if(data == '1'){
						window.location.replace("<?php echo $this->basepath; ?>asset/admin/level");
					} else {
						alert(data);
					}
				});
			}
		});
	});
</script>
<table class="input_form" style="width:100%">
        <tr>
          <th style="width:30px;">No</th>
          <th>Name</th>
          <th>Preview</th>
          <th>Brand</th>
          <th>Download</th>
          <th>Operation</th>
        </tr>
      
      <?php
      $level_array = $this->listdata;
      $no = 0;
      foreach($level_array as $curr)
      {      
        $no++;
      ?>      
        <tr>
          <td style="width:30px;"><?php echo $no; ?></td>
          <td><?php echo $curr['name']; ?></td>
          <td style="text-align:center"><?php echo (file_exists($curr['preview_file'])) ? "<img src='" . $this->basepath . $curr['preview_file'] . "' style='max-width:50px; max-height:50px;' >" : ''; ?></td>
          <td>
						<div class="tinyscrollbar_" style="max-height:100px; overflow-y:auto">
              <?php
//                foreach($curr['assets'] as $key => $val){
//                  echo "&bull;&nbsp;" . $key . " - " . $val['objectName'] . "<br />";
//                }
              echo $curr['brand_id'];
              ?>
            </div>
          </td>
          <td style="text-align:left">
            &bull;&nbsp;<a style="text-decoration:none;" href="<?php echo $this->basepath . '/' . $curr['source_file']; ?>">Level Package</a>
            <br />
						<?php echo (file_exists($curr['skybox_file'])) ? "&bull;&nbsp;<a href='" . $this->basepath . $curr['skybox_file'] . "' style='text-decoration:none;' >Skybox</a>" : ''; ?>
          	<br />
            <?php echo (file_exists($curr['audio_file'])) ? "&bull;&nbsp;<a href='" . $this->basepath . $curr['audio_file'] . "' style='text-decoration:none;' >Audio</a>" : ''; ?>
          </td>
          <td style="text-align:center">
            <a class="level_detail" id="level_detail_<?php echo (string)$curr['_id']; ?>">Detail</a>&nbsp;|&nbsp;
            <a class="channel_detail" id="channel_detail_<?php echo (string)$curr['_id']; ?>">Channel</a>&nbsp;|&nbsp;
            <a class="level_delete" id="level_delete_<?php echo (string)$curr['_id']; ?>">Delete</a>
          </td>
        </tr>      
      <?php
      }
      ?>      
      </table>