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
<script language="javascript">
	$(document).ready(function(){
    
    function loadPlayer(keyword){
      $.post("<?php echo $this->basepath; ?>quest/admin/wssearchplayer/" + keyword, {}, function(data){
        // alert(data);
        
        var _data = eval('(' + data + ')');
        var _html = '<table class="table_report">';
        _html += '<tr>';
        _html += '<th>User Name</th>';
        _html += '<th>Full Name</th>';
        _html += '<th>Email</th>';
        _html += '<th>Twitter</th>';
        _html += '<th>Handphone</th>';
        _html += '<th>Completed Quest</th>';
        _html += '<th>Current Quest</th>';
        _html += '</tr>';
        
        for(var _idx = 0; _idx < _data.length; _idx++){
          // alert(_data[_idx].username);
          _html += '<tr>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].username) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].fullname) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].email) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].twitter) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].handphone) + '</td>';
          _html += '<td><a class="completedquest" id="completedquest_'+ $.trim(_data[_idx].lilo_id) +'">View</a></td>';
          _html += '<td><a class="currentquest" id="currentquest_'+ $.trim(_data[_idx].lilo_id) +'">View</a></td>';
          _html += '</tr>';
        }
        _html += '</table>';
        
        $('#tabs-1').html(_html);
        
      });
    }
    
    function loadQuest(keyword){
      $.post("<?php echo $this->basepath; ?>quest/admin/wssearchquest/" + keyword, {}, function(data){
        // alert(data);
        
        var _data = eval('(' + data + ')');
        var _html = '<table class="table_report">';
        _html += '<tr>';
        _html += '<th>ID</th>';
        _html += '<th>Quest</th>';
        _html += '<th>Start Date</th>';
        _html += '<th>End Date</th>';
        _html += '<th>Finished Player</th>';
        _html += '<th>Current Player</th>';
        _html += '</tr>';
        
        for(var _idx = 0; _idx < _data.length; _idx++){
          // alert(_data[_idx].Description);
          _html += '<tr>';
          _html += '<td style="text-align:center;">' + $.trim(_data[_idx].ID) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].Description) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].StartDate) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].EndDate) + '</td>';
          _html += '<td><a class="finishedplayer" id="finishedplayer_' + $.trim(_data[_idx].ID) + '">View</a>&nbsp;|&nbsp;<a style="text-decoration:none;" href="<?php echo $this->basepath; ?>quest/admin/wsquesttoplayer/csv_completed/' + $.trim(_data[_idx].ID) + '">Download</a></td>';
          _html += '<td><a class="currentplayer" id="currentplayer_' + $.trim(_data[_idx].ID) + '">View</a></td>';
          _html += '</tr>';
        }
        _html += '</table>';
        
        $('#tabs-2').html(_html);
        
      });
    }
    
    loadPlayer('');
    loadQuest('');
    
		// Tabs
		$('#tabs').tabs();
		
		$("#dialog").dialog({
			autoOpen: false, 
			minWidth: 1024, 
			minHeight: 400,
			modal: true,
			buttons: [
				//{
				//	text: "Save",
				//	click: function() { 
				//		
				//	}
				//},
				
				{
					text: "Close",
					click: function() { $(this).dialog("close"); }
				},
				
			]
		});
    
    $('.completedquest').live('click', function(){
      var _id = $(this).attr('id');
      
      var _id_split = _id.split('_');;
      
      // alert(_id_split[1]);
      
      $.post("<?php echo $this->basepath; ?>quest/admin/wsplayertoquest/completed/" + _id_split[1], {}, function(data){// alert(data);
        var _data = eval('(' + data + ')');
        //alert(_data);
        var _html = '<table class="table_report">';
        _html += '<tr>';
        _html += '<th>Quest ID</th><th>Description</th><th>Start Date</th><th>End Date</th><th>Duration</th><th>Operation</th>';
        _html += '</tr>';
        // alert(_data[0].Description);
        for(var _idx = 0; _idx < _data.length; _idx++){
          // alert(_data[_idx].Description);
          _html += '<tr id="rowundoneplayerquest_' + $.trim(_data[_idx].questid) + '_' + $.trim(_data[_idx].userid) + '">';
          _html += '<td style="text-align:center;">' + $.trim(_data[_idx].questid) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].Description) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].start_date) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].end_date) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].duration) + '</td>';
          _html += '<td style="text-align:center;"><a class="undoneplayerquest" id="undoneplayerquest_' + $.trim(_data[_idx].questid) + '_' + $.trim(_data[_idx].userid) + '">Undone</a></td>';
          _html += '</tr>';
        }
        _html += '</table>';
        // alert(_html);
        $('#dialog').html(_html);
				
				
        $('#dialog').attr('title', 'Completed Quest');
        $('#dialog').dialog('open');
        
      });
    });
		
    $('.currentquest').live('click', function(){
      var _id = $(this).attr('id');
      
      var _id_split = _id.split('_');;
      
      // alert(_id_split[1]);
      
      $.post("<?php echo $this->basepath; ?>quest/admin/wsplayertoquest/current/" + _id_split[1], {}, function(data){// alert(data);
        var _data = eval('(' + data + ')');
        //alert(_data);
        var _html = '<table class="table_report">';
        _html += '<tr>';
        _html += '<th>Quest</th>';
        _html += '</tr>';
        // alert(_data[0].Description);
        for(var _idx = 0; _idx < _data.length; _idx++){
          // alert(_data[_idx].Description);
          _html += '<tr>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].Description) + '</td>';
          _html += '</tr>';
        }
        _html += '</table>';
        // alert(_html);
        $('#dialog').html(_html);
				
        $('#dialog').attr('title', 'Current Quest');
        $('#dialog').dialog('open');
        
      });
    });
		
		
		$('.finishedplayer').live('click', function(){
      var _id = $(this).attr('id');
      
      var _id_split = _id.split('_');;
      
      // alert(_id_split[1]);
      
      $.post("<?php echo $this->basepath; ?>quest/admin/wsquesttoplayer/completed/" + _id_split[1], {}, function(data){ // alert(data);
        var _data = eval('(' + data + ')');
        //alert(_data);
        var _html = '<table class="table_report">';
        _html += '<tr>';
        _html += '<th>User Name</th><th>Full Name</th><th>Handphone</th><th>Email</th><th>Twitter</th><th>Start Date</th><th>End Date</th><th>Duration</th><th>Operation</th>';
        _html += '</tr>';
        // alert(_data[0].Description);
        for(var _idx = 0; _idx < _data.length; _idx++){
          // alert(_data[_idx].Description);
//          _html += '<tr>';
          _html += '<tr id="rowundoneplayerquest_' + $.trim(_data[_idx].questid) + '_' + $.trim(_data[_idx].userid) + '">';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].username) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].fullname) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].handphone) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].email) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].twitter) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].start_date) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].end_date) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].duration) + '</td>';
//          _html += '<td style="text-align:center;">Undone</td>';
          _html += '<td style="text-align:center;"><a class="undoneplayerquest" id="undoneplayerquest_' + $.trim(_data[_idx].questid) + '_' + $.trim(_data[_idx].userid) + '">Undone</a></td>';
          _html += '</tr>';
        }
        _html += '</table>';
        // alert(_html);
        $('#dialog').html(_html);
        
        $('#dialog').attr('title', 'Players Completed this Quest');
        $('#dialog').dialog('open');
        
      });
		});
		
		
    $('.currentplayer').live('click', function(){
      var _id = $(this).attr('id');
      
      var _id_split = _id.split('_');;
      
      // alert(_id_split[1]);
      
      $.post("<?php echo $this->basepath; ?>quest/admin/wsquesttoplayer/current/" + _id_split[1], {}, function(data){// alert(data);
        var _data = eval('(' + data + ')');
        //alert(_data);
        var _html = '<table class="table_report">';
        _html += '<tr>';
        _html += '<th>User Name</th><th>Full Name</th><th>Start Time</th>';
        _html += '</tr>';
        // alert(_data[0].Description);
        for(var _idx = 0; _idx < _data.length; _idx++){
          // alert(_data[_idx].Description);
          _html += '<tr>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].username) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].fullname) + '</td>';
          _html += '<td style="text-align:left;">' + $.trim(_data[_idx].datetime) + '</td>';
          _html += '</tr>';
        }
        _html += '</table>';
        // alert(_html);
        $('#dialog').html(_html);
				
        $('#dialog').attr('title', 'Players currently playing this quest');
        $('#dialog').dialog('open');
        
      });
    });
		
		
		$('.undoneplayerquest').live('click', function(){
			if(!confirm('Undone quest for this player?')){
				return;
			}
			
			// undoneplayerquest_1_4df6e7192cbfd4e6c000fd9b
			var _id = $(this).attr('id');
			var _id_split = _id.split('_');
			
			var questid = _id_split[1];
			var userid = _id_split[2];
			
			$.post("<?php echo $this->basepath; ?>quest/admin/undoneplayerquest/" + questid + "/" + userid, {}, function(data){
				if($.trim(data) == 'OK'){
					$('#row' + _id).hide();
				} else {
					alert('An error occured while deleting data. Error message: ' + data);
				}
			});
			
		});
		
		
		
  function loadQuestPerUser(){
    $.post("<?php echo $this->basepath; ?>report/admin/wsusercountquest", {}, function(data){
      var data_ = eval('(' + data + ')');
      
      var html = '';
      for(var idx = 4; idx >= 1; idx--){
		    html += '<table style="width:100%; border:0; text-align:left;" >';
        html += '<tr><th colspan="5">Player yang selesaikan ' + idx + ' Quest</th></tr>';
        html += '<tr><th style="text-align:center;">No</th><th style="text-align:center;">Full Name</th><th style="text-align:center;">Email</th><th style="text-align:center;">Avatar Name</th><th style="text-align:center;">Operation</th></tr>';
        
        var c_ = data_['c_' + idx];
        for(var i = 0; i < c_.length; i++){
          var no = i + 1;
          html += '<tr>';
          
          html += '<td style="width:50px; text-align:center;">' + no + '</td>';
          html += '<td style="width:250px;">' + c_[i]['fullname'] + '</td>';
          html += '<td style="width:250px;">' + c_[i]['email'] + '</td>';
          html += '<td>' + $.trim(c_[i]['avatarname']) + '</td>';
          html += '<td style="text-align:center;width:250px;"><a class="detailcountquest" id="detailcountquest_' + c_[i]['lilo_id'] + '">Detail</a></td>';
          
          html += '</tr>';
        }
				html += '<tr><td colspan="5" style="height:10px; background:#fff;">&nbsp;</td></tr>';
	      html += '</table>';
      }
      
      
      $('#tabs-3').html(html);


      var html = '';
      for(var idx = 4; idx >= 1; idx--){
		    html += '<table style="width:100%; border:0; text-align:left;" >';
        html += '<tr><th colspan="5">Player yang masih memainkan ' + idx + ' Quest</th></tr>';
        html += '<tr><th style="text-align:center;">No</th><th style="text-align:center;">Full Name</th><th style="text-align:center;">Email</th><th style="text-align:center;">Avatar Name</th><th style="text-align:center;">Operation</th></tr>';
        
        var c_ = data_['cur_' + idx];
        for(var i = 0; i < c_.length; i++){
          var no = i + 1;
          html += '<tr>';
          
          html += '<td style="width:50px; text-align:center;">' + no + '</td>';
          html += '<td style="width:250px;">' + c_[i]['fullname'] + '</td>';
          html += '<td style="width:250px;">' + c_[i]['email'] + '</td>';
          html += '<td>' + $.trim(c_[i]['avatarname']) + '</td>';
          html += '<td style="text-align:center;width:250px;"><a class="detailcountactivequest" id="detailcountactivequest_' + c_[i]['lilo_id'] + '">Detail</a></td>';
          
          html += '</tr>';
        }
				html += '<tr><td colspan="5" style="height:10px; background:#fff;">&nbsp;</td></tr>';
	      html += '</table>';
      }
      
      
      $('#tabs-4').html(html);

      
    });

  }

	
	loadQuestPerUser();
		
		
	});
</script>

<div class="centered transbg" style="width:1200px; height:540px; border:none;">
	<div style="float:left; width:1200px; height:80px;">

  </div>

  <div id="tabs" style="float:left; width:1200px;">
    <ul>
      <li><a href="#tabs-1">Players</a></li>
      <li><a href="#tabs-2">Quests</a></li>
      <li><a href="#tabs-3">Completed Quest per Player</a></li>
      <li><a href="#tabs-4">Active Quest per Player</a></li>
    </ul>
    <div id="tabs-1">
      
    </div>
    <div id="tabs-2">
      
    </div>
    <div id="tabs-3">
      
    </div>
    <div id="tabs-4">
      
    </div>
  </div>

</div>

<div style="width: auto; min-height: 58.4px; max-height: 540px; min-width:1024px; overflow-y:auto; " class="ui-dialog-content ui-widget-content" id="dialog" title=""></div>
