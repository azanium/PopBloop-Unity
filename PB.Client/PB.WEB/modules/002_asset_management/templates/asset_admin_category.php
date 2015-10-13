<script src="<?php echo $this->basepath; ?>libraries/js/jquery_placeholder.js"></script>
<script language="javascript">
$(document).ready(function(){
		$('#tabs').tabs();
                $("#brand_add_form").submit(function(){
                   if($('#add_name').val()=='')
                   {
                       $('#add_name').focus();
                       alert('Category Name is Required');
                   }
                   else
                   {
                        $("#showdt_type").val($("#editor_div_type").val());
                        var url=$("#brand_add_form").attr('action');
                        var datapost=$("#brand_add_form").serialize()+"&showdt="+$("#showdt_type").val()+"&json=true";
                        $.post(url, datapost, function(data){
                            $('#tabs').tabs({ selected: 0 });
                            $('#add_name').val('');
                            $("#listdatabrand").html(data);
                        });      
                   }
                    return false;
                });    
                $("#showdt_type").change(function(){
                    $.post("<?php echo $this->basepath; ?>asset/admin/category/", {showdt:$("#showdt_type").val(),json:true}, function(data){
                            $('#tabs').tabs({ selected: 0 });
                            $('#add_name').val('');
                            $("#listdatabrand").html(data);
                        });
                });
});
function functionhapus(dataid)
{
    $.post("<?php echo $this->basepath; ?>asset/admin/category/delete", {"_id":dataid,"json":"true"}, function(data){
       $("#listdatabrand").html(data);
    });
}
function functiongetdetail(dataid)
{
    $.post("<?php echo $this->basepath; ?>asset/admin/category/detail", {"_id":dataid,"json":"true"}, function(data){
            $('#edit_name').val(data['name']);
            $('#id').val(data['_id']);
            $('#editor_edit_type').val(data['tipe']);
            $("#brand_dialog").dialog({
			autoOpen: true, 
			minWidth: 450, 
			minHeight: 120,
                        buttons: [
				{
					text: "Save",
					click: function() {
                                            if($('#edit_name').val()=='')
                                            {
                                                $('#edit_name').focus();
                                                alert('Category Name is Required');
                                            }
                                            else
                                            {
                                                 $("#showdt_type").val($("#editor_edit_type").val());
                                                 var url=$("#brand_edit_form").attr('action');
                                                 var datapost=$("#brand_edit_form").serialize()+"&showdt="+$("#showdt_type").val()+"&json=true";
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
          <li><a href="#tabs-1">List Category!</a></li>
          <li><a href="#tabs-2">Add New Category!</a></li>
        </ul>
        <div id="tabs-1">
            <select name="showdt_type" id="showdt_type">
                    <option value=''>All data show</option>
                    <?php
                        foreach($this->tipe_array as $result)
                        {
                          echo "<option value='".$result['name']."'>".$result['name']."</option>";
                        }
                    ?>
            </select>
            <div style="width:100%; text-align:left" id="listdatabrand">                
                <?php
                if($this->category_array)
                {
                    echo "<table class='input_form' width='100%'>";
                    echo "<tr>";
                    echo "<th>Name</th>";
                    echo "<th>Avatar Body Part Type</th>";
                    echo "<th>Action</th>";
                    echo "</tr>";
                    foreach($this->category_array as $dt)
                    {
                        echo "<tr>";
                        echo "<td>".$dt['name']."</td>";
                        echo "<td>".$dt['tipe']."</td>";
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
        <form id="brand_add_form" class="input_form" method="post" action="<?php echo $this->basepath; ?>asset/admin/category/add">
            <input type="hidden" size="40" name="id_name" id="id_name" />
        <table style="width:95%">
          <tr>
            <td>Avatar Body Part</td>
            <td>
                <select name="editor_div_type" id="editor_div_type">
                    <?php
                        foreach($this->tipe_array as $result)
                        {
                          echo "<option value='".$result['name']."'>".$result['name']."</option>";
                        }
                    ?>
                  </select>
            </td>
          </tr>
          <tr>
            <td>Category Name</td>
            <td><input type="text" size="40" name="name" id="add_name" class="light_shadow transparent_70" placeholder="Category Name" /></td>
          </tr>          
          <tr>
              <td colspan="2" style="text-align:center"><input type="submit" name="add_submit" id="add_submit" value="Save" /></td>
          </tr>
        </table>
        </form>
    </div>
    </div>
</div>
<div id="brand_dialog" style="width: auto; min-height: 200px; height: auto; min-width:600px;display: none;" class="ui-dialog-content ui-widget-content" id="brand_dialog" title="Detail Category">
    <form id="brand_edit_form" class="input_form" method="post" enctype="multipart/form-data" action="<?php echo $this->basepath; ?>asset/admin/category/edit">
        <input type="hidden" name="id" id="id" value="" />
        <table style="width:95%">
          <tr>
            <td>Avatar Body Part</td>
            <td>
                <select name="editor_div_type" id="editor_edit_type">
                    <?php
                        foreach($this->tipe_array as $result)
                        {
                          echo "<option value='".$result['name']."'>".$result['name']."</option>";
                        }
                    ?>
                  </select>
            </td>
          </tr>
          <tr>
            <td>Category Name</td>
            <td><input type="text" size="40" name="name" id="edit_name" class="light_shadow transparent_70" placeholder="Brand Name" /></td>
          </tr> 
        </table>
    </form>
</div>