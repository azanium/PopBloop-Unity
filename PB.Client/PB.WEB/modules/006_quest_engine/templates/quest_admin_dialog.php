<script src="<?php echo $this->basepath; ?>libraries/js/jquery_placeholder.js"></script>
<script language="javascript">
	$(document).ready(function(){
		$('input[placeholder],textarea[placeholder]').placeholder();
		
		$('#tabs').tabs();
		
		$('#tabs_new').tabs();
				
//		$('#create_new_dialog_form').submit(function() {
		$('#create_new_dialog_form_submit').die('click');
		$('#create_new_dialog_form_submit').live('click', function() {
			var _inc = 1;
			var _inc_dialog_id = 0;

//			alert($(this).serialize());
			var dialog_story_name = $("#dialog_story_name").val();//alert(dialog_story_name);
			var dialog_story_description = $("#dialog_story_description").val();
			
			var all_variable = $('#create_new_dialog_form').serialize();//alert(all_variable);
			
			$.post("<?php echo $this->basepath; ?>quest/admin/ws_dialogstory/create", {'name': dialog_story_name, 'description': dialog_story_description, 'all_variable': all_variable}, function(data){
				if(data == "1"){
					alert('Data berhasil ditambahkan');
					// window.location.replace("<?php echo $this->basepath; ?>asset/admin/avatar");
					
					$.post("<?php echo $this->basepath; ?>" + "quest/admin/dialog", {}, function(data_){
						$("#detail_container").html(data_);
					});

					
				} else {
					alert('Data gagal ditambahkan. Data: ' + data);
				}
			});
			
			return false;
		});
		
		// dari awal langsung load semua dialog story ke search_dialog_result
		$.post("<?php echo $this->basepath; ?>quest/admin/ws_dialogstory", {}, function(data){
			var all_stories = eval('('+data+')');
			
			var text = '';
			for(idx = 0; idx < all_stories.length; idx++){
				text = text + '<div style="width:240px; height:80px; float:left; text-align:left; overflow-x:auto;" id="div_'+all_stories[idx].lilo_id+'">';
				text = text + '&bull;&nbsp;' + all_stories[idx].name + '<br />';
				text = text + all_stories[idx].description + '<br />';
				text = text + '<a style="color:#0CF;" class="edit_stories" id="edit_'+all_stories[idx].lilo_id+'">Detail...</a>' + '<br />';
		
				text = text + '</div>';
			}

			
			$("#search_dialog_result").html(text);
			
		});


		$(".edit_stories").die('click');
		$(".edit_stories").live('click', function(){
			var _id = $(this).attr('id');
			var _id_split = _id.split('_');
			
			// alert('You choose to edit: ' + _id_split[1]);
			$('#editor_div').html(_id_split[1]);
			$('#current_dialog').val(_id_split[1]);
			
			$.post("<?php echo $this->basepath; ?>quest/admin/editdialogstory/" + _id_split[1], {}, function(data){
				$("#editor_div").html(data);
				$("#editor_div").dialog('open');
			});
		});
		
		$(".delete_fieldset").die('click');
		$(".delete_fieldset").live('click', function(){
			if(confirm('Hapus?')){
				var _id = $(this).attr('id');
				var _id_split = _id.split('_'); //delete,fieldset,x
				var _id_fieldset = 'fieldset_' + _id_split[2];
				
				$("#" + _id_fieldset).children().remove();
				
				$('#' + _id_fieldset).hide("slow");
				//alert(_id_fieldset);
			}
		});
		
		$('.select_options').die('change');
		$('.select_options').live('change', function(){
			var _id = $(this).attr('id');
			var _id_split = _id.split('_'); //select,options,[fieldset_id],[incremental_index]
			//alert(_id);
			
			// dapatkan span id
			var span_id = 'span_' + _id_split[2] + '_' + _id_split[3];
			
			var value = $(this).val();
			
			var id_name_choice = 'option_content_choice_' + _id_split[2] + '_' + _id_split[3];
			var id_name_quest = 'option_content_quest_' + _id_split[2] + '_' + _id_split[3];
			var id_name_select_quest = 'selectquest_' + _id_split[2] + '_' + _id_split[3];
			
			var span_text_0 = "<input type='text' name='"+id_name_choice+"' id='"+id_name_choice+"' value='' placeholder='Description' />&nbsp;<input type='text' name='nextid"+id_name_choice+"' id='nextid"+id_name_choice+"' value='' placeholder='Next ID' />";
			var span_text_1 = "<input type='text' name='"+id_name_quest+"' id='"+id_name_quest+"' value='' placeholder='Select Quest...' readonly='readonly' />&nbsp;<input type='button' class='select_quest' id='"+id_name_select_quest+"'  value='...' />&nbsp;<input type='text' name='nextid"+id_name_choice+"' id='nextid"+id_name_choice+"' value='' placeholder='Next ID' />";
			
			if(value == "0"){
				$('#' + span_id).html(span_text_0);
			} else if(value == "1"){
				$('#' + span_id).html(span_text_1);
			}
			
			
			
		});
		
		var add_option_inc = 1;
		$('.add_option').die('click');
		$('.add_option').live('click', function(){
//				alert($(this).attr('id'));
			var _id = $(this).attr('id');
			var _id_split = _id.split('_'); //add,option,x
			var _id_fieldset = 'options_fieldset_' + _id_split[2];
//			var text_ = $('#'+_id_fieldset).html();
			var text_ = '';

			var id_name = 'select_options_' + _id_split[2] + '_' +  add_option_inc;
			var span_id = 'span_' + _id_split[2] + '_' +  add_option_inc;


			text_ = text_ + "<select class='select_options' name='"+id_name+"' id='"+id_name+"'>";
			text_ = text_ + "<option value=''>--Type--</option>";
			text_ = text_ + "<option value='0'>Choice</option>";
			text_ = text_ + "<option value='1'>Quest</option>";
			text_ = text_ + "</select><span id='"+span_id+"'></span><br />";
//			$('#'+_id_fieldset).html(text_);
			$('#'+_id_fieldset).append(text_);
			add_option_inc++;
		});
		
		var _inc = 1;
		var _inc_dialog_id = 0;
		$("#add_new_dialog").die('click');
		$("#add_new_dialog").live('click', function(){
//			var text = $('#dialog_container').html();
			var text = '';
//			text = text + "<fieldset>ID:<br />Description:<br />Options:<br /></fieldset><br />";
			text = text + "<fieldset id='fieldset_"+_inc+"'>";
			text = text + "<table style='width:100%;text-align:left;'>";
			text = text + "<tr>";
			text = text + "<td colspan='2' style='text-align:right'><a class='delete_fieldset' id='delete_fieldset_"+_inc+"'>[x]</a></td>";
			text = text + "</tr>";
			text = text + "<tr>";
			text = text + "<td width='180px'>ID</td><td><input type='text' name='dialogid_"+_inc+"' class='dialogid' id='dialogid_"+_inc+"' value='"+_inc_dialog_id+"' /></td>";
			text = text + "</tr>";
			text = text + "<tr>";
			text = text + "<td>Description</td><td><textarea rows='3' cols='50' name='description_"+_inc+"' class='description' id='description_"+_inc+"'></textarea></td>";
			text = text + "</tr>";
			text = text + "<tr>";
//			text = text + "<td>Options</td><td><input type='text' name='options[]' class='options' id='options_"+_inc+"' value=''></td>";
			text = text + "<td>Options</td>";
			text = text + "<td><fieldset id='options_fieldset_"+_inc+"'>";
			text = text + "<a class='add_option' id='add_option_"+_inc+"'>[+]&nbsp;Add Option</a><br />";
			text = text + "</fieldset></td>";
			
			text = text + "</tr>";
			text = text + "</table>";
			text = text + "</fieldset>";
			_inc++;
			_inc_dialog_id++;
//			$('#dialog_container').html(text);
			//alert(text);
			$('#dialog_container').append(text);
		});

		$("#editor_div").dialog({
			autoOpen: false, 
			minWidth: 900, 
			minHeight: 200,

			buttons: [
				{
					text: "Save Changes",
					click: function() { 
						//alert('save...');
						var serialized_data = $('#myForm').serialize();
						//alert(serialized_data);
						
						$.post("<?php echo $this->basepath; ?>quest/admin/editdialogstorysubmit", {serialized_data: serialized_data}, function(data){
							if($.trim(data) == 'OK'){
								alert("Dialog story updated");
								$("#editor_div").dialog('close');
							} else {
								alert("Update dialog story failed.");
							}
						});
						
						return false;
					}
				},
				
				{
					text: "Delete This Dialog Story",
					click: function() { 
						if(confirm('Are you sure to delete this dialog story?')){
							// post ke quest/admin/ws_dialogstory/delete/$id
							var current_dialog = $('#current_dialog').val();
							alert('Hapus...!! - ' + current_dialog);
							
							$.post("<?php echo $this->basepath; ?>quest/admin/ws_dialogstory/delete/" + current_dialog, {}, function(data){
								if(data == "1"){
									$('#div_' + current_dialog).hide("slow");
									$("#editor_div").dialog('close');
								} else {
									alert('Data deletion failed.');
								}
							});
							
						} else {
							alert('Ga dihapus...!!');
						}
					}
				},
				
				{
					text: "Cancel",
					click: function() { $(this).dialog("close"); }
				},
				
			]

		});
		
		$('#select_quest_dialog').dialog({
			autoOpen: false, 
			minWidth: 600, 
			minHeight: 200

		});

		$('.select_quest').die('click');
		$('.select_quest').live('click', function(){
			var _id = $(this).attr('id');
			var _id_split = _id.split('_');

			//alert(_id); // selectquest_1_1
			$('#quest_input').val('#option_content_quest_' + _id_split[1] + '_' + _id_split[2]);

			// dapatkan list Quest
			$.post("<?php echo $this->basepath; ?>quest/admin/ws_quest", {}, function(data){
				var all_quests = eval('('+data+')');
				
				var text = '';
				for(idx = 0; idx < all_quests.length; idx++){
					text += "<div style='width:300px; height:80px; float:left; text-align:left;  overflow-x:hidden;'>";
					text += "&bull;&nbsp;" + all_quests[idx].Description + "<br>";
					text += "Required Energy: " + all_quests[idx].RequiredEnergy + "<br>";
					text += "<span style='color:#0CF; font-weight:bold; cursor:pointer;' class='choose_quest' title='" + all_quests[idx].Description + "' id='choosequest_"+all_quests[idx].ID+"_"+_id+"'>Select...</span>";
					text += "</div>";
				}
				$('#select_quest_dialog').html(text);
			});

			$('#select_quest_dialog').dialog('open');
			
			// update option_content_quest_1_1
		});

		$('.choose_quest').die('click');
		$('.choose_quest').live('click', function(){
			var _title = $(this).attr('title');
			
			var _id = $(this).attr('id');
			//alert(_id);	// choosequest_2_selectquest_1_1
									// choosequest_1_selectquest_1_1
									
									// choosequest_ [Quest ID] _selectquest_1_1
			var _id_split = _id.split('_');
			var input_ = '#option_content_quest_' + _id_split[3] + '_' + _id_split[4];
			var nextinput_ = '#nextidoption_content_choice_' + _id_split[3] + '_' + _id_split[4];

			$('' + input_).val(_id_split[1] + ' - ' + _title);
			$('' + nextinput_).val(_id_split[1]);
			$('#select_quest_dialog').dialog('close');
			
		});

	});
</script>

<input type="hidden" name="selected_quest" id="selected_quest" value="" />
<input type="hidden" name="quest_input" id="quest_input" value="" />
<div style="width: auto; min-height: 58.4px; height: auto; min-width:600px; " class="ui-dialog-content ui-widget-content" id="select_quest_dialog" 
	title="Select Quest">


</div>

<input type="hidden" name="current_dialog" id="current_dialog" value="" />
<div style="width: auto; min-height: 58.4px; height: auto; min-width:900px; " class="ui-dialog-content ui-widget-content" id="editor_div" 
	title="Dialog Story Detail">
  
  <!--[*
  	- dialog story editor [name, description]
    - CRUD for dialogs and options
    - 
  *]-->

</div>



<div class="centered transbg" style="width:960px; border:none;">
  <div id="tabs" style="float:left; width:960px;">
    <ul>
      <li><a href="#tabs-1">Dialog Story</a></li>
      <li><a href="#tabs-2">Create New Dialog Story</a></li>
    </ul>
    <div id="tabs-1">
			<div style="width:100%;" id="search_dialog_result">
      	
      </div>
    	<!--[ NANTI HARUS PAKE SEARCH ]-->
      <!--[
			<div style="width:100%; text-align:center">
      	<input type="text" class="light_shadow transparent_70" name="search_dialog_keyword" id="search_dialog_keyword" title="Search Dialogs..." placeholder="Search Dialogs..." class="light_shadow transparent_70" />
        &nbsp;
        <input type="button" value="Search" id="search_dialog" style="width:100px;" class="light_shadow transparent_70" />
      </div>
			<div style="width:100%;" id="search_dialog_result">
      search result
      </div>
      ]-->
    </div>

    <form method="post" id="create_new_dialog_form" action="<?php echo $this->basepath; ?>quest/admin/ws_dialogstory/create">
    <div id="tabs-2">
			<div style="width:100%;" id="create_new_dialog">
        <div style="float:left; width:100%; text-align:right;">
			    <input id="create_new_dialog_form_submit" type="button" value="Save" style="width:120px;" />
				</div>

        <div id="tabs_new" style="float:left; width:100%;">
          <ul>
            <li><a href="#tabs_new-1">Dialog Story</a></li>
            <li><a href="#tabs_new-2">Dialogs</a></li>
          </ul>
          <div id="tabs_new-1">
            <table style="width:100%; text-align:left">
              <tr>
                <th>Name</th>
                <td><input type="text" name="dialog_story_name" id="dialog_story_name" value="" /></td>
              </tr>
              <tr>
                <th>Description</th>
                <td><textarea rows="3" cols="50" name="dialog_story_description" id="dialog_story_description"></textarea></td>
              </tr>
              <tr>
                <th>Type</th>
                <td>
									<select name="tipedialog" id="tipedialog">
										<option value="npc">NPC</option>
										<option value="float">Float</option>
										<option value="startup">Start Up</option>
									</select>
								</td>
              </tr>
              <tr>
                <td colspan="2">
                </td>
              </tr>
            </table>
          </div>
          <div id="tabs_new-2">
	          <div style="width:100%; text-align:left;"><a id="add_new_dialog">[+] Add New Dialog</a><br />&nbsp;</div>
            <div style="width:100%;" id="dialog_container">
            
            </div>
          </div>
        </div>

        
      </div>
      
      <br />&nbsp;
      
    </div>
    </form>
  </div>

</div>

