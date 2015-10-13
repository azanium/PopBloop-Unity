<script language="javascript">
	$(document).ready(function(){
		$('#tabs').tabs();

    $('#submit_form').live('click', function(){
			// cek apakah code sudah digunakan
			var code = $('#code').val();
			$.post("<?php echo $this->basepath; ?>asset/admin/wsinventoryicons/exists/" + code, {}, function(data){
				if(data != "OK"){
					alert(data);
					$('#code').focus();
					return;
				} else {
					
					// cek input file
					var file = $('#file').val();
					if(file == ''){
						alert('File should not be empty.');
						return;
					}
					
					$('#inventory_add_form').submit();
				}
			});
		});
		
		$('#submit_edit_form').live('click', function(){
			var code = $('#edit_code').val();
			
			if($.trim(code) == ''){
				alert('Code should not be empty.');
				return;
			}
			
			$('#inventory_edit_form').submit();
		});
		
		$("#inventory_dialog").dialog({
			autoOpen: false, 
			minWidth: 600, 
			minHeight: 200
    });

		$('#close_edit_form').live('click', function(){
			$("#inventory_dialog").dialog('close');
		});

		
		$('.edit_inventory').live('click', function(){
			var _id = $(this).attr('id');
			
			// alert(_id);	// editinventory_CODE 002
			
			var _id_split = _id.split('__');
			
			var code = _id_split[1];
			
			// alert(code);
			
			// dapatkan data utk code ini
			$.post("<?php echo $this->basepath; ?>asset/admin/wsinventoryicons/get/" + code, {}, function(data){
				// alert(data);
				
				var _data = eval('(' + data + ')');
				
				$('#edit_code').val(_data['code']);
				$('#old_file').html(_data['file']);
				$("#inventory_dialog").dialog('open');
				
			});
			
			
		});
		
		$('.reset_version').live('click', function(){
			
			if(!confirm('Are you sure to reset this inventory icons to version 1?')){
				return;
			}
			
			var _id = $(this).attr('id');
			var _id_split = _id.split('__');
			
			var code = _id_split[1];
			
			//alert(code);
			
			$.post("<?php echo $this->basepath; ?>asset/admin/wsinventoryicons/resetversion/" + code, {}, function(data){
				if(data == 'OK'){
					loadInventory();
					alert('Data updated');
					return;
				} else {
					alert('Data update failed.');
					return;
				}
			});
			
		});
		
		
		$('.delete_icon').live('click', function(){
			if(!confirm('Are you sure to delete this data?')){
				return;
			}
			
			var _id = $(this).attr('id');
			var _id_split = _id.split('__');
			
			var code = _id_split[1];
			
			$.post("<?php echo $this->basepath; ?>asset/admin/wsinventoryicons/delete/" + code, {}, function(data){
				if(data == 'OK'){
					loadInventory();
					alert('Data deleted');
					return;
				} else {
					alert('Data deletion failed.');
					return;
				}
			});
		});

		function loadInventory(){
			$.post("<?php echo $this->basepath; ?>asset/admin/wsinventoryicons/getall", {}, function(data){
				var _data = eval('(' + data + ')');
				var _html = '<table style="width:100%; text-align:left; border:0;">';
				_html += '<tr><th>No</th><th>Code</th><th>Version</th><th>File</th><th style="text-align:center;">Operation</th></tr>';
				
				var no = 1;
        for(var _idx = 0; _idx < _data.length; _idx++){
					_html += '<tr>';
					
					_html += '<td>'+no+'</td>';
					_html += '<td>'+_data[_idx]['code']+'</td>';
					_html += '<td>'+_data[_idx]['version']+'</td>';
					_html += '<td><a target="_blank" href="<?php echo $this->basepath; ?>'+_data[_idx]['file']+'">'+_data[_idx]['file']+'</a></td>';
					_html += '<td style="text-align:center;"><a class="edit_inventory" id="editinventory__'+_data[_idx]['code']+'">Edit</a>&nbsp;|&nbsp;<a class="reset_version" id="resetversion__'+_data[_idx]['code']+'">Reset Version</a>&nbsp;|&nbsp;<a class="delete_icon" id="deleteicon__'+_data[_idx]['code']+'">Delete</a></td>';
					
					_html += '</tr>';
					no++;
				}
				
				_html += '</table>';
				
				$('#tabs-1').html(_html);
			});
		}
		
		loadInventory();
  });
  
</script>

<div class="centered transbg" style="width:960px; height:540px; border:none;">
	<div style="float:left; width:960px; height:80px;">
    
  </div>

  <div id="tabs" style="float:left; width:960px;">
    <ul>
      <li><a href="#tabs-1">Inventory Icons</a></li>
      <li><a href="#tabs-2">New Inventory Icons</a></li>
    </ul>
    <div id="tabs-1">
    </div>
    <div id="tabs-2">
      <form id="inventory_add_form" class="input_form" method="post" enctype="multipart/form-data" action="<?php echo $this->basepath; ?>asset/admin/wsinventoryicons/add/">
        <table style="width:95%">
          <tr>
            <td>Code</td>
            <td><input type="text" size="40" name="code" id="code" class="light_shadow transparent_70" placeholder="Code" /></td>
          </tr>
          <tr>
            <td>File</td>
            <td>
              <input type="file" name="file" id="file" class="light_shadow transparent_70" placeholder="File" />
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
  </div>
  
</div>

<div style="width: auto; min-height: 200px; max-height: 400px; min-width:600px; overflow-y:auto; " class="ui-dialog-content ui-widget-content" id="inventory_dialog" title="Edit Inventory">
  <form id="inventory_edit_form" class="input_form" method="post" enctype="multipart/form-data" action="<?php echo $this->basepath; ?>asset/admin/wsinventoryicons/update">
    <table style="width:95%">
      <tr>
        <td>Code</td>
        <td><input type="text" size="40" name="code" id="edit_code" class="light_shadow transparent_70" placeholder="Code" readonly="readonly" /></td>
      </tr>
      <tr>
        <td>File</td>
        <td>
          <input type="file" name="file" id="edit_file" class="light_shadow transparent_70" placeholder="File" />
					<br />
					<span id="old_file"></span>
        </td>
      </tr>
      
      <tr>
        <td colspan="2" style="text-align:center">
          <input type="button" id="submit_edit_form" value="Submit" class="light_shadow transparent_70" />&nbsp;<input type="button" id="close_edit_form" value="Cancel" />
        </td>
      </tr>
    </table>
  </form>
</div>

