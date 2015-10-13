<?php
//$this->server_detail;
?>

<table class="input_form">
  <tr>
  	<td>Name</td>
    <td><input type="text" style="width:90%" name="server_detail_name" id="server_detail_name" value="<?php echo $this->server_detail['name']; ?>" /></td>
  </tr>
  <tr>
  	<td>IP</td>
    <td><input type="text" size="15" name="server_detail_ip" id="server_detail_ip" value="<?php echo $this->server_detail['ip']; ?>" /></td>
  </tr>
  <tr>
  	<td>Port</td>
    <td><input type="text" size="5" name="server_detail_port" id="server_detail_port" value="<?php echo $this->server_detail['port']; ?>" /></td>
  </tr>
  <tr>
  	<td>Maximum CCU</td>
    <td><input type="text" size="5" name="server_detail_max_ccu" id="server_detail_max_ccu" value="<?php echo $this->server_detail['max_ccu']; ?>" /></td>
  </tr>
</table>