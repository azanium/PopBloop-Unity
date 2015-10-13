<style>
  .table_report{
    width: 100%;
    border: 0;
		text-align: center;
  }
	
	.table_report td, .table_report th{
    border: 1px solid #ccc;
	}
	
	tr:nth-child(even) {background: #CCC}
	tr:nth-child(odd) {background: #FFF}
</style>

<input type="hidden" name="countcurrentplayer" id="countcurrentplayer" value="" />

<script language="javascript">
	$(document).ready(function(){
    
    function loadPlayer(){
      $.post("<?php echo $this->basepath; ?>user/admin/wscountcurrentplayer", {}, function(data){
        // alert(data);
        $('#countcurrentplayer').val(data);
      });
			
      $.post("<?php echo $this->basepath; ?>user/admin/wscurrentplayer", {}, function(data){
        // alert(data);
        
				var countcurrentplayer = $('#countcurrentplayer').val();
				
        var _data = eval('(' + data + ')');
        var _html = '<table class="table_report">';
				_html += '<tr><td colspan="7" style="height:100px; font-size:96px;">' + countcurrentplayer + '</td></tr>';
        _html += '<tr>';
        _html += '<th>Avatar Name</th>';
        _html += '<th>Full Name</th>';
        _html += '<th>Email</th>';
        _html += '<th>Twitter</th>';
        _html += '<th>Handphone</th>';
        _html += '<th>Room</th>';
        _html += '<th>Start Time</th>';
        _html += '</tr>';
        
        for(var _idx = 0; _idx < _data.length; _idx++){
          // alert(_data[_idx].username);
          _html += '<tr>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].avatarname) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].fullname) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].email) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].twitter) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].handphone) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].room) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].datetime) + '</td>';
          _html += '</tr>';
        }
        _html += '</table>';
        
        $('#tabs-1').html(_html);
        
      });
    }
    
    
		
    loadPlayer();
    
		
		setInterval(loadPlayer, 10000);
		// Tabs
		$('#tabs').tabs();
		
	});
</script>

<div class="centered transbg" style="width:960px; height:540px; border:none;">
	<div style="float:left; width:960px; height:80px;">

  </div>

  <div id="tabs" style="float:left; width:960px;">
    <ul>
      <li><a href="#tabs-1">Current Players</a></li>
    </ul>
    <div id="tabs-1">
      
    </div>
  </div>

</div>

