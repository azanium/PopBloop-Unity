<script src="<?php echo $this->basepath; ?>libraries/js/jquery_placeholder.js"></script>
<script language="javascript">
	$(document).ready(function(){
		$('input[placeholder],textarea[placeholder]').placeholder();
		
		$('#tabs').tabs();
		
		// saat pertama load, search_users_result kosong
		// saat search_people di-klik, baru tampilkan hasilnya
		$('#search_people').live('click', function(){
			var keyword = $("#search_user_keyword").val();
			$.post("<?php echo $this->basepath; ?>friend/user/ws_search/" + keyword, {}, function(data){
				var all_people = eval('('+data+')');
				var text = '';
				for(idx = 0; idx < all_people.length; idx++){
					text = text + '<div id="div_'+ all_people[idx].lilo_id +'" style="width:280px; height:90px; float:left; text-align:left; overflow-x:hidden;">';
					foto = ($.trim(all_people[idx].profile_picture) != '') ? all_people[idx].profile_picture : 'default.png';
					foto_tag = "<a class='edituser' id='edituserimage_" + all_people[idx].lilo_id + "'><img style='max-width:80px; max-height:80px;' src='<?php echo $this->basepath; ?>user_generated_data/profile_picture/"+foto+"' /></a>";
					text = text + '<div style="width:100px; height:90px; float:left; text-align:left; overflow-x:hidden;">'+foto_tag+'</div>';
					text = text + '<div style="width:180px; height:90px; float:left; text-align:left; overflow-x:hidden;">';
					text = text + all_people[idx].username + '<br />';
					text = text + all_people[idx].email + '<br />';
					
					if(all_people[idx].invitation_exists <= 0){
						text = text + "<a class='edituser' id='edituser_" + all_people[idx].lilo_id + "'>Detail...</a>";
					}
					
					text = text + '</div>';
					text = text + '</div>';
				}
				$("#search_users_result").html(text);
			});
		});

		$("#user_detail_dialog").dialog({
			autoOpen: false, 
			minWidth: 720, 
			minHeight: 200,

			buttons: [
				{
					text: "Save Changes",
					click: function() { 
						alert('save...'); 
					}
				},
				
				{
					text: "Delete This User",
					click: function() { 
						if(confirm('Are you sure to delete this user?')){
							// post ke quest/admin/ws_dialogstory/delete/$id
							var selected_user = $('#selected_user').val();
//							alert('Hapus...!! - ' + selected_user);
							
							$.post("<?php echo $this->basepath; ?>user/admin/deletebyid/" + selected_user, {}, function(data){
								if(data == "1"){
									$('#div_' + selected_user).hide("slow");
									$("#user_detail_dialog").dialog('close');
								} else {
									alert('Data deletion failed.');
								}
							});
							
						} else {
//							alert('Ga dihapus...!!');
						}
					}
				},
				
				{
					text: "Cancel",
					click: function() { $(this).dialog("close"); }
				},
				
			]

		});


		$('.edituser').live('click', function(){
			var _id = $(this).attr('id');
			var _id_split = _id.split('_');
			
			var user_id = _id_split[1];
//			alert(user_id);

			$('#selected_user').val(user_id);

//			$('#user_detail_dialog').html('Yuuhuuuu... ' + user_id);
			
			$('#user_detail_dialog').dialog('open');
			
			// dapatkan detail data selected_user, gunakan untuk mengubah value form di dialog user_detail_dialog
			$.post("<?php echo $this->basepath; ?>user/admin/detail/" + user_id, {}, function(data){
//				alert(data);
//				$('#user_detail_dialog').html(data);
				var user_data = eval('('+data+')');

				$('#username').val(user_data.username);
				$('#fullname').val(user_data.fullname);
				$('#email').val(user_data.email);
				
				if($.trim(user_data.profile_picture) != ''){
					$('#profile_picture').attr('src', "<?php echo $this->basepath; ?>user_generated_data/profile_picture/" + user_data.profile_picture);
				} else {
					$('#profile_picture').attr('src', "<?php echo $this->basepath; ?>user_generated_data/profile_picture/default.png");
				}

			});

		});
		
	});
</script>

<!--[
User Detail:
 - basic data
 - social
 - game
]-->
<input type="hidden" name="selected_user" id="selected_user" value="" />
<div style="width: auto; min-height: 58.4px; height: auto; min-width:600px; " class="ui-dialog-content ui-widget-content" id="user_detail_dialog" 
	title="User Detail">
  
  <table style="width:100%; text-align:left">
  	<tr>
    	<td style="width:150px">Username</td>
    	<td><input type="text" disabled="disabled" name="username" id="username" value="" /></td>
    </tr>
  	<tr>
    	<td style="width:150px">Full Name</td>
    	<td><input type="text" disabled="disabled" name="fullname" id="fullname" value="" /></td>
    </tr>
  	<tr>
    	<td style="width:150px">Email</td>
    	<td><input type="text" disabled="disabled" name="email" id="email" value="" /></td>
    </tr>
  	<tr>
    	<td style="width:150px">Profile Picture</td>
    	<td><img id="profile_picture" style="max-height:100px; max-width:100px;" src="<?php echo $this->basepath; ?>user_generated_data/profile_picture/default.png" /></td>
    </tr>
  </table>
</div>

<div class="centered transbg" style="width:960px; border:none;">
  <div id="tabs" style="float:left; width:960px;">
    <ul>
      <li><a href="#tabs-1">User List</a></li>
<!--      <li><a href="#tabs-2">Add New User</a></li>	-->
    </ul>
    <div id="tabs-1">
			<div style="width:100%; text-align:center">
      	<input type="text" class="light_shadow transparent_70" name="search_user_keyword" id="search_user_keyword" title="Search Users..." placeholder="Search Users..." class="light_shadow transparent_70" />
        &nbsp;
        <input type="button" value="Search" id="search_people" style="width:100px;" class="light_shadow transparent_70" />
      </div>
			<div style="width:100%; text-align:center" id="search_users_result">
      <!--[*
				<?php
        $no = 1;
        foreach($this->users_array as $ua){
        ?>
        
        <div style="width:50px; height:80px; float:left; text-align:left; overflow-x:hidden;">Foto</div>
        <div style="width:180px; height:80px; float:left; text-align:left; overflow-x:hidden;">
          <?php print($ua['username']); ?><br  />
          <a id="user_list_edit" href="<?php print($basepath . "user/admin/"); ?>user_edit/<?php print($ua['username']); ?>">Edit</a>
          &nbsp;|&nbsp;
          <a id="user_list_delete" onclick="return confirm('Hapus user <?php print($ua['username']); ?>?')" href="<?php print($basepath . "user/admin/"); ?>user_delete/<?php print($ua['username']); ?>">Delete</a>
        </div>

        <?php
          $no++;
        }
        ?>
      *]-->
        
	    </div>
    </div>
		
		<!--
    <div id="tabs-2">
			<div style="width:100%; text-align:center">
      	<input type="text" class="light_shadow transparent_70" name="search_people_keyword" id="search_people_keyword" title="Search People..." placeholder="Search People..." class="light_shadow transparent_70" />
        &nbsp;
        <input type="button" value="Search" id="search_people" style="width:100px;" class="light_shadow transparent_70" />
      </div>
			<div style="width:100%;" id="search_people_result">
      ...
      </div>
    </div>
  </div>

	]-->
	
</div>