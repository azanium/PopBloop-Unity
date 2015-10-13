<style type="text/css">
.quest_, .inventory_item {
	width:180px;
	height:40px;
	float:left;
	text-align:left;
	overflow-x:hidden;
	background-color: transparent;
/*	border:1px solid #FFF; */
	margin:2px;
	padding:5px;
}

.quest_:hover, .inventory_item:hover {
	background-color: #EEE;
/*	border:1px solid #CCC; */
}
</style>

<script src="<?php echo $this->basepath; ?>libraries/js/jquery_placeholder.js"></script>
<script language="javascript">

	$(document).ready(function(){
		function reload_search_result(){
			$.post("<?php echo $this->basepath; ?>quest/admin/ws_quest", {}, function(data){
				var all_quests = eval('('+data+')');
	
				var text = '';
				for(idx = 0; idx < all_quests.length; idx++){
					text = text + '<div style="width:240px; height:80px; float:left; text-align:left; overflow-x:auto;" id="div_'+all_quests[idx].lilo_id+'">';
					text = text + '&bull;&nbsp;' + all_quests[idx].ID + '<br />';
					text = text + all_quests[idx].Description + '<br />';
					text = text + '<a style="color:#0CF;" class="edit_quest" id="edit_'+all_quests[idx].lilo_id+'">Detail...</a>' + '<br />';
					text = text + '</div>';
				}
	
//				alert(text);
				$("#search_quest_result").html(text);
			});
		
		}
		
		$('#quest_id').die('change');
		$('#quest_id').live('change', function(){
			var _ID = $(this).val();
			
			$.post("<?php echo $this->basepath; ?>quest/admin/ws_quest/exist_id", {ID: _ID}, function(data){
				if(data != ''){
					alert(data);
					$("#quest_id").val('');
					$("#quest_id").focus();
				}
			});
			
		});

		$('.edit_quest').die('click');
		$('.edit_quest').live('click', function(){
			var _id = $(this).attr('id');
			var _id_split = _id.split('_');

			var lilo_id = _id_split[1];
			$('#current_quest').val(lilo_id);
//			alert(lilo_id);

			// dari lilo_id, dapatkan detail quest terpilih
			$.post("<?php echo $this->basepath; ?>quest/admin/ws_quest/detail_by_lilo_id/" + lilo_id, {}, function(data){
				if(data == ''){
					return false;
				} else {
					var selected_quest = eval('('+data+')');
					$('#edit_quest_id').val(selected_quest.ID);
					$('#edit_quest_description').val(selected_quest.Description);
					$('#edit_quest_description_normal').val(selected_quest.DescriptionNormal);
					$('#edit_quest_description_active').val(selected_quest.DescriptionActive);
					$('#edit_quest_description_done').val(selected_quest.DescriptionDone);
					$('#edit_requirement').val(selected_quest.Requirement);
					$('#edit_energy').val(selected_quest.RequiredEnergy);
					
					var EditItem_ = selected_quest.RequiredItem;
					/* 21 Juni 2012 */
					$('#edit_item').val(EditItem_);
					
					//var EditItem_split = EditItem_.split('|');
					//var EditItem = EditItem_split[0];
					//var EditItemCount = EditItem_split[1];
					//$('#edit_item').val(EditItem);
					//$('#edit_item_count').val(EditItemCount);
					
					$('#edit_rewards').val(selected_quest.Rewards);
					$('#edit_isdone').val(selected_quest.IsDone);
					$('#edit_isactive').val(selected_quest.IsActive);
					$('#edit_isreturn').val(selected_quest.IsReturn);
					
					// selected_quest.StartDate : 6/30/2012 9:20 AM
					var start_date_split = selected_quest.StartDate.split(' ');
					var end_date_split = selected_quest.EndDate.split(' ');
					
					var start_hour_split = start_date_split[1].split(':');
					var end_hour_split = end_date_split[1].split(':');

					$('#edit_start_date').val(start_date_split[0]/*selected_quest.StartDate*/);//alert(start_date_split[0]);
					$('#edit_end_date').val(end_date_split[0]/*selected_quest.EndDate*/);
					
					$('#edit_start_hour').val(start_hour_split[0]);
					$('#edit_end_hour').val(end_hour_split[0]);
					
					$('#edit_start_minute').val(start_hour_split[1]);
					$('#edit_end_minute').val(end_hour_split[1]);
					
					$('#edit_start_am_pm').val(start_date_split[2]);
					$('#edit_end_am_pm').val(end_date_split[2]);
				}
			});



			$("#editor_div").dialog('open');

		});

//		$('#start_date').datepicker({ dateFormat: 'dd-mm-yy' });
//		$('#end_date').datepicker({ dateFormat: 'dd-mm-yy' });
		$('#start_date').datepicker({ dateFormat: 'm/d/yy' });
		$('#end_date').datepicker({ dateFormat: 'm/d/yy' });

		$('#edit_start_date').datepicker({ dateFormat: 'm/d/yy' });
		$('#edit_end_date').datepicker({ dateFormat: 'm/d/yy' });

		$('input[placeholder],textarea[placeholder]').placeholder();
		
		$('#tabs').tabs();

		// dari awal langsung load semua quest ke search_quest_result
		reload_search_result();

		$('#create_new_quest_button').die('click');
		$('#create_new_quest_button').live('click', function(){
			var quest_id = $('#quest_id').val();
			var quest_description = $('#quest_description').val();
			var quest_description_normal = $('#quest_description_normal').val();
			var quest_description_active = $('#quest_description_active').val();
			var quest_description_done = $('#quest_description_done').val();
			var requirement = $('#requirement').val();
			var energy = $('#energy').val();
			var item_ = $('#item').val();
			var item_count = $('#item_count').val();
			var rewards = $('#rewards').val();
			var isdone = $('#isdone').val();
			var isactive = $('#isactive').val();
			var isreturn = $('#isreturn').val();
			
			var start_date = $('#start_date').val();
			var end_date = $('#end_date').val();
			
			// item_ = $.trim(item_) + '|' + $.trim(item_count);
			
			if(quest_id == ''){
				alert('Quest ID tidak boleh kosong.');
				return false;
			}
			
			var post_data = {	ID: quest_id, Description: quest_description, DescriptionNormal: quest_description_normal, 
												DescriptionActive: quest_description_active, DescriptionDone: quest_description_done, 
												Requirement: requirement, RequiredEnergy: energy, RequiredItem: item_, Rewards: rewards,
												IsDone: isdone, IsActive: isactive, IsReturn: isreturn,
												StartDate: start_date, EndDate: end_date
												};
			
			$.post("<?php echo $this->basepath; ?>quest/admin/ws_quest/create", post_data, function(data){
				if(data == '1'){
//					window.location.replace("<?php echo $this->basepath; ?>quest/admin/default");
//					return false;
					reload_search_result();
					
					$(':input','#create_new_quest_form')
						.not(':button, :submit, :reset, :hidden')
						.val('')
						.removeAttr('checked')
						.removeAttr('selected');

					$('#tabs').tabs('select', '#tabs-1');
				} else {
					alert(data);
					return false;
				}
			});
		});


		$("#editor_div").dialog({
			modal: true, 
			autoOpen: false, 
			minWidth: 900, 
			minHeight: 200,

			buttons: [
				{
					text: "Save Changes",
					click: function() { 
//						alert('save...'); 
						
						
						var quest_id = $('#edit_quest_id').val();
						var quest_description = $('#edit_quest_description').val();
						var quest_description_normal = $('#edit_quest_description_normal').val();
						var quest_description_active = $('#edit_quest_description_active').val();
						var quest_description_done = $('#edit_quest_description_done').val();
						var requirement = $('#edit_requirement').val();
						var energy = $('#edit_energy').val();
						
						var item_ = $('#edit_item').val();	// edit_item
						var item_count_ = $('#edit_item_count').val();	// edit_item_count
						//var item_ = item_ + '|' + item_count_;
						
						var rewards = $('#edit_rewards').val();
						var isdone = $('#edit_isdone').val();
						var isactive = $('#edit_isactive').val();
						var isreturn = $('#edit_isreturn').val();
						
						var lilo_id = $('#current_quest').val();
						
						if(quest_id == ''){
							alert('Quest ID tidak boleh kosong.');
							return false;
						}
						
						// date related data
						var edit_start_date = $('#edit_start_date').val();
						var edit_end_date = $('#edit_end_date').val();
						
						var edit_start_hour = $('#edit_start_hour').val();
						var edit_end_hour = $('#edit_end_hour').val();
						
						var edit_start_minute = $('#edit_start_minute').val();
						var edit_end_minute = $('#edit_end_minute').val();
						
						var edit_start_am_pm = $('#edit_start_am_pm').val();
						var edit_end_am_pm = $('#edit_end_am_pm').val();
						
						// month/date/year hour:minute AM/PM
						var start_date = edit_start_date + ' ' + edit_start_hour + ':' + edit_start_minute + ' ' + edit_start_am_pm;
						var end_date = edit_end_date + ' ' + edit_end_hour + ':' + edit_end_minute + ' ' + edit_end_am_pm;
						
						if($.trim(edit_start_date) == ''){
							start_date = '';
						}
						
						if($.trim(edit_end_date) == ''){
							end_date = '';
						}
						
						var post_data = {	lilo_id: lilo_id, ID: quest_id, Description: quest_description, DescriptionNormal: quest_description_normal, 
															DescriptionActive: quest_description_active, DescriptionDone: quest_description_done, 
															Requirement: requirement, RequiredEnergy: energy, RequiredItem: item_, Rewards: rewards,
															IsDone: isdone, IsActive: isactive, IsReturn: isreturn, StartDate: start_date, EndDate: end_date};
						
						$.post("<?php echo $this->basepath; ?>quest/admin/ws_quest/update", post_data, function(data){
							if(data == '1'){
			
								reload_search_result();
								
								$("#editor_div").dialog('close');
			
								$('#tabs').tabs('select', '#tabs-1');
							} else {
								alert(data);
								return false;
							}
						});
			
						
						
					}
				},
				
				{
					text: "Delete This Quest",
					click: function() { 
						if(confirm('Are you sure to delete this dialog story?')){
							// post ke quest/admin/ws_dialogstory/delete/$id
							var current_quest = $('#current_quest').val();
							alert('Hapus...!! - ' + current_quest);
							
							$.post("<?php echo $this->basepath; ?>quest/admin/ws_quest/delete/" + current_quest, {}, function(data){
								if(data == "1"){
									$('#div_' + current_quest).hide("slow");
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
		
		$('#item_selector').dialog({
			modal: true, 
			autoOpen: false, 
			minWidth: 600, 
			minHeight: 200
		});
		
		$('#quest_selector').dialog({
			modal: true, 
			autoOpen: false, 
			minWidth: 600, 
			minHeight: 200
		});
		
		$('#select_quest, #edit_select_quest').die('click');
		$('#select_quest, #edit_select_quest').live('click', function(){
			var requirement_field = $(this).attr('id') == 'select_quest' ? 'requirement' : 'edit_requirement';
			
			// dapatkan list quest
			// alert('quest dialog');
			$.post("<?php echo $this->basepath; ?>quest/admin/ws_quest", {}, function(data){
				var all_quest = eval('('+data+')');
				
				var text = '';
				for(idx = 0; idx < all_quest.length; idx++){
					text += '<div class="quest_" id="div_'+all_quest[idx].lilo_id+'">';
					text += '&bull;&nbsp;' + all_quest[idx].ID + '<br />';
					text += all_quest[idx].Description + '<br />';
					text += '<a style="color:#0CF;" class="selectedquest" fieldtofill="'+requirement_field+'" title="'+ all_quest[idx].ID +'" id="selectedquest_'+all_quest[idx].lilo_id+'">Select...</a>' + '<br />';
					text += '</div>';
				}
				
//				text += '<input type="hidden" name="requirement_field" id="requirement_field" val="' + requirement_field + '" />';
				
				$('#quest_selector').html(text);
				$('#quest_selector').dialog('open');
			});
			
		});
		
		$('.selectedquest').die('click');
		$('.selectedquest').live('click', function(){
			var _title = $(this).attr('title');
			var _requirement_field = $(this).attr('fieldtofill');//alert(_requirement_field);
			$('#' + _requirement_field).val(_title);
			$('#quest_selector').dialog('close');
		});
		
		$('#select_item, #edit_select_item').die('click');
		$('#select_item, #edit_select_item').live('click', function(){
			var item_field = $(this).attr('id') == 'select_item' ? 'item' : 'edit_item';
			
			// dapatkan list item
			$.post("<?php echo $this->basepath; ?>asset/admin/inventory", {return_json: '1'}, function(data){
				
				var all_items = eval('('+data+')');
	
				var text = '';
				for(idx = 0; idx < all_items.length; idx++){
					text += '<div class="inventory_item" id="div_'+all_items[idx].lilo_id+'">';
					text += '&bull;&nbsp;' + all_items[idx].tipe + '<br />';
					text += all_items[idx].description + '<br />';
					text += '<a style="color:#0CF;" class="selected_item" fieldtofill="'+item_field+'" title="'+ all_items[idx].tipe +'" id="selecteditem_'+all_items[idx].lilo_id+'">Select...</a>' + '<br />';
					text += '</div>';
				}
				$('#item_selector').html(text);
//				alert(data);
			});
			
			$('#item_selector').dialog('open');
		});
		
		$('.selected_item').die('click');
		$('.selected_item').live('click', function(){
			var _id = $(this).attr('id');
			var _title = $(this).attr('title');
			var _item_field = $(this).attr('fieldtofill');
			
			$('#' + _item_field).val(_title);
			
			$('#item_selector').dialog('close');
		});

	});
</script>


<div class="centered transparent_70" style="width:960px; border:none;">
  <div id="tabs" style="float:left; width:960px;">
    <ul>
      <li><a href="#tabs-1">Quests</a></li>
      <li><a href="#tabs-2">Create New Quest</a></li>
    </ul>
    <div id="tabs-1">
			<div style="width:100%;" id="search_quest_result">
      	
      </div>
    </div>

    <form method="post" id="create_new_quest_form" action="<?php echo $this->basepath; ?>quest/admin/ws_quest/create">
    <div id="tabs-2">
			<div style="width:100%;" id="create_new_quest">
        <table style="width:100%; text-align:left">
          <tr>
            <th>ID</th>
            <td colspan="3"><input type="text" name="quest_id" id="quest_id" value="" /></td>
          </tr>
          <tr>
            <th>Description</th>
            <td colspan="3"><textarea rows="2" style="width:96%" name="quest_description" id="quest_description"></textarea></td>
          </tr>
          <tr>
            <th>Description Normal</th>
            <td colspan="3"><textarea rows="2" style="width:96%" name="quest_description_normal" id="quest_description_normal"></textarea></td>
          </tr>
          <tr>
            <th>Description Active</th>
            <td colspan="3"><textarea rows="2" style="width:96%" name="quest_description_active" id="quest_description_active"></textarea></td>
          </tr>
          <tr>
            <th>Description Done</th>
            <td colspan="3"><textarea rows="2" style="width:96%" name="quest_description_done" id="quest_description_done"></textarea></td>
          </tr>
          
          <tr>
          	<th>Start Date</th>
            <td><input type="text" name="start_date" id="start_date" value="" /></td>
          	<th>End Date</th>
            <td><input type="text" name="end_date" id="end_date" value="" /></td>
          </tr>
          
          <tr>
            <th>Requirement (Quest ID)</th>
            <td><input type="text" name="requirement" id="requirement" value="" />&nbsp;<input type="button" id="select_quest" value="..."></td>
            <th>Energy Requirement</th>
            <td><input type="text" name="energy" id="energy" value="" /></td>
          </tr>
          
          <tr>
            <th>Required Item</th>
            <td>
							<?php /* 21 Juni 2012, modifikasi Required Item jadi input string
							<input type="text" name="item" id="item" value="" />&nbsp;<input placeholder="Count" type="text" size="4" name="item_count" id="item_count" value="" />&nbsp;<input type="button" id="select_item" value="..." />
							*/ ?>
							
							<input type="text" name="item" id="item" value="" />
							
						</td>
            <th>Rewards</th>
            <td><input type="text" name="rewards" id="rewards" value="" /></td>
          </tr>
          <tr>
            <th>IsDone</th>
            <td>
            	<select name="isdone" id="isdone">
              	<option value="false">False</option>
              	<option value="true">True</option>
              </select>
            </td>
            <th>IsActive</th>
            <td>
            	<select name="isactive" id="isactive">
              	<option value="false">False</option>
              	<option value="true">True</option>
              </select>
            </td>
          </tr>
          <tr>
            <th>IsReturn</th>
            <td>
            	<select name="isreturn" id="isreturn">
              	<option value="true">True</option>
              	<option value="false">False</option>
              </select>
            </td>
						<td></td>
						<td></td>
          </tr>
          <tr>
            <td colspan="2">
            	<input type="button" name="create_new_quest_button" id="create_new_quest_button" value="Save" />
            </td>
          </tr>
        </table>

        
      </div>
      
      <br />&nbsp;
      
    </div>
    </form>
  </div>

</div>


<input type="hidden" name="current_quest" id="current_quest" value="" />
<div style="width: auto; min-height: 58.4px; height: auto; min-width:890px; " class="ui-dialog-content ui-widget-content" id="editor_div" 
	title="Quest Detail">
  <table style="width:100%; text-align:left">
    <tr>
      <th>ID</th>
      <td colspan="3"><input type="text" name="edit_quest_id" id="edit_quest_id" value="" /></td>
    </tr>
    <tr>
      <th>Description</th>
      <td colspan="3"><textarea rows="2" style="width:96%" name="edit_quest_description" id="edit_quest_description"></textarea></td>
    </tr>
    <tr>
      <th>Description Normal</th>
      <td colspan="3"><textarea rows="2" style="width:96%" name="edit_quest_description_normal" id="edit_quest_description_normal"></textarea></td>
    </tr>
    <tr>
      <th>Description Active</th>
      <td colspan="3"><textarea rows="2" style="width:96%" name="edit_quest_description_active" id="edit_quest_description_active"></textarea></td>
    </tr>
    <tr>
      <th>Description Done</th>
      <td colspan="3"><textarea rows="2" style="width:96%" name="edit_quest_description_done" id="edit_quest_description_done"></textarea></td>
    </tr>
    
    <tr>
      <th>Start Date</th>
      <td><input size="10" type="text" name="edit_start_date" id="edit_start_date" value="" />&nbsp;<input size="2" type="text" name="edit_start_hour" id="edit_start_hour" value="" />&nbsp;<input size="2" type="text" name="edit_start_minute" id="edit_start_minute" value="" />&nbsp;<select name="edit_start_am_pm" id="edit_start_am_pm" ><option value="AM">AM</option><option value="PM">PM</option></select></td>
      <th>End Date</th>
      <td><input size="10" type="text" name="edit_end_date" id="edit_end_date" value="" />&nbsp;<input size="2" type="text" name="edit_end_hour" id="edit_end_hour" value="" />&nbsp;<input size="2" type="text" name="edit_end_minute" id="edit_end_minute" value="" />&nbsp;<select name="edit_end_am_pm" id="edit_end_am_pm" ><option value="AM">AM</option><option value="PM">PM</option></select></td>
    </tr>
    
    <tr>
      <th>Requirement (Quest ID)</th>
      <td><input type="text" name="edit_requirement" id="edit_requirement" value="" size="6" />&nbsp;<input type="button" id="edit_select_quest" value="..."></td>
      <th>Energy Requirement</th>
      <td><input type="text" name="edit_energy" id="edit_energy" value="" /></td>
    </tr>
    <tr>
      <th>Required Item</th>
      <td>
				<?php /* 21 Juni 2012, modifikasi Required Item jadi input string
				<input type="text" name="edit_item" id="edit_item" value="" size="6" />&nbsp;<input placeholder="Count" type="text" size="3" name="edit_item_count" id="edit_item_count" value="" />&nbsp;<input type="button" id="edit_select_item" value="..." />
				*/ ?>
				<input type="text" name="edit_item" id="edit_item" value="" />
			</td>
      <th>Rewards</th>
      <td><input type="text" name="edit_rewards" id="edit_rewards" value="" /></td>
    </tr>
    <tr>
      <th>IsDone</th>
      <td>
        <select name="edit_isdone" id="edit_isdone">
          <option value="false">False</option>
          <option value="true">True</option>
        </select>
      </td>
      <th>IsActive</th>
      <td>
        <select name="edit_isactive" id="edit_isactive">
          <option value="false">False</option>
          <option value="true">True</option>
        </select>
      </td>
    </tr>
    <tr>
      <th>IsReturn</th>
      <td>
        <select name="edit_isreturn" id="edit_isreturn">
          <option value="true">True</option>
          <option value="false">False</option>
        </select>
      </td>
      <td></td>
      <td></td>
    </tr>
  </table>

</div>


<div style="width: auto; min-height: 200px; height: auto; min-width:600px; " class="ui-dialog-content ui-widget-content transparent_70" id="item_selector" 
	title="Select Item">

</div>

<div style="width: auto; min-height: 200px; height: auto; min-width:600px; " class="ui-dialog-content ui-widget-content transparent_70" id="quest_selector" 
	title="Select Quest">

</div>