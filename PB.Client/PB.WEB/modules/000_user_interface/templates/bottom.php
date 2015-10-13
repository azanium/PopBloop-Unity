<?php
if($this->logged_in){
/*
	scroll otomatis setiap update data di .chatwindowcontent
<script language="javascript" src="<?php echo $this->basepath; ?>libraries/js/jquery.smooth.scroll/jquery.smooth-scroll.js"></script>

      $('button.scrollsomething').click(function() {
        $.smoothScroll({
          scrollElement: $('div.scrollme'),	// div yg mau di scroll
          scrollTarget: '#findme'						// id element tujuan scroll
        });
        return false;
      });

	atau baca: http://stackoverflow.com/questions/2291645/jquery-div-autoscroll

*/

?>


<script language="javascript">
	$(document).ready(function(){
		
		$.post("<?php echo $this->basepath; ?>ui/user/getdisplayconfig", {key: 'friendlist_visible'}, function(data){
//			alert(data);
			if(data == 'visible'){
				$('.friendlist_bottom_content').show();
			} else if(data == 'notvisible'){
				$('.friendlist_bottom_content').hide();
			}
		});
		
//		var shown_id = ["4e38df26c1b4ba8c09000001"];
//		for(idx = 0; idx <= shown_id.length; idx++){
//			var _id = shown_id[idx];
//
//			var chatwindowexists = $('#chatwindow' + _id).length > 0;alert(chatwindowexists);
//			if(!chatwindowexists){
//				text = "<div class='chatwindow' id='chatwindow"+ _id +"'>";
//				text += "<div class='chatwindowtitle' id='chatwindowtitle"+ _id +"'><div style='text-align:center; float:left; min-width:180px;'>"+_id+"</div><div class='toggle_item_chat' id='showhidechatwindowcontent"+ _id +"'>[&bull;]</div></div>";
//				text += "<div class='chatwindowcontent transparent_70' id='chatwindowcontent"+ _id +"'></div>";
//				text += "<div class='chatwindowtext transparent_70' id='chatwindowtext"+ _id +"'><textarea class='chattextarea' id='chattextarea"+ _id +"' ></textarea></div>";
//				text += "</div>";
//				
//				$('.chatarea').append(text);
//			} else {
//				$('#chatwindow' + _id).show();
//			}
//		}
		
		$('.friendlist_bottom_header').live('click', function(){
			var friendlist_visible = $('.friendlist_bottom_content').is(":visible") ? 'notvisible' : 'visible';
			
			$('.friendlist_bottom_content').slideToggle('slow');
			
			// ui_user_displayconfig

			$.post("<?php echo $this->basepath; ?>ui/user/displayconfig", {key: 'friendlist_visible', val: friendlist_visible}, function(data){
//				alert(data);
			});
			
		});
		
		// dapatkan friend list
		// refresh setiap x seconds
		
		function loadFriendList(){
			$.post("<?php echo $this->basepath; ?>friend/user/ws_friendlist", {}, function(data){
				if(data.substr(0,16) == "##ACCESSDENIED##"){
					document.write(data);
					return true;
				}
	//			alert(data);
				/*
				[
					["Management",{"user_id":"4df6e7192cbfd4e6c000fd9b","friend_id":"4e38df26c1b4ba8c09000001","circle_array":["Management"],"approval_time":1318865716,"lilo_id":"4e9c4b3489b38f4c0b000000","email":"qurrota@m-stars.net","username":"qurrota","fullname":"Qurrota Ayun","foto":"default.png","foto_url":"http:\/\/localhost\/lilo3a\/user_generated_data\/profile_picture\/default.png"}],
				 
					["Outer Circle"]
				]
				*/
				
				
				var all_friends = eval('('+data+')');
				var text = '';
				for(idx = 0; idx < all_friends.length; idx++){
					var curr_circle = all_friends[idx][0];
					var curr_circle_no_space = curr_circle.replace(/\s/g, '__SPASI__')
					text += "<div class='friendlist_bottom_circle_name' id='div_"+curr_circle_no_space+"'>"+curr_circle+"</div>";
					
					// div list friend di curr_circle
					text += "<div class='friendlist_bottom_content' id='content_"+curr_circle_no_space+"'>";
					
					for(i = 1; i < all_friends[idx].length; i++){
						text += "<div class='friendlist_bottom_item' id='item_"+all_friends[idx][i]['friend_id']+"'>";
						
	//					text += "<img src='<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/icon.msg.10x10.png' >";
	//					text += "<img src='<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/icon.chat.10x10.png' >";
						text += all_friends[idx][i]['fullname'];
						
						text += "</div>";
	//					text += "<div class='friendlist_bottom_msg'></div>";
					}
					
					text += "</div>";
					
				}
				
				$('.friendlist_bottom_content').html(text);
	
			});
		}
		
		// saat halaman load, langsung tampilkan friend list
		loadFriendList();
		
		// fungsi yg merequest ke server secara berkala untuk menjaga apa yg ditampilkan di halaman ini tetap fresh
		function heartBeat(){
			loadFriendList();
		}
		
		setInterval(heartBeat, <?php echo isset($this->heartBeatInterval) ? $this->heartBeatInterval : "60000"; ?>);

		
		$('.friendlist_bottom_circle_name').live('click', function(){
			var _id = $(this).attr('id');
//			alert(_id);
			
			// dari _id, dapatkan id div yg harus di slideToggle
			var circle_name = $.trim(_id.substring(4));
//			alert(circle_name);
			$('#content_' + circle_name).slideToggle('slow');
			
		});
		
		
		$('.friendlist_bottom_item').live('click', function(){
			var friend_fullname = $(this).html();
//			alert("Lets chat with " + $(this).html());

			var _id = $(this).attr('id');
			//alert(_id);	// item_4e1df5f8c1b4bad817000001
			
			var chatwindowexists = $('#chatwindow' + _id).length > 0;
			if(!chatwindowexists){
				text = "<div class='chatwindow' id='chatwindow"+ _id +"'>";
				text += "<div class='chatwindowtitle' id='chatwindowtitle"+ _id +"'><div style='text-align:center; float:left; min-width:180px;'>"+friend_fullname+"</div><div class='toggle_item_chat' id='showhidechatwindowcontent"+ _id +"'>[&bull;]</div></div>";
				text += "<div class='chatwindowcontent transparent_70' id='chatwindowcontent"+ _id +"'></div>";
				text += "<div class='chatwindowtext transparent_70' id='chatwindowtext"+ _id +"'><textarea class='chattextarea' id='chattextarea"+ _id +"' ></textarea></div>";
				text += "</div>";
				
				$('.chatarea').append(text);
			} else {
				$('#chatwindow' + _id).show();
			}

		});
		
		
		$('.toggle_item_chat').live('click', function(){
			var _id = $(this).attr('id');
			
//			alert(_id);	// showhidechatwindowcontentitem_4e1df5f8c1b4bad817000001
			
			var _id_split = _id.split('_');
			$('#chatwindowitem_' + _id_split[1]).hide();
			
//			var _id_to_toggle = _id.substring(8)
//			alert(_id_to_toggle);
//			$('#' + _id_to_toggle).slideToggle();
			
		});
		
		$('.chattextarea').live('keypress', function(event){
			var _id = $(this).attr('id');	// chattextareaitem_4e1df5f8c1b4bad817000001
			var _id_split = _id.split('_');
			
			// manipulasi chatwindowcontentitem_4e1df5f8c1b4bad817000001
			
			
			if(event.which == 13){
				var text = $.trim($(this).val());
				if(text != ''){
//					text = "<strong>You: </strong>" + text;
					$.post("<?php echo $this->basepath; ?>friend/user/submitchat", {friend_id:_id_split[1], text:text}, function(data){
//						$('#chatwindowcontentitem_' + _id_split[1]).html('<br>' + data);
					});
				}
				
				$(this).val('');
				event.preventDefault();
				
				
			}
		});
	
		function loadChat(){	// me-reload semua class .chatwindowcontent setiap x.x detik
			$('.chatwindowcontent').each(function(){
				var _id = $(this).attr('id');
				// alert(_id);	// chatwindowcontentitem_4e1df5f8c1b4bad817000001
				var _id_split = _id.split('_');
				// alert(_id_split[1]);
				
				var oldscrollHeight = $(this)[0].scrollHeight;
//				$.post("<?php echo $this->basepath; ?>friend/user/submitchat", {friend_id: _id_split[1]}, function(data){
//					$('#' + _id).html(data);
//				});

				$.ajax({
					url: "<?php echo $this->basepath; ?>friend/user/getchat/" + _id_split[1],
					cache: false,
					success: function(html){
						$('#' + _id).html(html);
						var newscrollHeight = $('#' + _id)[0].scrollHeight;
						if(newscrollHeight > oldscrollHeight){
							$('#' + _id).animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div
						}
					}
					
					
				});

				
			});

		}
		
		setInterval(loadChat, 2000);
		
/*		
		function loadLog(){		
			var oldscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
			$.ajax({
				url: "log.html",
				cache: false,
				success: function(html){		
					$("#chatbox").html(html); //Insert chat log into the #chatbox div				
					var newscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
					if(newscrollHeight > oldscrollHeight){
						$("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div
					}				
					},
			});
		}
		setInterval (loadLog, 2500);	//Reload file every 2.5 seconds
*/
		
		
	});

</script>

<div class="float_div_bottom shadow">

<?php
// sementara untuk player dulu, admin belum
	if(isset($_SESSION['user_id'])){
?>

<div class='chatarea'>
	
</div>

<div class='friendlist_bottom shadow transparent_70'>
	<div class='friendlist_bottom_header'>
		<div class='friendlist_bottom_title'>
			Friend List
		</div>
	</div>
	
	<div class='friendlist_bottom_content'>
		
	</div>
	
</div>


<?php
	}
?>
	
</div>
<?php
}
?>
