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


    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});
    </script>
    <script type="text/javascript">
//			var array_data = [
//          ['Year', 'Austria', 'Belgium', 'Czech Republic', 'Finland', 'France', 'Germany'],
//          ['2003',  1336060,   3817614,       974066,       1104797,   6651824,  15727003],
//          ['2004',  1538156,   3968305,       928875,       1151983,   5940129,  17356071],
//          ['2005',  1576579,   4063225,       1063414,      1156441,   5714009,  16716049],
//          ['2006',  1600652,   4604684,       940478,       1167979,   6190532,  18542843],
//          ['2007',  1968113,   4013653,       1037079,      1207029,   6420270,  19564053],
//          ['2008',  1901067,   6792087,       1037327,      1284795,   6240921,  19830493]
//        ];
			// 'Jakcloth Island Hari 4'
			// [['7_6_2012', '7_7_2012', '7_8_2012'],
			//	['124', '221', '232']]



		$(document).ready(function(){
			function is__Array(obj) {
				// do an instanceof check first
				if (obj instanceof Array) {
					return true;
				}
				// then check for obvious falses
				if (typeof obj !== 'object') {
					return false;
				}
				if (utils.type(obj) === 'array') {
					return true;
				}
				return false;
			};
			
			function loadChart(){
				$.post("<?php echo $this->basepath; ?>user/admin/wsroomstatrangedate", {}, function(data){
					var _data = eval('(' + data + ')');
					// console.log(_data.Jakcloth__space__Island__space__Hari__space__4); // Object { 7_8_2012={...}, 7_9_2012={...}, 6_30_2012=null, more...}
					for (room_ in _data)
					{	// alert(room_);	// Jakcloth__space__Island__space__Hari__space__4
						var room = room_.split('__space__').join(' ');
						// console.log(room);	// Jakcloth Island Hari 4
						// console.log(_data[room_]);	// Object { 7_8_2012={...}, 7_9_2012={...}, 6_30_2012=null, more...}
						var cur_graph_data = _data[room_];
						// console.log(cur_graph_data);	// Object { 7_8_2012={...}, 7_9_2012={...}, 6_30_2012=null, more...}
						var header = ['Date', 'Unique Visit', 'Total Visit'];
						// console.log(header);	// ["Date", "Unique Visit", "Total Visit"]
						var body = [];
						body.push(header);
						
						$.each(cur_graph_data, function(i, n){
							// alert("I: " + i + ", N: " + n);	// I: 7_8_2012, N: [object Object]		=> Object { _id={...}, date="7/8/2012", room="Jakcloth Island Hari 4", more...}
																							// I: 7_10_2012, N: null
							console.log(n);
							
							if(typeof n !== 'undefined' && n !== null){
								//alert("n unique: " + n['unique_visit']);
								body.push([i.split('_').join('/'), n['unique_visit'], n['visit']]);
							} else {
								// alert("tipenya n: " + typeof n);
								body.push([i.split('_').join('/'), 0, 0]);
							}
							
						});

						// return;
						
						//console.log(cur_graph_data);
						//for(date_ in cur_graph_data){
						//	// console.log(cur_graph_data[date_]);
						//	var unq_vis = 0;
						//	var vis = 0;//alert('sdfsdf');
						//	if(typeof cur_graph_data[date_] != 'undefined'){
						//		unq_vis = parseInt(cur_graph_data[date_].unique_visit);
						//		vis = parseInt(cur_graph_data[date_].visit);
						//	} else {
						//	}
						//	
						//	body.push([date_.split('_').join('/'), unq_vis, vis]);
						//	
						//}
						
						// alert(body);
						var chart_data = google.visualization.arrayToDataTable(body);
						
						new google.visualization.ColumnChart(document.getElementById(room_)).
							draw(chart_data,
									 {title:"Visit Statistics - Room: " + room,
									 width:940, height: 400, hAxis: {title: "Date"}, vAxis: {title: "Visit"}}
							);
						
					}
					
				});
			}
			google.setOnLoadCallback(loadChart);
//			loadChart();
		});
			
//			var array_data = [];
//			
//			
//      function drawVisualization() {
//        // Create and populate the data table.
//        var data = google.visualization.arrayToDataTable(array_data);
//      
//        // Create and draw the visualization.
//        new google.visualization.ColumnChart(document.getElementById('result_room_chart')).
//            draw(data,
//                 {title:"Yearly Coffee Consumption by Country",
//                  width:940, height:400,
//                  hAxis: {title: "Year"}}
//            );
//      }
//      
//
//      google.setOnLoadCallback(drawVisualization);
    </script>



<input type="hidden" name="countcurrentplayer" id="countcurrentplayer" value="" />

<script language="javascript">
	$(document).ready(function(){
    // alert([['are1a', 'are1b'], ['are2a'], ['are3a', 'are3b', 'are3c']]);
//		function loadRoom(){
//			$.post("<?php echo $this->basepath; ?>user/admin/wsroomstat", {}, function(data){
//				var _data = eval('(' + data + ')');
//				var _html = '<table class="table_report">';
//        _html += '<tr>';
//        _html += '<th>No</th>';
//        _html += '<th>Room</th>';
//        _html += '<th>Unique Visit</th>';
//        _html += '<th>Visit</th>';
//        _html += '</tr>';
//				
//				for(var _idx = 0; _idx < _data.length; _idx++){
//					_html += '<tr>';
//					_html += '<td style="text-align:center;">' + $.trim(_data[_idx].no) + '</td>';
//					_html += '<td style="text-align:left;">' + $.trim(_data[_idx].room) + '</td>';
//					_html += '<td style="text-align:center;">' + $.trim(_data[_idx].unique_visit) + '</td>';
//					_html += '<td style="text-align:center;">' + $.trim(_data[_idx].visit) + '</td>';
//					_html += '</tr>';
//				}
//				
//				_html += '</table>';
//				$('#result_room').html(_html);
//			});
//			
//		}
		
		
    function loadPlayer(){
			$.post("<?php echo $this->basepath; ?>user/admin/wsplayerstat", {}, function(data){
				var _data = eval('(' + data + ')');
				var _html = '<table class="table_report">';
        _html += '<tr>';
        _html += '<th>No</th>';
        _html += '<th>User Name</th>';
        _html += '<th>Full Name</th>';
        _html += '<th>Avatar Name</th>';
        _html += '<th>Twitter</th>';
        _html += '<th>Room</th>';
        _html += '<th>Visit</th>';
        _html += '</tr>';
				
				for(var _idx = 0; _idx < _data.length; _idx++){
					_html += '<tr>';
					_html += '<td style="text-align:center;">' + $.trim(_data[_idx].no) + '</td>';
					_html += '<td style="text-align:left;">' + $.trim(_data[_idx].username) + '</td>';
					_html += '<td style="text-align:left;">' + $.trim(_data[_idx].fullname) + '</td>';
					_html += '<td style="text-align:left;">' + $.trim(_data[_idx].avatarname) + '</td>';
					_html += '<td style="text-align:left;">' + $.trim(_data[_idx].twitter) + '</td>';
					_html += '<td style="text-align:left;">' + $.trim(_data[_idx].room) + '</td>';
					_html += '<td style="text-align:left;">' + $.trim(_data[_idx].visit) + '</td>';
					_html += '</tr>';
				}
				
        _html += '</table>';
				$('#result_player').html(_html);
			});
			
    }
    
//		loadRoom();
    loadPlayer();
    
		
//		setInterval(loadRoom, 30000);
		setInterval(loadPlayer, 30000);
		// Tabs
		$('#tabs').tabs();
		
		// datepicker
		$('#date_room, #date_player').datepicker({dateFormat: 'm/d/yy'});
		
	});
</script>

<div class="centered transbg" style="width:960px; height:540px; border:none;">
	<div style="float:left; width:960px; height:80px;">

  </div>

  <div id="tabs" style="float:left; width:960px;">
    <ul>
      <li><a href="#tabs-1">Room Statistics</a></li>
      <li><a href="#tabs-2">Player Statistics</a></li>
    </ul>
    <div id="tabs-1">
			<div style="height: 400px;" id="result_room_chart">
				<div id="Jakcloth__space__Island__space__Hari__space__4"></div>
				<div id="PrivateRoom"></div>
				<div id="PopBloop__space__Farewell__space__Island"></div>
			</div>
      <div style="height: 80px; text-align: center;">
				Select Date:&nbsp;<input type="text" size="12" name="date_room" id="date_room" value="<?php echo date("n/j/Y"); ?>" />&nbsp;<input type="button" id="show_room" value="Show" />
			</div>
			<div id="result_room">
				
			</div>
    </div>
    <div id="tabs-2">
      <div style="height: 80px; text-align: center;">
				Select Date:&nbsp;<input type="text" size="12" name="date_player" id="date_player" value="<?php echo date("n/j/Y"); ?>" />&nbsp;<input type="button" id="show_player" value="Show" />
			</div>
			<div id="result_player">
				
			</div>
    </div>
  </div>

</div>

