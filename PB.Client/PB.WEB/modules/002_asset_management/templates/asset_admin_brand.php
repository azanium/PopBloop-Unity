<script src="<?php echo $this->basepath; ?>libraries/js/jquery_placeholder.js"></script>
<script language="javascript">
$(document).ready(function(){
		$('#tabs').tabs();
                $("#brand_add_form").submit(function(){
                   if($('#add_id').val()=='')
                   {
                       $('#add_id').focus();
                       alert('Brand ID is Required');
                   }
                   else if($('#add_name').val()=='')
                   {
                       $('#add_name').focus();
                       alert('Brand Name is Required');
                   }
                   else
                   {
                        var url=$("#brand_add_form").attr('action');
                        var datapost=$("#brand_add_form").serialize()+"&json=true";
                        $.post(url, datapost, function(data){
                            $('#tabs').tabs({ selected: 0 });
                            $('#add_name').val('');
                            $("#listdatabrand").html(data);
                        });      
                   }
                    return false;
                });                
});
function functionhapus(dataid)
{
    $.post("<?php echo $this->basepath; ?>asset/admin/brand/delete", {"_id":dataid,"json":"true"}, function(data){
       $("#listdatabrand").html(data);
    });
}
function functiongetdetail(dataid)
{
    $.post("<?php echo $this->basepath; ?>asset/admin/brand/detail", {"_id":dataid,"json":"true"}, function(data){
            $('#edit_name').val(data['name']);
            $('#edit_id').val(data['brand_id']);
            $('#id').val(data['_id']);
            $("#brand_dialog").dialog({
			autoOpen: true, 
			minWidth: 450, 
			minHeight: 120,
                        buttons: [
				{
					text: "Save",
					click: function() {
                                            if($('#edit_id').val()=='')
                                            {
                                                $('#edit_id').focus();
                                                alert('Brand ID is Required');
                                            }
                                            else if($('#edit_name').val()=='')
                                            {
                                                $('#edit_name').focus();
                                                alert('Brand Name is Required');
                                            }
                                            else
                                            {
                                                 var url=$("#brand_edit_form").attr('action');
                                                 var datapost=$("#brand_edit_form").serialize()+"&json=true";
                                                 $.post(url, datapost, function(data){
                                                     $('#tabs').tabs({ selected: 0 });
                                                     $('#edit_name').val('');
                                                     $('#id').val('');
                                                     $("#listdatabrand").html(data);
                                                 });      
                                                 $(this).dialog("close");
                                            }	
                                            return false;
					}
				},				
				{
					text: "Cancel",
					click: function() { $(this).dialog("close"); }
				},				
			]
		});
    },"json");
}
</script>
<div class="centered transparent_70" style="width:960px; height:540px; border:none;">
    <div style="float:left; width:960px; height:80px;"></div>
    <div id="tabs" style="float:left; width:960px;">
        <ul>
          <li><a href="#tabs-1">List Brand!</a></li>
          <li><a href="#tabs-2">Add New Brand!</a></li>
        </ul>
        <div id="tabs-1">
            <div style="width:100%; text-align:left" id="listdatabrand">                
                <?php
                if($this->brand_array)
                {
                    echo "<table class='input_form' width='100%'>";
                    echo "<tr>";
                    echo "<th>Brand ID</th>";
                    echo "<th>Name</th>";
                    echo "<th>Action</th>";
                    echo "</tr>";
                    foreach($this->brand_array as $dt)
                    {
                        echo "<tr>";
                        echo "<td>".$dt['brand_id']."</td>";
                        echo "<td>".$dt['name']."</td>";
                        echo "<td>";
                        echo "<button onclick='functionhapus(\"".$dt['_id']."\");'>Delete</button>";
                        echo "<button onclick='functiongetdetail(\"".$dt['_id']."\");'>Edit</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }                
                ?>
            </div>
        </div>
    <div id="tabs-2">
        <form id="brand_add_form" class="input_form" method="post" action="<?php echo $this->basepath; ?>asset/admin/brand/add">
            <input type="hidden" size="40" name="id_name" id="id_name" />
        <table style="width:95%">
          <tr>
            <td>Brand ID</td>
            <td><input type="text" size="40" name="id_brand" id="add_id" class="light_shadow transparent_70" placeholder="Brand ID" /></td>
          </tr>
          <tr>
            <td>Brand Name</td>
            <td><input type="text" size="40" name="name" id="add_name" class="light_shadow transparent_70" placeholder="Brand Name" /></td>
          </tr>          
          <tr>
              <td colspan="2" style="text-align:center"><input type="submit" name="add_submit" id="add_submit" value="Save" /></td>
          </tr>
        </table>
        </form>
    </div>
    </div>
</div>
<div id="brand_dialog" style="width: auto; min-height: 200px; height: auto; min-width:600px;display: none;" class="ui-dialog-content ui-widget-content" id="brand_dialog" title="Detail Brand">
    <form id="brand_edit_form" class="input_form" method="post" enctype="multipart/form-data" action="<?php echo $this->basepath; ?>asset/admin/brand/edit">
        <input type="hidden" name="id" id="id" value="" />
        <table style="width:95%">
          <tr>
            <td>Brand ID</td>
            <td><input type="text" size="40" name="id_brand" id="edit_id" class="light_shadow transparent_70" placeholder="Brand ID" /></td>
          </tr>
          <tr>
            <td>Brand Name</td>
            <td><input type="text" size="40" name="name" id="edit_name" class="light_shadow transparent_70" placeholder="Brand Name" /></td>
          </tr> 
        </table>
    </form>
</div>