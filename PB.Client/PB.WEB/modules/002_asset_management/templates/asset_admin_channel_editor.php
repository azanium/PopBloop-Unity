<script language="javascript">
$(document).ready(function(){
	$(".delete_channel").click(function(){
		if(confirm('Anda yakin untuk menghapus data channel ini?')){
			var _id = $(this).attr('id');
			var _id_split = _id.split('_');
			
			$.post("<?php echo $this->basepath; ?>asset/admin/channel_delete/" + _id_split[1], {}, function(data){
				if(data == "1"){
					$("#tr_" + _id_split[1]).hide();
				} else {
					alert(data);
				}
			});
		}
		
	});
});
</script>

<table class="input_form" style="width:98%">
	<tr>
  	<th>No</th>
  	<th>Channel Name</th>
  	<th>Operation</th>
  </tr>
	<?php
	$no = 1;
  foreach($this->channels_array as $ca){
  ?>
  <tr id="tr_<?php echo $ca['lilo_id']; ?>">
  	<td style="text-align:center;"><?php echo $no; ?></td>
    <td>
    	<input type="text" style="width:90%" name="channel_editor_name" id="channel_editor_name" value="<?php echo $ca['name']; ?>" />
    </td>
    <td style="text-align:center;">
    	<a style="cursor:pointer;" class="delete_channel" id="id_<?php echo $ca['lilo_id']; ?>">Delete</a>
    </td>
  </tr>
	<?php
		$no++;
  }
  ?>
</table>