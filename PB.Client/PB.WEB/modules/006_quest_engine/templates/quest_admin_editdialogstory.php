<?php
$dialog = $this->dialog_story_detail;
?>

<style type="text/css">
table{
	background-color: white;
	border: 1px solid #00CCFF;
	border-collapse: collapse;
	width: 100%;
}

th, td {
	background-color: #FFFFFF;
	border: 1px solid #00CCFF;
	padding: 3px;
	vertical-align: top;
}

td {
	text-align: left;
}

#myForm input[type=text]{
	width: 150px;
}

.deleteoption{
	color:red !important;
}
</style>

<script type="text/javascript">
$(document).ready(function(){
	//alert('woi');

	var NextIDs = ["-1"];
	
	function populateNextOption(){
		NextIDs = ["-1"];	// reset ke awal
		$('.dialogid').each(function(index){
			// alert(index + ':' + $(this).val());
			NextIDs.push($(this).val());
		});
		NextIDs.sort(function(a,b){return a-b});
	}
	
	populateNextOption();
	
	$('.dialogid').die('change');
	$('.dialogid').live('change', function(){
		populateNextOption();
	});
	
	$('.nextidoption').die('focus');
	$('.nextidoption').live('focus', function(){
		$(this).autocomplete({
			source: NextIDs,
			delay: 0,
			minLength: 0
		}).focus(function(){
//			if(this.value == ""){
				// $(this).trigger('keydown.autocomplete');
				$(this).data("autocomplete").search($(this).val());
//			}
		});
	});

	$('.nextidoption').die('click');
	$('.nextidoption').live('click', function(){
		//$(this).trigger('keydown.autocomplete');//alert('woi');
		$(this).data("autocomplete").search('');
	});

	
	$('.deleteoption').die('click');
	$('.deleteoption').live('click', function(){
		if(!confirm('Delete this option?')){
			return;
		}
		var _id = $(this).attr('id');
		//alert(_id);
		var _id_split = _id.split('_');
		var table_to_hide = '#tableoption_' + _id_split[2];
		//alert(table_to_hide);
		$(table_to_hide).children().remove();
		$(table_to_hide).hide();
	});
	
	$('.select_options_edit').die('change');
	$('.select_options_edit').live('change', function(){
		var _val = $(this).val();	// 0: Choice, 1: Quest
		var _id = $(this).attr('id');
		
		//alert("Val: " + _val + ", ID: " + _id);
		
		var _idx = _id.replace(/selectoptions_/g,"");
		//alert(_idx);
		
		// yg di show / hide adalah: option_content_quest_, selectquestedit_, option_content_choice_
		var _elm_choice = "#option_content_choice_" + _idx;
		var _elm_quest = "#option_content_quest_" + _idx + ", #selectquestedit_" + _idx;
		
		if(_val == "0"){
			//$('#nextidoption_content_choice_' + _idx).attr('readonly', false);
			
			$(_elm_choice).show();
			$(_elm_choice).removeAttr('disabled');
			
			$("#option_content_choice_" + _idx).val('');
			
			$(_elm_quest).hide();
			$(_elm_quest).attr('disabled','disabled');
		} else {
			//$('#nextidoption_content_choice_' + _idx).attr('readonly', true);
			
			$(_elm_choice).hide();
			$(_elm_choice).attr('disabled','disabled');
			
			$("#option_content_quest_" + _idx).val('');
			
			$(_elm_quest).show();
			$(_elm_quest).removeAttr('disabled');
		}
		
	});

	
	
	$('.deletedialog').die('click');
	$('.deletedialog').live('click', function(){
		var _id = $(this).attr('id');
		if(!confirm('Are you sure to delete this dialog component?')){
			return;
		}
		$(".tr" + _id).children().remove();
		$(".tr" + _id).hide();
	});
	
	
	var dialog_idx = <?php echo count($dialog['Dialogs']); ?>;
	
	$('#addnewdialog').die('click');
	$('#addnewdialog').live('click', function(){
		// 3 tr: ID, Description, Options
		dialog_idx++;
		var _html = '<tr class="trdeletedialogid_' + dialog_idx + '">';
				_html += '<td style="background-color:#99ccff;">ID</td>';
				_html += '<td colspan="1" style="background-color:#99ccff;"><input type="text" class="dialogid" id="dialogid_' + dialog_idx + '" name="dialogid_' + dialog_idx + '" value="" /></td>';
				_html += '<td colspan="1" style="background-color:#99ccff; text-align: center; vertical-align: middle;"><a class="deletedialog" id="deletedialogid_' + dialog_idx + '">Delete</a></td>';
				_html += '</tr>';
				
		_html += '<tr class="trdeletedialogid_' + dialog_idx + '">';
				_html += '<td>Description</td>';
				_html += '<td colspan="2"><input style="width: 90%;" type="text" name="description_' + dialog_idx + '" value="" /></td>';
				_html += '</tr>';
				
		_html += '<tr class="trdeletedialogid_' + dialog_idx + '">';
			_html += '<td valign="top">';
				_html += 'Options';
				_html += '<br /><br />';
				_html += '[<a class="addnewoption" id="addnewoption_' + dialog_idx + '" style="text-decoration: none; color: #00f; font-weight: normal;">Add Option</a>]';
			_html += '</td>';
			_html += '<td colspan="2" id="tdoption_' + dialog_idx + '">';
				_html += '<table id="tableoption_' + newoptionidx + '" class="table_option" style="width: 95%; border: 0;">';
					_html += '<tr>';
						_html += '<td style="border: 0; vertical-align: middle;">&bull;&nbsp;Tipe</td>';
						_html += '<td style="border: 0; vertical-align: middle; width: 3px;">:</td>';
						_html += '<td style="border: 0; vertical-align: middle;">';
							
							_html += '<select name="select_options_' + dialog_idx + '_' + newoptionidx + '" class="select_options_edit" id="selectoptions_' + dialog_idx + '_' + newoptionidx + '">';
								_html += '<option value="0" selected="selected">Choice</option>';
								_html += '<option value="1">Quest</option>';
							_html += '</select>';
							
						_html += '</td>';
						
						_html += '<td style="border: 0; vertical-align: middle;">&bull;&nbsp;Content</td>';
						_html += '<td style="border: 0; vertical-align: middle; width: 3px;">:</td>';
						_html += '<td style="border: 0; vertical-align: middle;">';
						
							_html += '<input style="display: none; width: 105px;" type="text"';
								_html += 'name="option_content_quest_' + dialog_idx + '_' + newoptionidx + '"';
								_html += 'id="option_content_quest_' + dialog_idx + '_' + newoptionidx + '"';
								
								_html += 'disabled="disabled"';
								
								_html += 'value="" />';
								
								_html += ' <input style="display: none;" type="button" class="select_quest" id="selectquestedit_' + dialog_idx + '_' + newoptionidx + '" value="..." />';
							
							_html += '<input type="text"';
								_html += 'id="option_content_choice_' + dialog_idx + '_' + newoptionidx + '"';
								_html += 'name="option_content_choice_' + dialog_idx + '_' + newoptionidx + '"';
								
								_html += 'value="" />';
							
						_html += '</td>';
						
						_html += '<td style="border: 0; vertical-align: middle;">&bull;&nbsp;Next</td>';
						_html += '<td style="border: 0; vertical-align: middle; width: 3px;">:</td>';
						_html += '<td style="border: 0; vertical-align: middle;">';
							_html += '<input type="text" style="width: 40px;" class="nextidoption"';
								_html += 'name="nextidoption_content_choice_' + dialog_idx + '_' + newoptionidx + '"';
								_html += 'id="nextidoption_content_choice_' + dialog_idx + '_' + newoptionidx + '"';
								_html += 'value="" />&nbsp;&nbsp;[<a class="deleteoption" id="deleteoption_' + dialog_idx + '_' + newoptionidx + '">X</a>]';
						
						_html += '</td>';
					_html += '</tr>';
				_html += '</table>';
			_html += '</td>';
		_html += '</tr>';
		
		$('#dialogtable > tbody:last').append(_html);
		newoptionidx++;
	});
	
	
	<?php
		// hitung index utk newoption
		$newoptionidx = 1;
		foreach($dialog['Dialogs'] as $dd_){
			$newoptionidx += count($dd_['Options']);
		}
	?>
	
	var newoptionidx = <?php echo $newoptionidx; ?>;
	// alert('newoptionidx: ' + newoptionidx);
	
	$('.addnewoption').die('click');
	$('.addnewoption').live('click', function(){
		var _id = $(this).attr('id');
		// alert(_id);
		var _id_split = _id.split('_');
		// alert(_id_split[1]);
		
		var _html = '<table id="tableoption_' + newoptionidx + '" class="table_option" style="width: 95%; border: 0;">';
			_html += '<tr>';
				_html += '<td style="border: 0; vertical-align: middle;">&bull;&nbsp;Tipe</td>';
				_html += '<td style="border: 0; vertical-align: middle; width: 3px;">:</td>';
				_html += '<td style="border: 0; vertical-align: middle;">';
					
					_html += '<select name="select_options_' + _id_split[1] + '_' + newoptionidx + '" class="select_options_edit" id="selectoptions_' + _id_split[1] + '_' + newoptionidx + '">';
						_html += '<option value="0" <?php if($opt['Tipe'] == '0'){ ?>selected="selected"<?php } ?>>Choice</option>';
						_html += '<option value="1" <?php if($opt['Tipe'] == '1'){ ?>selected="selected"<?php } ?>>Quest</option>';
					_html += '</select>';
					
				_html += '</td>';
				
				_html += '<td style="border: 0; vertical-align: middle;">&bull;&nbsp;Content</td>';
				_html += '<td style="border: 0; vertical-align: middle; width: 3px;">:</td>';
				_html += '<td style="border: 0; vertical-align: middle;">';
				
					_html += '<input style="display: none; width: 105px;" type="text"';
						_html += 'name="option_content_quest_' + _id_split[1] + '_' + newoptionidx + '"';
						_html += 'id="option_content_quest_' + _id_split[1] + '_' + newoptionidx + '"';
						
						_html += 'disabled="disabled"';
						
						_html += 'value="" />';
						
						_html += ' <input style="display: none;" type="button" class="select_quest" id="selectquestedit_' + _id_split[1] + '_' + newoptionidx + '" value="..." />';
					
					_html += '<input type="text"';
						_html += 'id="option_content_choice_' + _id_split[1] + '_' + newoptionidx + '"';
						_html += 'name="option_content_choice_' + _id_split[1] + '_' + newoptionidx + '"';
						
						//_html += 'disabled="disabled"';
						
						_html += 'value="" />';
					
				_html += '</td>';
				
				_html += '<td style="border: 0; vertical-align: middle;">&bull;&nbsp;Next</td>';
				_html += '<td style="border: 0; vertical-align: middle; width: 3px;">:</td>';
				_html += '<td style="border: 0; vertical-align: middle;"><input type="text" style="width: 40px;" class="nextidoption" name="nextidoption_content_choice_' + _id_split[1] + '_' + newoptionidx + '" id="nextidoption_content_choice_' + _id_split[1] + '_' + newoptionidx + '" value="" />&nbsp;&nbsp;[<a class="deleteoption" id="deleteoption_' + _id_split[1] + '_' + newoptionidx + '">X</a>]</td>';
			_html += '</tr>';
		_html += '</table>';
		
		
		$('#tdoption_' + _id_split[1]).append(_html);
		
		newoptionidx++;
	});
	
});
</script>

<div style="max-height:600px; overflow:auto; max-width:890px;">
<form id="myForm">
<input type="hidden" name="lilo_id" value="<?php echo $dialog['LILO_ID'] ?>" />
<table>
	<tr>
  	<th style="width:100px;">Name</th>
    <td colspan="2"><input style="width: 90%;" type="text" name="dialog_story_name" value="<?php echo $dialog['Name'] ?>" /><?php //print_r($dialog); ?></td>
  </tr>
	<tr>
  	<th style="width:100px;">Description</th>
    <td colspan="2"><input style="width: 90%;" type="text" name="dialog_story_description" value="<?php echo $dialog['Description'] ?>" /><?php //print_r($dialog); ?></td>
  </tr>
  
	
  <tr>
  	<th valign="top" style="text-align: center;">
			Dialog
			<br />
			<br />
			[<a id="addnewdialog" style="text-decoration: none; color: #00f; font-weight: normal;">Add Dialog</a>]
		</th>
    <td>
    	<table id="dialogtable" border="1" style="text-align:left; width:100%" style="text-align:left; border:#000 thick groove; vertical-align:top; border-collapse:collapse;">
				<?php
				$dialog_idx = 0;
				$opt_idx = 0;
        foreach($dialog['Dialogs'] as $dd){
			  ?>
      	<tr class="trdeletedialogid_<?php echo $dialog_idx + 1; ?>">
        	<td style="background-color:#99ccff;">ID</td>
        	<td colspan="1" style="background-color:#99ccff;"><input type="text" class="dialogid" id="dialogid_<?php echo $dialog_idx + 1; ?>" name="dialogid_<?php echo $dialog_idx + 1; ?>" value="<?php echo $dd['ID']; ?>" /></td>
					<td colspan="1" style="background-color:#99ccff; text-align: center; vertical-align: middle;"><a class="deletedialog" id="deletedialogid_<?php echo $dialog_idx + 1; ?>">Delete</a></td>
        </tr>
      	<tr class="trdeletedialogid_<?php echo $dialog_idx + 1; ?>">
        	<td>Description</td>
        	<td colspan="2"><input style="width: 90%;" type="text" name="description_<?php echo $dialog_idx + 1; ?>" value="<?php echo $dd['Description']; ?>" /></td>
        </tr>
				
      	<tr class="trdeletedialogid_<?php echo $dialog_idx + 1; ?>">
        	<td valign="top">
						Options
						<br /><br />
						[<a class="addnewoption" id="addnewoption_<?php echo $dialog_idx + 1; ?>" style="text-decoration: none; color: #00f; font-weight: normal;">Add Option</a>]
					</td>
          <td colspan="2" id="tdoption_<?php echo $dialog_idx + 1; ?>">
          	<?php
            foreach($dd['Options'] as $opt){
							$opt_idx++;
						?>
						
							<table id="tableoption_<?php echo $opt_idx; ?>" class="table_option" style="width: 95%; border: 0;">
								<tr>
									<td style="border: 0; vertical-align: middle;">&bull;&nbsp;Tipe</td>
									<td style="border: 0; vertical-align: middle; width: 3px;">:</td>
									<td style="border: 0; vertical-align: middle;">
										
										<!--input type="text" style="width: 20px;" class="select_options" name="select_options_<?php echo $dialog_idx + 1; ?>_<?php echo $opt_idx; ?>" value="<?php echo $opt['Tipe']; ?>" /-->
										
										<select name="select_options_<?php echo $dialog_idx + 1; ?>_<?php echo $opt_idx; ?>" class="select_options_edit" id="selectoptions_<?php echo $dialog_idx + 1; ?>_<?php echo $opt_idx; ?>">
											<option value="0" <?php if($opt['Tipe'] == '0'){ ?>selected="selected"<?php } ?>>Choice</option>
											<option value="1" <?php if($opt['Tipe'] == '1'){ ?>selected="selected"<?php } ?>>Quest</option>
										</select>
										
									</td>
									
									<td style="border: 0; vertical-align: middle;">&bull;&nbsp;Content</td>
									<td style="border: 0; vertical-align: middle; width: 3px;">:</td>
									<td style="border: 0; vertical-align: middle;">
									
										<input style="<?php if($opt['Tipe'] == '0'){ ?>display: none;<?php } ?> width: 105px;" type="text"
											name="option_content_quest_<?php echo $dialog_idx + 1; ?>_<?php echo $opt_idx; ?>"
											id="option_content_quest_<?php echo $dialog_idx + 1; ?>_<?php echo $opt_idx; ?>"
											
											<?php if($opt['Tipe'] == '0'){ ?>
											disabled="disabled"
											<?php } ?>
											
											value="<?php echo $opt['Content']; ?>" />
											
											<input <?php if($opt['Tipe'] == '0'){ ?>style="display: none;"<?php } ?> type="button" class="select_quest" id="selectquestedit_<?php echo $dialog_idx + 1; ?>_<?php echo $opt_idx; ?>" value="..." />
										
										<input <?php if($opt['Tipe'] == '1'){ ?>style="display: none;"<?php } ?> type="text"
											id="option_content_choice_<?php echo $dialog_idx + 1; ?>_<?php echo $opt_idx; ?>"
											name="option_content_choice_<?php echo $dialog_idx + 1; ?>_<?php echo $opt_idx; ?>"
											
											<?php if($opt['Tipe'] == '1'){ ?>
											disabled="disabled"
											<?php } ?>
											
											value="<?php echo $opt['Content']; ?>" />
										
									</td>
									
									<td style="border: 0; vertical-align: middle;">&bull;&nbsp;Next</td>
									<td style="border: 0; vertical-align: middle; width: 3px;">:</td>
									<td style="border: 0; vertical-align: middle;">
										<input type="text" style="width: 40px;" class="nextidoption"
											name="nextidoption_content_choice_<?php echo $dialog_idx + 1; ?>_<?php echo $opt_idx; ?>"
											id="nextidoption_content_choice_<?php echo $dialog_idx + 1; ?>_<?php echo $opt_idx; ?>"
											value="<?php echo $opt['Next']; ?>" />&nbsp;&nbsp;[<a class="deleteoption" id="deleteoption_<?php echo $dialog_idx + 1; ?>_<?php echo $opt_idx; ?>">X</a>]
									
									</td>
								</tr>
							</table>
						
						<?php
							/*
							echo "&bull;&nbsp;Tipe: " . $opt['Tipe'] . "<br />";
							echo "&nbsp;&nbsp;Content: " . $opt['Content'] . "<br />";
							echo "&nbsp;&nbsp;Next: " . $opt['Next'] . "<br /><br />";
							*/
						}
						?>
          </td>
        </tr>
				
        <?php
					$dialog_idx++;
        }
        ?>
      
      </table>
    </td>
  </tr>
  
  
</table>
</form>
</div>

<!--[*
<div style="text-align:center; width:100%; font-size:18px; color:#0CF; font-weight:bolder;">Under Construction...</div>
<div style="text-align:center; width:100%; font-size:60px; color:#0CF; font-weight:bolder;">:)</div>
*]-->