<div class="withjs">

<script language="javascript">
	$(document).ready(function(){
		$('#btn_shout').live('click', function(){
			var shout = $('#shout').val();alert("Your shout: " + shout);
			var session_id = $('#session_id').val();
			var circle = $('#circle').val();
			
			$.post("<?php print($this->basepath); ?>message/guest/shout", {shout: shout, session_id: session_id, circle: circle}, function(data){
				if($.trim(data) == "OK"){
					alert(data + " - Shout shouted :)");
				} else {
					alert(data + " - Shout ga ke shout :( ");
				}
			});
		});
	});
</script>

<div class="container_12 pop_20_spacer">
</div>

<div class="container_12">
  <div class="grid_12">
  	<table style="width:100%; border:0;">
    	<tr>
      	<td style="width:300px">Shout</td>
      	<td>
        	<textarea name="shout" id="shout" style="width:635px;"></textarea>
        </td>
      </tr>
    	<tr>
      	<td style="width:300px">Session ID</td>
      	<td><input type="text" name="session_id" id="session_id" /></td>
      </tr>
    	<tr>
      	<td style="width:300px">Circle</td>
      	<td><input type="text" name="circle" id="circle" /></td>
      </tr>
      <tr>
      	<td colspan="2" style="text-align:center">
        	<input style="width:200px;" type="button" id="btn_shout" name="btn_shout" value="Shout!" />
        </td>
      </tr>
    </table>
	</div>
</div>

</div><!--[ end withjs ]-->