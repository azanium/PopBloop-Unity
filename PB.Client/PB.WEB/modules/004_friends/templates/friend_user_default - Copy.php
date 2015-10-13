<style type="text/css">
.friend_detail {
	cursor:pointer;
}
</style>
<script src="<?php echo $this->basepath; ?>libraries/js/jquery_placeholder.js"></script>
<script language="javascript">
	$(document).ready(function(){
		$('input[placeholder],textarea[placeholder]').placeholder();
		
		$('#tabs').tabs();
		
		$("#search_people").click(function(){
			var keyword = $("#search_people_keyword").val();
//			alert(keyword);
			$.post("<?php echo $this->basepath; ?>friend/user/ws_search/" + keyword, {}, function(data){
//				alert(data);
				var all_people = eval('('+data+')');
//				alert(all_people[0].email);
				var text = '';
				for(idx = 0; idx < all_people.length; idx++){
					text = text + '<div style="width:50px; height:80px; float:left; text-align:left; overflow-x:hidden;">Foto</div>';
					text = text + '<div style="width:180px; height:80px; float:left; text-align:left; overflow-x:hidden;">';
					text = text + all_people[idx].username + '<br />';
					text = text + all_people[idx].email + '<br />';
					
					if(all_people[idx].invitation_exists <= 0){
						text = text + "<a class='inviteasfriend' id='inviteasfriend_" + all_people[idx].lilo_id + "'>Add as friend</a>";
					} else {
						text = text + "<i>Pending...</i>";
					}
					
					text = text + '</div>';
				}
				$("#search_people_result").html(text);
			});
		});
		
		$(".inviteasfriend").live('click',function(){
			var _id = $(this).attr('id');
//			alert(_id);
			var _id_split = _id.split('_');
			
			$('#friend_user_id').val(_id_split[1]);
			
			// tampilkan daftar circle
			$("#circle_dialog").dialog('open');
			
			// kirim ke fungsi friend_user_ws_invite
//			$.post("<?php echo $this->basepath; ?>friend/user/ws_invite", {invitee_user_id: _id_split[1]}, function(data){
//				alert(data);
//			});
			// sampe senee...
		});

		$('#request_approval_new_circle_submit').live('click', function(){
			var new_circle = $("#request_approval_new_circle").val();
			
			if($.trim(new_circle) == ''){
				return false;
			}
			
			$.post("<?php echo $this->basepath; ?>friend/user/ws_circle/create/" + new_circle, {}, function(data){
				// data berupa array dalam format json
				var all_circles = eval('('+data+')');
				
				
				var text = '';
				for(idx = 0; idx < all_circles.length; idx++){
					text = text + "<input type='checkbox' class='selected_circles' name='selected_circles[]' id='selected_circles_"+idx+"' value='"+all_circles[idx]+"' />" + "<label for='selected_circles_"+idx+"'>&nbsp;" + all_circles[idx] +"</label>"+ '<br />';
				}
				$("#request_approval_circle_list").html(text);

				
//				$("#circle_list").html(data);
				$("#request_approval_new_circle").val('');
			});
		});

		$("#new_circle_submit").live('click', function(){
			var new_circle = $("#new_circle").val();
			
			if($.trim(new_circle) == ''){
				return false;
			}
			
			$.post("<?php echo $this->basepath; ?>friend/user/ws_circle/create/" + new_circle, {}, function(data){
				// data berupa array dalam format json
				var all_circles = eval('('+data+')');
				
				
				var text = '';
				for(idx = 0; idx < all_circles.length; idx++){
					text = text + "<input type='checkbox' class='selected_circles' name='selected_circles[]' id='selected_circles_"+idx+"' value='"+all_circles[idx]+"' />" + "<label for='selected_circles_"+idx+"'>&nbsp;" + all_circles[idx] +"</label>"+ '<br />';
				}
				$("#circle_list").html(text);

				
//				$("#circle_list").html(data);
				$("#new_circle").val('');
			});
		});

		$("#circle_dialog").dialog({
			autoOpen: false, 
			minWidth: 420, 
			minHeight: 200,

			buttons: [
				{
					text: "Save",
					click: function() {
						// dapatkan daftar checkbox yg dipilih user
						var selected_circles = [];
						$('.selected_circles').each(function(index){
							if($(this).attr('checked')){
								selected_circles.push($(this).val());
							}
						});
						// dapatkan friend_user_id
						var friend_user_id = $('#friend_user_id').val();
						
						$.post("<?php echo $this->basepath; ?>friend/user/ws_invite", {'invitee_user_id':friend_user_id, 'circle_array':selected_circles}, function(data){
							if(data == '1'){
								alert('Invitation sent. Data: ' + data);
							} else {
								alert('Failed to send invitation. Data: ' + data);
							}
						});
						
						alert('save...'); 
					}
				},
				
				{
					text: "Cancel",
					click: function() { $(this).dialog("close"); }
				},
				
			]

		});

		$("#request_approval_circle_dialog").dialog({
			autoOpen: false, 
			minWidth: 420, 
			minHeight: 200,

			buttons: [
				{
					text: "Save",
					click: function() {
						// dapatkan daftar checkbox yg dipilih user
						var selected_circles = [];
						$('.request_approval_selected_circles').each(function(index){
							if($(this).attr('checked')){
								selected_circles.push($(this).val());
							}
						});
						// dapatkan invitation_id
						var invitation_id = $('#invitation_id').val();
						
						// friend_user_ws_invitation_approval
						$.post("<?php echo $this->basepath; ?>friend/user/ws_invitation_approval", {'invitation_id':invitation_id, 'circle_array':selected_circles}, function(data){
							if(data == '1'){
								alert('Friend added.');
								// hide dari request list
								//		'div_"+all_requests[idx]['lilo_id']+"'
								$('#div_' + invitation_id).hide('slow');
								
								// append search_friend_result
								var text_ = '';
								
								// sampe seneee... 
								// dapatkan fullname dan email dari friend yg baru ditambahkan... dari: 'selected_requests__"+all_requests[idx]['lilo_id']+"'
								var fullname = $('#selected_requests__' + invitation_id).html();

								text_ = text_ + '<div style="width:50px; height:80px; float:left; text-align:left; overflow-x:hidden;">Foto</div>';
								text_ = text_ + '<div style="width:180px; height:80px; float:left; text-align:left; overflow-x:hidden;">';
								text_ = text_ + fullname + '<br />';
								text_ = text_ + 'email blm ada :(' + '<br />';
								
								text_ = text_ + "<a class='friend_detail' id='friend_detail_" + all_people[idx].lilo_id + "'>Detail...</a>";
								
								text_ = text_ + '</div>';

								
								$('#search_friend_result').append(text_);
								
							} else {
								alert('Failed to save friend data. Data: ' + data);
							}
						});
						
						alert('save...'); 
					}
				},
				
				{
					text: "Cancel",
					click: function() { $(this).dialog("close"); }
				},
				
			]

		});
		
		$('.editcircle').live('click', function(){
			var _alt = $(this).attr('alt');
			alert(_alt);
		});
		
		// saat load, langsung tampilkan daftar friend di div search_friend_result
		$.post("<?php echo $this->basepath; ?>friend/user/ws_friendlist", {}, function(data){

				var all_circles = eval('('+data+')');
				
				
				var text = '';
				for(idx = 0; idx < all_circles.length; idx++){
					var circle_name = all_circles[idx][0];
					// contoh circle_name: 'MU FC'. semua ' ' / spasi harus diubah menjadi: '__spasi__'
					circle_name_nospace = circle_name.replace(' ', '__spasi__');
					
					var friend_array = all_circles[idx];
//					text = text + "<input type='checkbox' class='selected_circles' name='selected_circles[]' id='selected_circles_"+idx+"' value='"+all_circles[idx]+"' />" + "<label for='selected_circles_"+idx+"'>&nbsp;" + all_circles[idx] +"</label>"+ '<br />';
					text = text + "<fieldset id='fieldset_"+circle_name_nospace+"'><legend>" + circle_name + "&nbsp;<a alt='"+circle_name+"' class='editcircle'>x</a></legend><br />";
					
					friend_text = '';
					for(i = 1; i < friend_array.length; i++){
//						friend_text = friend_text + friend_array[i]['friend_id'] + ' -- <br />';
//						friend_text = friend_text + friend_array[i]['username'] + ' -- <br />';
//						friend_text = friend_text + friend_array[i]['email'] + ' -- <br />';
							friend_text_ = "<span class='friend_detail' id='friend_detail_"+friend_array[i]['friend_id']+"_"+circle_name_nospace+"' style='width:240px; height:80px; float:left; margin:5px;'>";
							friend_text_ += "<span style='width:80px; height:80px; float:left;'><img style='max-height:80px; max-width:80px;' src='"+friend_array[i]['foto_url']+"' /></span>";
							friend_text_ += "<span style='width:140px; height:80px; float:left; text-align:left; overflow:hidden;'>"+friend_array[i]['fullname']+"</span>";
							friend_text_ += "<span style='width:20px; height:80px; float:left; text-align:left; overflow:hidden;'>";
							friend_text_ += "<a alt='Delete from "+circle_name+"' class='deletefromcircle' id='deletefromcircle_"+friend_array[i]['friend_id']+"'>x</a><br />";
							friend_text_ += "y<br />";
							friend_text_ += "z";
							friend_text_ += "</span>";
							friend_text_ += "</span>";
							friend_text = friend_text + friend_text_;
					}
					
					text = text + friend_text;
					text = text + "</fieldset>";
				}
				$("#search_friend_result").html(text);

		});
		
		$('.deletefromcircle').live('click', function(){
			var _id = $(this).attr('id');
			var _id_split = _id.split('_');
			var user_id = _id_split[1];
			
			var _alt = $(this).attr('alt');
			
			// 'Delete from xxx'
			var circle_name = $.trim(_alt.substring(11));
			var circle_name_nospace = circle_name.replace(' ', '__spasi__');
			
			alert(user_id + ' -- ' + circle_name);
			alert('#friend_detail_' + user_id + '_' + circle_name);
			
			// post ke ws
			$.post("<?php echo $this->basepath; ?>friend/user/ws_deletefromcircle", {circle_name:circle_name, friend_id:user_id}, function(data){
				if(data == '1'){
					if(circle_name != 'Outer Circle'){
						var text_ = $('#friend_detail_' + user_id + '_' + circle_name_nospace).html();
						$('#fieldset_Outer__spasi__Circle').append(text_);
					}
					
					// hide 
					$('#friend_detail_' + user_id + '_' + circle_name_nospace).hide('slow');
				} else {
					alert(data);
				}

			});
			
		});
		
		$('.friend_detail').live({
			mouseenter: function(){
				$(this).addClass('shadow');
			}, 
			mouseleave: function(){
				$(this).removeClass("shadow");
			}
		});


		// saat load, langsung tampilkan daftar invitation di div invitation_list
		$.post("<?php echo $this->basepath; ?>friend/user/ws_friendrequest", {}, function(data){
				// data berupa array dalam format json
				var all_requests = eval('('+data+')');
				
				var text = '';
				for(idx = 0; idx < all_requests.length; idx++){
//					text = text + "<div id=''><input type='checkbox' class='selected_requests' name='selected_requests[]' id='selected_requests__"+all_requests[idx]['lilo_id']+"' value='"+all_requests[idx]['lilo_id']+"' />" + "<label for='selected_requests__"+all_requests[idx]['lilo_id']+"'>&nbsp;" + all_requests[idx]['fullname'] +"</label>"+ '</div>';
					text = text + "<div id='div_"+all_requests[idx]['lilo_id']+"' ><a class='selected_requests' id='selected_requests__"+all_requests[idx]['lilo_id']+"'>" + all_requests[idx]['fullname'] + "</a></div>";
				}
				$("#invitation_list").html(text);
		});

		$('.selected_requests').live('click', function(){
			var _id = $(this).attr('id');
			var _id_split = _id.split('__');
			var invitation_id = _id_split[1];
			$('#invitation_id').val(_id_split[1]);

			alert('Invitation ID: ' + invitation_id);
			$("#request_approval_circle_dialog").dialog('open');
		});

	});
</script>

<div style="width: auto; min-height: 58.4px; height: auto; min-width:420px; " class="ui-dialog-content ui-widget-content" id="request_approval_circle_dialog" 
	title="Add to Circle">

	<input type="hidden" name="invitation_id" id="invitation_id" value="" />
	<div id="request_approval_circle_list" style="text-align:left;">
	<?php
		$idx = 0;print_r($this->circles, true);
  	foreach($this->circles as $key => $circle){
//			 print($circle . "<br />");
			print("<input type='checkbox' class='request_approval_selected_circles' name='request_approval_selected_circles[]' id='request_approval_selected_circles_".$idx."' value='".$circle."' />");
			print("<label for='request_approval_selected_circles_".$idx."'>&nbsp;".$circle."</label>" . '<br />');
			// text = text + "<input type='checkbox' name='selected_circles' id='selected_circles_"+idx+"' value='"+all_circles[idx]+"' />" + "<label for='selected_circles_"+idx+"'>" + all_circles[idx] +"</label>"+ '<br />';
			$idx++;
		}
	?>
  </div>
  
	<input type="text" size="12" name="request_approval_new_circle" id="request_approval_new_circle" value="" />&nbsp;<input type="button" name="request_approval_new_circle_submit" id="request_approval_new_circle_submit" value="Add New Circle" />

</div>



<div style="width: auto; min-height: 58.4px; height: auto; min-width:420px; " class="ui-dialog-content ui-widget-content" id="circle_dialog" 
	title="Add People to Circle">

	<input type="hidden" name="friend_user_id" id="friend_user_id" value="" />
	<div id="circle_list" style="text-align:left;">
	<?php
		$idx = 0;print_r($this->circles, true);
  	foreach($this->circles as $key => $circle){
//			 print($circle . "<br />");
			print("<input type='checkbox' class='selected_circles' name='selected_circles[]' id='selected_circles_".$idx."' value='".$circle."' />");
			print("<label for='selected_circles_".$idx."'>&nbsp;".$circle."</label>" . '<br />');
			// text = text + "<input type='checkbox' name='selected_circles' id='selected_circles_"+idx+"' value='"+all_circles[idx]+"' />" + "<label for='selected_circles_"+idx+"'>" + all_circles[idx] +"</label>"+ '<br />';
			$idx++;
		}
	?>
  </div>
  
	<input type="text" size="12" name="new_circle" id="new_circle" value="" />&nbsp;<input type="button" name="new_circle_submit" id="new_circle_submit" value="Add New Circle" />

</div>


<form id="login_form">
<div class="centered transbg" style="width:960px; border:none;">
  <div id="tabs" style="float:left; width:960px;">
    <ul>
      <li><a href="#tabs-1">Your Friends</a></li>
      <li><a href="#tabs-2">Find New Friends!</a></li>
      <li><a href="#tabs-3">Friend Request</a></li>
    </ul>
    <div id="tabs-1">
			<div style="width:100%; text-align:center">
      	<input type="text" class="light_shadow transparent_70" name="search_friend_keyword" id="search_friend_keyword" title="Search Friends..." placeholder="Search Friends..." class="light_shadow transparent_70" />
        &nbsp;
        <input type="button" value="Search" id="search_friend" style="width:100px;" class="light_shadow transparent_70" />
      </div>
			<div style="width:100%;" id="search_friend_result">
      search result
      </div>
    </div>
    <div id="tabs-2">
			<div style="width:100%; text-align:center">
      	<input type="text" class="light_shadow transparent_70" name="search_people_keyword" id="search_people_keyword" title="Search People..." placeholder="Search People..." class="light_shadow transparent_70" />
        &nbsp;
        <input type="button" value="Search" id="search_people" style="width:100px;" class="light_shadow transparent_70" />
      </div>
			<div style="width:100%;" id="search_people_result">
      ...
      </div>
    </div>
    <div id="tabs-3">
			<div style="width:100%; text-align:left" id="invitation_list">
    		List of Friend Request...
      </div>
    </div>
  </div>

</div>
</form>
