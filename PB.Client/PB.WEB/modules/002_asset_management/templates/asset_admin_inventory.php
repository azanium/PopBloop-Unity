<script language="javascript">
	$(document).ready(function(){
		// http://css-tricks.com/6291-row-and-column-highlighting/
		
		$('#tabs').tabs();
		
		$("#viewicon_dialog").dialog({
			autoOpen: false, 
			minWidth: 760, 
			minHeight: 500
//			maxWidth: 800, 
//			maxHeight: 600
    });
		
		$('.viewicon').live('click', function(){
			var src = $(this).attr('src');
			var text = "<img src='"+src+"' />";
			$("#viewicon_dialog").html(text);
			$("#viewicon_dialog").dialog('open');
		});

		$("#inventory_dialog").dialog({
			autoOpen: false, 
			minWidth: 600, 
			minHeight: 200
    });

    $('#submit_form').live('click', function(){
      var tipe = $.trim($("#tipe").val());
  
      $.post("<?php echo $this->basepath; ?>asset/admin/inventory_exist", {tipe: tipe}, function(data){
        if(data == 'EMPTYTYPE'){
          alert('Type name should not be empty...');
          return false;
        } else if(data == '1'){	// name exists
          var override_confirm = confirm('Tipe ' + tipe + ' sudah ada di server. Timpa dengan data yang baru?');
          if(!override_confirm){
            return false;
          } else {
            $("#inventory_add_form").submit();
          }
        } else {
          $("#inventory_add_form").submit();
        }
      });
    });
    
    $('#submit_edit_form').live('click', function(){
      var edit_tipe = $.trim($("#edit_tipe").val());
      
      var edit_inventory_ = $('#edit_inventory').val();
  
      $.post("<?php echo $this->basepath; ?>asset/admin/inventory_exist", {tipe: edit_tipe, edit: 1, lilo_id: edit_inventory_}, function(data){

        if(data == 'EMPTYTYPE'){
          alert('Type name should not be empty...');
          return false;
        } else if(data == '1'){	// name exists
          var override_confirm = confirm('Tipe ' + edit_tipe + ' sudah ada di server. Timpa dengan data yang baru?');
          if(!override_confirm){
            return false;
          } else {
            $("#inventory_edit_form").submit();
          }
        } else if(data == '0'){
          $("#inventory_edit_form").submit();
        } else {
					alert(data);
				}
      });
    });
    
    $('.inventory_edit').live('click', function(){
      var _id = $(this).attr('id');
      var _id_split = _id.split('_');
      
      $.post("<?php echo $this->basepath; ?>asset/admin/inventory", {detail:_id_split[1]}, function(data){
        var inventory = eval('('+data+')');
//  ubah value: edit_inventory, edit_tipe, edit_description, edit_icon
        $('#edit_inventory').val(inventory.lilo_id);
        $('#edit_tipe').val(inventory.tipe);
        $('#edit_description').val(inventory.description);
        
        $('#inventory_dialog').dialog('open');
      });
      
      
    });
    
    $('.inventory_delete').live('click', function(){
      var _id = $(this).attr('id');
      var _id_split = _id.split('_');
      
//      alert(_id_split[1]);
      if(confirm('Are you sure to delete this inventory item?')){
        $.post("<?php echo $this->basepath; ?>asset/admin/inventory", {delete:_id_split[1]}, function(data){
          if(data == "1"){
            $('#tr_' + _id_split[1]).hide('slow');
          }
        });
        
      }
      
      
      
    });
    
  });
  
</script>

<div class="centered transbg" style="width:960px; height:540px; border:none;">
	<div style="float:left; width:960px; height:80px;">
    
  </div>

  <div id="tabs" style="float:left; width:960px;">
    <ul>
      <li><a href="#tabs-1">Inventory List</a></li>
      <li><a href="#tabs-2">New Inventory Item</a></li>
    </ul>
    <div id="tabs-1">
      <table class="input_form" style="width:100%">
        <tr>
          <th style="width:30px;">No</th>
          <th>Tipe</th>
          <th>Icon</th>
          <th>Description</th>
          <th>Operation</th>
        </tr>
        
				<?php
				$inventory_array = $this->inventory_array;
				$no = 0;
				while($curr = $inventory_array->getNext()){
					$no++;
				?>
        <tr id="tr_<?php echo $curr['lilo_id']; ?>">
          <td style="text-align:right"><?php echo $no; ?></td>
          <td><?php echo $curr['tipe']; ?></td>
          <td style="text-align:center"><img class="viewicon" style="cursor:pointer; max-height:40px; max-width:40px;" src="<?php echo $this->basepath . $curr['icon']; ?>" /></td>
          <td><?php echo $curr['description']; ?></td>
          <td style="text-align:center">
            <a class="inventory_edit" id="inventoryedit_<?php echo $curr['lilo_id']; ?>">Edit</a>
            &nbsp;
            <a class="inventory_delete" id="inventorydelete_<?php echo $curr['lilo_id']; ?>">Delete</a>
          </td>
        </tr>
				<?php
				}
				?>
        
      </table>
    </div>
    <div id="tabs-2">
      <form id="inventory_add_form" class="input_form" method="post" enctype="multipart/form-data" action="<?php echo $this->basepath; ?>asset/admin/inventory">
        <input type="hidden" name="new_inventory" value="1" />
        <table style="width:95%">
          <tr>
            <td>Type</td>
            <td><input type="text" size="40" name="tipe" id="tipe" class="light_shadow transparent_70" placeholder="Type" /></td>
          </tr>
          <tr>
            <td>Description</td>
            <td><input type="text" style="width:80%" name="description" id="description" class="light_shadow transparent_70" placeholder="Description" /></td>
          </tr>
          <tr>
            <td>Icon</td>
            <td>
              <input type="file" name="icon" id="icon" class="light_shadow transparent_70" placeholder="Icon" />
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
  <form id="inventory_edit_form" class="input_form" method="post" enctype="multipart/form-data" action="<?php echo $this->basepath; ?>asset/admin/inventory">
    <input type="hidden" name="edit_inventory" id="edit_inventory" value="" />
    <table style="width:95%">
      <tr>
        <td>Type</td>
        <td><input type="text" size="40" name="edit_tipe" id="edit_tipe" class="light_shadow transparent_70" placeholder="Type" /></td>
      </tr>
      <tr>
        <td>Description</td>
        <td><input type="text" style="width:80%" name="edit_description" id="edit_description" class="light_shadow transparent_70" placeholder="Description" /></td>
      </tr>
      <tr>
        <td>Icon</td>
        <td>
          <input type="file" name="edit_icon" id="edit_icon" class="light_shadow transparent_70" placeholder="Icon" />
          <br>
          (Kosongkan jika tidak ingin mengubah icon yang sudah ada)
        </td>
      </tr>
      
      <tr>
        <td colspan="2" style="text-align:center">
          <input type="button" id="submit_edit_form" value="Submit" class="light_shadow transparent_70" />&nbsp;<input type="reset" value="Reset" />
        </td>
      </tr>
    </table>
  </form>
</div>


<div style="width: auto; min-height: 200px; max-height: 400px; min-width:400px; overflow-y:auto; " class="ui-dialog-content ui-widget-content" id="viewicon_dialog" title="View Icon">

</div>