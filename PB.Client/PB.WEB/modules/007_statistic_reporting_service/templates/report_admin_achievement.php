<script src="<?php echo $this->basepath; ?>libraries/js/jquery_placeholder.js"></script>

<script language="javascript">
	$(document).ready(function(){
		// Tabs
		$('#tabs').tabs();

		function getallachievement(){
			$.post("<?php echo $this->basepath; ?>report/admin/achievement/get/json/all", {}, function(data){
				var all_achievements = eval('('+data+')');
				
				var text = '';
				for($idx = 0; idx < all_achievements.length; idx++){
					
				}
			});
		}

		function getallanimation(){
			$.post("<?php echo $this->basepath; ?>asset/admin/animation/getall/json", {}, function(data){
				var all_animations = eval('('+data+')');

				var text = '';
				for(idx = 0; idx < all_animations.length; idx++){
					text += "<div style='width:200px; margin:5px; min-height:40px; float:left; text-align:left; overflow-x:hidden; border-radius:5px; cursor:pointer;' class='cdiv_animation "+all_animations[idx]['gender']+"' id='div_"+all_animations[idx]['lilo_id']+"' >";
					text += "<div style='width:50px; min-height:40px; float:left; text-align:left; overflow-x:hidden;' class='cdiv_animation_preview' id='preview_"+all_animations[idx]['lilo_id']+"' >";
					text += "<img style='max-width:40px; max-height:40px;' src='<?php echo $this->basepath; ?>bundles/animations/preview/"+all_animations[idx]['preview_file']+"'>";
					text += "</div>";
					text += "<div style='width:150px; min-height:40px; float:left; text-align:left; overflow-x:hidden;' class='cdiv_animation_desc' id='desc_"+all_animations[idx]['lilo_id']+"' >";
					text += "&bull;&nbsp;" + all_animations[idx]['name'] + "<br>";
					text += all_animations[idx]['description'] + "<br>";
					text += all_animations[idx]['gender'] + ", " + all_animations[idx]['permission'] + "<br>";
					text += "</div>";
					text += "</div>";
				}
				
				$('#animation_list').html(text);
			});
		}
		
		getallanimation();
		
		$('#add_submit').live('click', function(){
			if($.trim($('#add_name').val()) == ''){
				alert('Name should not be empty!');
				return false;
			}
			if($.trim($('#add_description').val()) == ''){
				alert('Description should not be empty!');
				return false;
			}
			if($.trim($('#add_animation_file').val()) == ''){
				alert('Where\'s the animation file?');
				return false;
			}
			if($.trim($('#add_preview_file').val()) == ''){
				alert('Where\'s the animation preview file?');
				return false;
			}

			$('#anim_add_form').submit();

		});
		
		$('.cdiv_animation').live({
			mouseenter:
				function(){
					$(this).addClass('shadow');
				},
			mouseleave:
				function(){
					$(this).removeClass('shadow');
				}
			}
		);
		
		$('.cdiv_animation').live('click', function(){
			var _id = $(this).attr('id');
			var _id_split = _id.split('_');
			
			$.post("<?php echo $this->basepath; ?>asset/admin/animation/detail/"+_id_split[1]+"/json", {}, function(data){
//				alert(data);
//				{"name":"Bye","description":"Bye - Male","gender":"male","animation_file":"male@bye.unity3d","preview_file":"anim.png","_id":{"$id":"4eb43b6ac1b4badc09000000"},"lilo_id":"4eb43b6ac1b4badc09000000"}
				var anim_data = eval('('+data+')');
//				, edit_, edit_animation_file, edit_preview_file, 
				$('#edit_lilo_id').val(anim_data['lilo_id']);
				$('#edit_name').val(anim_data['name']);
				$('#edit_description').val(anim_data['description']);
				$('#edit_gender').val(anim_data['gender']);
				$('#edit_permission').val(anim_data['permission']);

//				$('#animation_dialog').html(data);
			});
			$('#animation_dialog').dialog('open');
		});


		$("#animation_dialog").dialog({
			autoOpen: false, 
			minWidth: 600, 
			minHeight: 200,

			buttons: [
				{
					text: "Save",
					click: function() {
						
						if($.trim($('#edit_name').val()) == ''){
							alert('Name should not be empty!');
							return false;
						}
						
						$('#anim_edit_form').submit();
						
					}
				},
				
				{
					text: "Delete this animation",
					click: function() {
//						alert('delete');
						if(!confirm('Delete this animation data?')){
							return false;
						}
						var edit_lilo_id = $('#edit_lilo_id').val();
//						alert(edit_lilo_id);
						$.post("<?php echo $this->basepath; ?>asset/admin/animation/delete/" + edit_lilo_id, {}, function(data){
							if($.trim(data) == "1"){
								$('#div_' + edit_lilo_id).removeClass('male');
								$('#div_' + edit_lilo_id).removeClass('female');
								$('#div_' + edit_lilo_id).hide('slow');
							}
						});
						$(this).dialog("close");
					}
				},
				
				{
					text: "Cancel",
					click: function() { $(this).dialog("close"); }
				},
				
			]

		});

		$('#anim_gender_all').live('click', function(){
			$('.male').show();
			$('.female').show();
		});

		$('#anim_gender_female').live('click', function(){
			$('.male').hide();
			$('.female').show();
		});

		$('#anim_gender_male').live('click', function(){
			$('.female').hide();
			$('.male').show();
		});

		
	});

</script>
<div class="centered transparent_70" style="width:960px; height:540px; border:none;">
	<div style="float:left; width:960px; height:80px;">

  </div>

  <div id="tabs" style="float:left; width:960px;">
    <ul>
      <li><a href="#tabs-1">List Emo!</a></li>
      <li><a href="#tabs-2">Add New Emo!</a></li>
    </ul>
    <div id="tabs-1">
			<div style="width:100%; text-align:left">
      	<div style="width:100%">
        	<a id="anim_gender_all">All</a>
          &nbsp;|&nbsp;
        	<a id="anim_gender_male">Male</a>
          &nbsp;|&nbsp;
        	<a id="anim_gender_female">Female</a>
        </div>
        <div style="width:100%; text-align:left" id="animation_list">
  
        </div>
      </div>
    </div>
    <div id="tabs-2">

      <form id="anim_add_form" class="input_form" method="post" enctype="multipart/form-data" action="<?php echo $this->basepath; ?>asset/admin/animation/add">
        <table style="width:95%">
          <tr>
            <td>Name</td>
            <td><input type="text" size="40" name="name" id="add_name" class="light_shadow transparent_70" placeholder="Animation Name" /></td>
          </tr>
          <tr>
            <td>Description</td>
            <td><input type="text" size="40" name="description" id="add_description" class="light_shadow transparent_70" placeholder="Animation Description" /></td>
          </tr>
          <tr>
            <td>Gender</td>
            <td>
            	<select name="gender" id="add_gender">
              	<option value="male">Male</option>
              	<option value="female">Female</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>Permission</td>
            <td>
            	<select name="permission" id="add_permission">
              	<option value="default">Default</option>
              	<option value="free">Free</option>
              	<option value="premium">Premium</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>Animation File</td>
            <td><input type="file" name="animation_file" id="add_animation_file" class="light_shadow transparent_70" placeholder="Animation File" /></td>
          </tr>
          <tr>
            <td>Animation Preview File</td>
            <td><input type="file" name="preview_file" id="add_preview_file" class="light_shadow transparent_70" placeholder="Animation Preview File" /></td>
          </tr>
          <tr>
          	<td colspan="2" style="text-align:center"><input type="button" id="add_submit" value="Save" /></td>
          </tr>
				</table>
			</form>

    </div>
	</div>
  
</div>


<div style="width: auto; min-height: 200px; height: auto; min-width:600px; " class="ui-dialog-content ui-widget-content" id="animation_dialog" 
	title="Detail Animation">

  <form id="anim_edit_form" class="input_form" method="post" enctype="multipart/form-data" action="<?php echo $this->basepath; ?>asset/admin/animation/edit">
    <input type="hidden" name="lilo_id" id="edit_lilo_id" value="" />
    <table style="width:95%">
      <tr>
        <td>Name</td>
        <td><input type="text" size="40" name="name" id="edit_name" class="light_shadow transparent_70" placeholder="Animation Name" value="" /></td>
      </tr>
      <tr>
        <td>Description</td>
        <td><input type="text" size="40" name="description" id="edit_description" class="light_shadow transparent_70" placeholder="Animation Description" /></td>
      </tr>
      <tr>
        <td>Gender</td>
        <td>
          <select name="gender" id="edit_gender">
            <option value="male">Male</option>
            <option value="female">Female</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Permission</td>
        <td>
          <select name="permission" id="edit_permission">
            <option value="default">Default</option>
            <option value="free">Free</option>
            <option value="premium">Premium</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Animation File</td>
        <td>
        		<input type="file" name="animation_file" id="edit_animation_file" class="light_shadow transparent_70" placeholder="Animation File" />
        		<br />
            <font size="-4">Kosongkan jika tidak ingin mengubah data</font>
				</td>
      </tr>
      <tr>
        <td>Animation Preview File</td>
        <td>
        		<input type="file" name="preview_file" id="edit_preview_file" class="light_shadow transparent_70" placeholder="Animation Preview File" />
        		<br />
            <font size="-4">Kosongkan jika tidak ingin mengubah data</font>
        </td>
      </tr>
    </table>
	</form>

</div>