<?php
// tab 1: daftar server [edit | delete]
// tab 2: add new server
// server:  name, port, ip, max CCU, current CCU* (*: updated realtime by game server on user connect/disconnect)
?>
<script src="<?php echo $this->basepath; ?>libraries/js/jquery_placeholder.js"></script>
<script language="javascript">
	$(document).ready(function(){

		// Tabs
		$('#tabs').tabs();

		// Tabs
		$('#tabs').tabs();
		$("#dialog").dialog({
			autoOpen: false, 
			minWidth: 820, 
			minHeight: 400,

			buttons: [
				{
					text: "Save",
					click: function() { 
						alert('save...'); 
						var level_detail_name = $("#level_detail_name").val();
						alert(level_detail_name);
					}
				},
				
				{
					text: "Cancel",
					click: function() { $(this).dialog("close"); }
				},
				
			]

		});


		$(".server_detail").click(function(){
			var _id = $(this).attr('id');
			var _id_split = _id.split('_');
			
			var server_id = _id_split[2];
			
			$.post("<?php echo $this->basepath; ?>server/admin/detail/" + server_id, {}, function(data){
				$('#dialog').html(data);
				$('#dialog').dialog('open');
				return false;
			});
			
		});
		
		$(".server_delete").click(function(){
			// dapatkan id
			var _id = $(this).attr('id');
//			alert(_id);
			var _id_split = _id.split('_');
			var server_id = _id_split[2];
			$.post("<?php echo $this->basepath; ?>server/admin/delete/" + server_id, {}, function(data){
				if(data == '1'){
					window.location.replace("<?php echo $this->basepath; ?>server/admin");
				} else {
					alert(data);
				}
			});
		});
		
	});
</script>

<div class="centered transbg" style="width:960px; height:540px; border:none;">
	<div style="float:left; width:960px; height:80px;">

  </div>

  <div id="tabs" style="float:left; width:960px;">
    <ul>
      <li><a href="#tabs-1">Game Server List</a></li>
      <li><a href="#tabs-2">Add New Game Server</a></li>
    </ul>
    <div id="tabs-1">
      <table class="input_form" style="width:100%">
        <tr>
          <th style="width:30px;">No</th>
          <th>Name</th>
          <th>IP:Port</th>
          <th>Current CCU / Max CCU</th>
          <th>Operation</th>
        </tr>
      
      <?php
		  $server_array = $this->server_array;
      $no = 0;
      while($curr = $server_array->getNext()){
        $no++;
      ?>
      
        <tr>
          <td style="width:30px;"><?php echo $no; ?></td>
          <td><?php echo $curr['name']; ?></td>
          <td><?php echo $curr['ip'] . ":" . $curr['port']; ?></td>
          <td><?php echo (int)$curr['current_ccu'] . ":" . $curr['max_ccu']; ?></td>
          <td style="text-align:center">
            <a class="server_detail" id="server_detail_<?php echo (string)$curr['_id']; ?>">Detail</a>&nbsp;|&nbsp;
            <a onclick="return confirm('Anda yakin untuk menghapus data server <?php echo $curr['name']; ?>?');" class="server_delete" id="server_delete_<?php echo (string)$curr['_id']; ?>">Delete</a>
          </td>
        </tr>
      
      <?php
      }
      ?>
      
      </table>
      <div style="height:40px">&nbsp;</div>
    </div>
    <div id="tabs-2" style="max-height:600px;">

      <div style="float:left; width:960px;">
        <form id="server_add_form" class="input_form" method="post" enctype="multipart/form-data" action="<?php echo $this->basepath; ?>server/admin/add">
          <input type="hidden" name="submitted" value="1" />
          <table style="width:95%">
            <tr>
              <td>Game Server Name</td>
              <td><input type="text" size="40" name="server_admin_add_name" id="server_admin_add_name" class="light_shadow transparent_70" placeholder="Game Server Name" /></td>
            </tr>
            <tr>
              <td>IP Address</td>
              <td><input type="text" size="15" name="server_admin_add_ip" id="server_admin_add_ip" class="light_shadow transparent_70" placeholder="IP Address" /></td>
            </tr>
            <tr>
              <td>Port</td>
              <td><input type="text" size="5" name="server_admin_add_port" id="server_admin_add_port" class="light_shadow transparent_70"  placeholder="Port Number" /></td>
            </tr>
            <tr>
              <td>Maximum CCU</td>
              <td><input type="text" size="40" name="server_admin_add_max_ccu" id="server_admin_add_max_ccu" class="light_shadow transparent_70" placeholder="Maximum CCU" /></td>
            </tr>
            
            <tr>
              <td colspan="2" style="text-align:center">
                <input type="submit" id="submit_form" value="Submit" class="light_shadow transparent_70" />&nbsp;<input type="reset" value="Reset" />
              </td>
            </tr>
            
          </table>
        </form>
      </div>

    </div>
  </div>

</div>

<div style="width: auto; min-height: 58.4px; height: auto; min-width:600px; " class="ui-dialog-content ui-widget-content" id="dialog" title="Game Server Detail"></div>
