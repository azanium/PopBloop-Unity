
<script language="javascript">
	$(document).ready(function(){

		// Tabs
		$('#tabs').tabs();


	});
</script>

<div class="centered transbg" style="width:960px; height:540px; border:none;">
	<div style="float:left; width:960px; height:80px;">

  </div>

  <div id="tabs" style="float:left; width:960px;">
    <ul>
      <li><a href="#tabs-1">Admin List</a></li>
      <li><a href="#tabs-2">Add New Admin</a></li>
    </ul>
    <div id="tabs-1">
      <table class="input_form" style="width:100%">
        <tr>
          <th style="width:30px;">No</th>
          <th>Name</th>
          <th>Last Login</th>
          <th>Operation</th>
        </tr>
      
      <?php
		  $admin_data_cursor = $this->admin_data_cursor;
      $no = 0;
      while($curr = $admin_data_cursor->getNext()){
        $no++;
      ?>
      
        <tr>
          <td style="width:30px;"><?php echo $no; ?></td>
          <td><?php echo $curr['fullname']; ?></td>
          <td><?php echo $curr['username']; ?></td>
          <td><?php echo "preview"; ?></td>
          <td>
              <?php
                $detail_div = '';
                foreach($curr['assets'] as $key => $val){
              ?>
                  &bull;&nbsp;<?php echo $key . " - " . $val['objectName'];?><br />
              <?php
                  $detail_div .= "<div class='detail_div' style='display:none' id='detail_div_".$no."'>
                                    &bull;&nbsp;$key - ".$val['objectName']."<br />
                                    Position: ". $val['position_x'] .", ". $val['position_y'] .", ". $val['position_z'] ."<br />
                                    Rotation: ". $val['rotation_x'] .", ". $val['rotation_y'] .", ". $val['rotation_z'] ."<br />
                                  </div>";
                }
              ?>
          </td>
          <td style="text-align:center">
            <a class="level_download" id="level_download_<?php echo $no; ?>">Download</a>&nbsp;|&nbsp;
            <a class="level_delete" id="level_delete_<?php echo $no; ?>">Delete</a>
          </td>
        </tr>
      
      <?php
      }
      ?>
      
      </table>
    </div>
    <div id="tabs-2" style="max-height:200px;">
			<?php
      echo trim($this->add_level_form);
      ?>
    </div>
  </div>

</div>
