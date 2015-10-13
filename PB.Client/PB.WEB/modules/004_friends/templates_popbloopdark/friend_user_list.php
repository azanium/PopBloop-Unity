<style type="text/css">
.friend_detail{
	cursor:pointer;
}

.approval_wait {
	cursor:pointer;
	color: #0CF;
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
					text = text + '<div id="photo_' + all_people[idx].lilo_id + '" style="width:50px; height:80px; float:left; text-align:left; overflow-x:hidden;">';
					text = text + '<img style="max-width:48px; max-height:48px;" src="<?php echo $this->basepath; ?>user_generated_data/profile_picture/'+all_people[idx].profile_picture+'">';
					text = text + '</div>';
					text = text + '<div id="desc_' + all_people[idx].lilo_id + '" style="width:180px; height:80px; float:left; text-align:left; overflow-x:hidden;">';
					text = text + all_people[idx].username + '<br />';
					text = text + all_people[idx].email + '<br />';
					
					text = text + '<span id="span_' + all_people[idx].lilo_id + '" >';
					
					if(all_people[idx].invitation_exists){
						text = text + "<i>Pending...</i>";
					} else if(all_people[idx].invitation2_exists){
						text = text + "<i class='approval_wait' id='wait_"+all_people[idx].lilo_id+"'>Waiting your approval...</i>";
					} else if(all_people[idx].friend_exists){
						text = text + "<i>Already in your friend list</i>";
					} else {
						text = text + "<a style='color:#0CF;' class='inviteasfriend' id='inviteasfriend_" + all_people[idx].lilo_id + "'>Add as friend</a>";
					}

					text = text + '</span>';
					
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
			new_circle = new_circle.replace('"', '');	// kurang...
			new_circle = new_circle.replace("'", "");
			if($.trim(new_circle) == ''){
				return false;
			}
			
			if($.trim(new_circle) == 'Outer Circle'){
				alert('Gunakan nama lain!');
				return false;
			}
			
			$.post("<?php echo $this->basepath; ?>friend/user/ws_circle/create/" + new_circle, {}, function(data){
				// data berupa array dalam format json
				var all_circles = eval('('+data+')');
				
				
				var text = '';
				for(idx = 0; idx < all_circles.length; idx++){
					text = text + "<input type='checkbox' class='r_a_selected_circles' name='r_a_selected_circles[]' id='r_a_selected_circles_"+idx+"' value='"+all_circles[idx]+"' />" + "<label for='r_a_selected_circles_"+idx+"'>&nbsp;" + all_circles[idx] +"</label>"+ '<br />';
				}
				$("#request_approval_circle_list").html(text);

				
//				$("#circle_list").html(data);
				$("#request_approval_new_circle").val('');
			});
		});

		$("#new_circle_submit").live('click', function(){
			var new_circle = $("#new_circle").val();
			new_circle = new_circle.replace('"', '');
			new_circle = new_circle.replace("'", "");
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
			minWidth: 400, 
			minHeight: 200,
			
			modal: true,


//			buttons: [
//				{
//					text: "Save",
//					click: function() {
//						// dapatkan daftar checkbox yg dipilih user
//						var selected_circles = [];
//						$('.selected_circles').each(function(index){
//							if($(this).attr('checked')){
//								selected_circles.push($(this).val());
//							}
//						});
//						// dapatkan friend_user_id
//						var friend_user_id = $('#friend_user_id').val();
//						
//						$.post("<?php echo $this->basepath; ?>friend/user/ws_invite", {'invitee_user_id':friend_user_id, 'circle_array':selected_circles}, function(data){
//							if(data == '1'){
//								alert('Invitation sent. Data: ' + data);
//								$('#span_' + friend_user_id).html("<i>Pending...</i>");
//							} else {
//								alert('Failed to send invitation. Data: ' + data);
//							}
//						});
//						
//						$(this).dialog('close');
////						alert('save...'); 
//					}
//				},
//				
//				{
//					text: "Cancel",
//					click: function() { $(this).dialog("close"); }
//				},
//				
//			]

		});
		
		$('#circle_dialog_close').live('click', function(){
			$("#circle_dialog").dialog('close');
		});

		$('#circle_dialog_save').live('click', function(){
																										
			var selected_circles = [];
			$('.selected_circles').each(function(index){
				if($(this).is(':checked')){
					selected_circles.push($(this).val());
				}
			});
			// dapatkan friend_user_id
			var friend_user_id = $('#friend_user_id').val();
			
			$.post("<?php echo $this->basepath; ?>friend/user/ws_invite", {'invitee_user_id':friend_user_id, 'circle_array':selected_circles}, function(data){
				if(data == '1'){
					alert('Invitation sent. Data: ' + data);
					$('#span_' + friend_user_id).html("<i>Pending...</i>");
				} else {
					alert('Failed to send invitation. Data: ' + data);
				}
			});
			
//			$(this).dialog('close');
			$("#circle_dialog").dialog('close');

			return true;
			
		});

		$("#request_approval_circle_dialog").dialog({
			autoOpen: false, 
			minWidth: 400, 
			minHeight: 200,
			
			modal: true,
		});

		$('#ra_circle_dialog_close').live('click', function(){
			$("#request_approval_circle_dialog").dialog('close');
		});

		$('#ra_circle_dialog_save').live('click', function(){
			alert('save');

			// dapatkan daftar checkbox yg dipilih user
			var selected_circles_ = [];
			// buggy
			// selected_circles nilainya NULL
			$('.r_a_selected_circles').each(function(index){alert("this val: " + $(this).val() + ", checked: " + $(this).is(':checked'));
				if($(this).is(':checked')){
					selected_circles_.push($(this).val());
				}
			});alert("selected circles: " + selected_circles_);
			// dapatkan invitation_id
			var invitation_id = $('#invitation_id').val();
			alert(invitation_id);


			$.post("<?php echo $this->basepath; ?>friend/user/ws_invitation_approval", {'invitation_id':invitation_id, 'circle_array':selected_circles_}, function(data){
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


			$("#request_approval_circle_dialog").dialog('close');
		});



		$('.deletecircle').live('click', function(){
			$('.loading_div').show();
			var _alt = $(this).attr('alt');
			alert(_alt);
			// buggy
			// hide gagal
			// pindah friend ke Outer Circle gagal
			$.post("<?php echo $this->basepath; ?>friend/user/ws_deletecircle", {circle_name: _alt}, function(data){
				if(data == '1'){
					// $(this).hide('slow');
					// ubah _alt menjadi circle_name_nospace
					// id='fieldset_"+circle_name_nospace+"'
					var circle_name_nospace = _alt.replace(' ', '__spasi__');
					
					var text_to_move = $('#fieldset_' + circle_name_nospace).html();
					$('#fieldset_Outer__spasi__Circle').append(text_to_move);
					$('#_fieldset_' + circle_name_nospace).hide('slow');
				} else {
					alert('Sometime shit happens...');
				}
			});
			$('.loading_div').hide();

		});
		
		// saat load, langsung tampilkan daftar friend di div search_friend_result
		$.post("<?php echo $this->basepath; ?>friend/user/ws_friendlist", {}, function(data){

				var all_circles = eval('('+data+')');
				
				
				var text = '';
				for(idx = 0; idx < all_circles.length; idx++){
					var circle_name = $.trim(all_circles[idx][0]);
					// contoh circle_name: 'MU FC'. semua ' ' / spasi harus diubah menjadi: '__spasi__'
					circle_name_nospace = circle_name.replace(' ', '__spasi__');
					
					var friend_array = all_circles[idx];
					var delete_link = "<a alt='"+circle_name+"' class='deletecircle'><img src='<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/delete.small.png'></a>"
					if(circle_name == 'Outer Circle'){
						delete_link = '';
					}
					text = text + "<fieldset id='_fieldset_"+circle_name_nospace+"'><legend id='legend_"+circle_name_nospace+"'>" + circle_name + "&nbsp;"+delete_link+"</legend><br />";
					
					text = text + "<div id='fieldset_"+circle_name_nospace+"'>";
					
					friend_text = '';
					for(i = 1; i < friend_array.length; i++){
							friend_text_ = "<span class='friend_detail' id='friend_detail_"+friend_array[i]['friend_id']+"_"+circle_name_nospace+"' style='width:160px; height:40px; float:left; margin:5px;'>";
							friend_text_ += "<span style='width:40px; height:40px; float:left;'><img style='max-height:40px; max-width:40px;' src='"+friend_array[i]['foto_url']+"' /></span>";
							friend_text_ += "<span style='width:100px; height:40px; float:left; text-align:left; overflow:hidden;'>"+friend_array[i]['fullname']+"</span>";
							friend_text_ += "<span style='width:20px; height:40px; float:left; text-align:left; overflow:hidden;'>";
							friend_text_ += "<a alt='Delete from "+circle_name+"' class='deletefromcircle' id='deletefromcircle_"+friend_array[i]['friend_id']+"'>";
							friend_text_ += "<img src='<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/delete.small.png'>";
							friend_text_ += "</a><br />";
//							friend_text_ += "y<br />";
//							friend_text_ += "z";
							friend_text_ += "</span>";
							friend_text_ += "</span>";
							friend_text = friend_text + friend_text_;
					}
					
					text = text + friend_text;
					text = text + "</div>";
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
						$('#' + _id).attr('alt', 'Delete from Outer Circle');
						var text_ = "<span class='friend_detail' id='friend_detail_" + user_id + "_Outer__spasi__Circle'>";
						text_ += $('#friend_detail_' + user_id + '_' + circle_name_nospace).html();
						text_ += "</span>";
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
		
		$('.friend_detail').live('click', function(){
			var _id = $(this).attr('id');
//			alert(_id);	// friend_detail_4df6e7192cbfd4e6c000fd9b_Colleague
			var _id_split = _id.split('_');
//			alert(_id_split[2]);
			
			$("#friend_detail_dialog").attr('title', _id_split[2]);
			$('#friend_detail_dialog_user_id').val(_id_split[2]);
			
			$('#friend_detail_dialog_profile_picture').attr('src', "<?php echo $this->basepath; ?>user_generated_data/profile_picture/" + 'default.png');
			
			$("#friend_detail_dialog").dialog('open');
		});
		
		$('#friend_detail_dialog_dm_submit').live('click', function(){
			var friend_id = $('#friend_detail_dialog_user_id').val();
			var dm = $('#friend_detail_dialog_dm').val();
			dm = $.trim(dm);
			
			if(friend_id.length == ''){
				alert("Sometimes it happens...");
				return false;
			}
			
			if(dm.length == ''){
				alert("The message should not be empty.");
				return false;
			}
			if(dm.length > 500){
				alert("Maximum characters is 500.");
				return false;
			}
			
			$.post("<?php echo $this->basepath; ?>message/user/dmsend", {friend_id: friend_id, dm: dm}, function(data){
				if(data == "OK"){
					alert("Direct message sent.");
					$("#friend_detail_dialog").dialog('close');
				} else {
					alert(data);
				}
			});

		});


		$("#friend_detail_dialog").dialog({
			autoOpen: false, 
			minWidth: 400, 
			minHeight: 110,
			maxWidth: 400, 
			maxHeight: 110,
			
			modal: true
		});

		$('.approval_wait').live('click', function(){
			var _id = $(this).attr('id');
//			alert(_id);
			var _id_split = _id.split('_');
			
			$('#tabs').tabs('select', 2);
			
			$('.cdiv_' + _id_split[1]).animate({backgroundColor:'#0CF', color:'#FFF'}, 2000).animate({backgroundColor:'transparent', color:'#000'}, 1000);
		});

		// saat load, langsung tampilkan daftar invitation di div invitation_list
		$.post("<?php echo $this->basepath; ?>friend/user/ws_friendrequest", {}, function(data){
				// data berupa array dalam format json
				var all_requests = eval('('+data+')');

				var text = '';
				for(idx = 0; idx < all_requests.length; idx++){

					// tampilkan foto dan mutual friends
					text = text + "<div style='width:240px; height:80px; float:left; text-align:left; overflow-x:hidden; border-radius:5px;' class='cdiv_"+all_requests[idx]['inviter_user_id']+"' id='div_"+all_requests[idx]['lilo_id']+"' >";
					text = text + "<div style='width:80px; height:80px; float:left; text-align:left; overflow-x:hidden;' class='foto_"+all_requests[idx]['inviter_user_id']+"' id='foto_"+all_requests[idx]['lilo_id']+"' >";
					text = text + "<a class='selected_requests' id='selected_requests2__"+all_requests[idx]['lilo_id']+"'>";
					text = text + "<img style='max-width:75px; max-height:75px;' src='<?php echo $this->basepath; ?>user_generated_data/profile_picture/"+all_requests[idx]['profile_picture']+"'>";
					text = text + "</a>";
					text = text + "</div>";
					text = text + "<div style='width:160px; height:80px; float:left; text-align:left; overflow-x:hidden;' class='desc_"+all_requests[idx]['inviter_user_id']+"' id='desc_"+all_requests[idx]['lilo_id']+"' >";
					text = text + "<a class='selected_requests' id='selected_requests__"+all_requests[idx]['lilo_id']+"'>" + all_requests[idx]['fullname'] + "</a>";
					text = text + "<br />";
					if(all_requests[idx]['mutual_friends_number'] >= 1){
						var s = all_requests[idx]['mutual_friends_number'] > 1 ? 's' : '';
						text = text + "<a class='mutual_friends' id='mutual_friends__"+all_requests[idx]['lilo_id']+"'>" + all_requests[idx]['mutual_friends_number'] + " mutual friend"+s+"</a>";
					} else {
						text = text + "No mutual friend";
					}
					text = text + "</div>";
					text = text + "</div>";
				}
				$("#invitation_list").html(text);
		});

		$('.selected_requests').live('click', function(){
			var _id = $(this).attr('id');
			var _id_split = _id.split('__');
			var invitation_id = _id_split[1];
			$('#invitation_id').val(_id_split[1]);

//			alert('Invitation ID: ' + invitation_id);
			$("#request_approval_circle_dialog").dialog('open');
		});

	});
</script>


<div class="container_12 pop_20_spacer">
</div>

<form id="login_form">
<div class="container_12">
  <div class="grid_12">
    <div id="tabs">
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
        <div style="width:100%; min-height:200px;" id="search_friend_result">
        &nbsp;
        </div>
      </div>
      <div id="tabs-2">
        <div style="width:100%; text-align:center">
          <input type="text" class="light_shadow transparent_70" name="search_people_keyword" id="search_people_keyword" title="Search People..." placeholder="Search People..." class="light_shadow transparent_70" />
          &nbsp;
          <input type="button" value="Search" id="search_people" style="width:100px;" class="light_shadow transparent_70" />
        </div>
        <div style="width:100%; min-height:200px;" id="search_people_result">
        ...
        </div>
      </div>
      <div id="tabs-3">
        <div style="width:100%; text-align:left; min-height:230px;" id="invitation_list">
          List of Friend Request...
        </div>
      </div>
    </div>

  </div>
</div>
</form>

<div style="width: auto; min-height: 200px; height: auto; min-width:400px; " class="ui-dialog-content ui-widget-content" id="request_approval_circle_dialog" 
  title="Add to Circle">


	<input type="hidden" name="invitation_id" id="invitation_id" value="" />
	<div id="request_approval_circle_list" style="text-align:left;">
	<?php
		$idx = 0;print_r($this->circles, true);
  	foreach((array)$this->circles as $key => $circle){
//			 print($circle . "<br />");
			print("<input type='checkbox' class='r_a_selected_circles' name='r_a_selected_circles[]' id='r_a_selected_circles_".$idx."' value='".$circle."' />");
			print("<label for='r_a_selected_circles_".$idx."'>&nbsp;".$circle."</label>" . '<br />');
			// text = text + "<input type='checkbox' name='selected_circles' id='selected_circles_"+idx+"' value='"+all_circles[idx]+"' />" + "<label for='selected_circles_"+idx+"'>" + all_circles[idx] +"</label>"+ '<br />';
			$idx++;
		}
	?>
  </div>
  
	<input type="text" size="12" name="request_approval_new_circle" id="request_approval_new_circle" value="" />&nbsp;<input type="button" name="request_approval_new_circle_submit" id="request_approval_new_circle_submit" value="Add New Circle" />

	<div>
  	<input type="button" id="ra_circle_dialog_save" name="ra_circle_dialog_save" value="Save" />&nbsp;<input type="button" id="ra_circle_dialog_close" name="ra_circle_dialog_close" value="Cancel" />
  </div>

</div>

<div style="width: auto; min-height: 200px; height: auto; min-width:400px; " class="ui-dialog-content ui-widget-content" id="circle_dialog" 
  title="Add People to Circle">


	<input type="hidden" name="friend_user_id" id="friend_user_id" value="" />
	<div id="circle_list" style="text-align:left;">
	<?php
		$idx = 0;//print_r($this->circles, true);
  	foreach((array)$this->circles as $key => $circle){
//			 print($circle . "<br />");
			print("<input type='checkbox' class='selected_circles' name='selected_circles[]' id='selected_circles_".$idx."' value='".$circle."' />");
			print("<label for='selected_circles_".$idx."'>&nbsp;".$circle."</label>" . '<br />');
			// text = text + "<input type='checkbox' name='selected_circles' id='selected_circles_"+idx+"' value='"+all_circles[idx]+"' />" + "<label for='selected_circles_"+idx+"'>" + all_circles[idx] +"</label>"+ '<br />';
			$idx++;
		}
	?>
  </div>
  
	<input type="text" size="12" name="new_circle" id="new_circle" value="" style="width:240px;" />&nbsp;<input type="button" name="new_circle_submit" id="new_circle_submit" value="Add New Circle" style="width:138px;" />


	<div>
  	<input type="button" id="circle_dialog_save" name="circle_dialog_save" value="Save" />&nbsp;<input type="button" id="circle_dialog_close" name="circle_dialog_close" value="Cancel" />
  </div>

</div>


<div style="width: auto; min-height: 110px; height: auto; min-width:400px; " class="ui-dialog-content ui-widget-content" id="friend_detail_dialog" 
  title="Direct Message">

	<input type="hidden" name="friend_detail_dialog_user_id" id="friend_detail_dialog_user_id" value="" />
  <div style="height:50px;">
  	<div style="width:50px; height:40px; float:left; text-align:left"><img id="friend_detail_dialog_profile_picture" src="" style="max-height:40px; max-width:40px;" /></div>
  	<div style="width:330px; height:40px; float:left;"><textarea style="max-height:40px; min-height:40px; max-width:330px; min-width:330px;" name="friend_detail_dialog_dm" id="friend_detail_dialog_dm"></textarea></div>
  </div>

  <div style="height:20px;">
  	<div style="width:300px; height:20px; float:left;"></div>
  	<div style="width:80px; height:20px; float:left;"><input type="button" name="friend_detail_dialog_dm_submit" id="friend_detail_dialog_dm_submit" value="Send" style="width:100%; height:100%;" /></div>
	  
  </div>
</div>