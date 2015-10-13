<?php
//	retrieve friend lists from facebook + save to db + display to user
//	get all lists members
//	user memilih dengan checklist + textarea dgn isi "Ayo gabung PopBloop, seru loh."
//	submit ke halaman ini lagi
//	ke user yg dipilih, kirimkan wall message: ""

require_once 'config.php';

set_time_limit(120);

?>

<?php
	require_once 'config.php';
	
	$session = $facebook->getUser();
	$me = null;
	if($session){
		try{
			$me = $facebook->api('/me');
		} catch (Exception $e){}
	}
	if(!($me)){
    $loginUrl = $facebook->getLoginUrl(array('scope' => 'email,user_birthday,status_update,publish_stream,user_photos,user_videos,read_friendlists'));
		echo '<script>
			top.location.href="'.$loginUrl.'"
		</script>';
		//	$facebook->getLoginUrl(array('req_perms' => 'publish_stream', 'next' => 'http://apps.facebook.com/popbloop/'))
		exit;
	}
	
	
?>
<html>
	<head>
  	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
    
    <script type="text/javascript">
    	$(document).ready(function(){
				$('#check_all_friends').live('click', function(){
					$('.checkbox_friend').attr("checked", true);
					$('#toggle_check_all_friends').html('<a style="cursor:pointer;" id="uncheck_all_friends">Deselect all friends</a>');
				});
				
				$('#uncheck_all_friends').live('click', function(){
					$('.checkbox_friend').attr("checked", false);
					$('#toggle_check_all_friends').html('<a style="cursor:pointer;" id="check_all_friends">Select all friends</a>');
				});
				
				$('#btn_invite').live('click', function(){
//					alert('post to ...');
					var allVals = [];
					$('.checkbox_friend').each(function() {
						if($(this).attr('checked')){
//							alert($(this).val());
							allVals.push($(this).val());
						}
					});
//					alert(allVals);
//					$('#t').val(allVals)
					

					$.post("invite_friends_action.php", {selected_friends: allVals}, function(data){
						alert(data);
						// data dalam format json
						var invited_friends = eval("(" + data + ")");
						for(var i = 0; i < invited_friends.length; i++){
							$('#invite_' + invited_friends[i]).attr('disabled', 'disabled');
						}
					});
				});
				
			});
    </script>
  
		<link href="css/fb.css" rel="stylesheet" type="text/css" />
  <link href="css/necolas-css3-facebook-buttons-7115bce/fb-buttons.css" rel="stylesheet" type="text/css">
	</head>
	<body class="fbbody">

	<div id="fb-root">

  	<pre>
  	<?php
//			print_r($firstPost);
//			print_r($allPosts);
//			print("First Post ID: " . $firstPostId . ", Like ID: " . $like_id);
			
			// OK
			// access friendlists
			$friendlists = $facebook->api('/me/friendlists');
//			print_r($friendlists);
//	[6] => Array
//			(
//					[id] => 1704140023370
//					[name] => SMUN 1 Yogyakarta
//					[list_type] => user_created
//			)
//			$friendlist_id = '1704140023370';	// SMUN 1 Yogyakarta
//			$friendlist_id = '2302957433431';	// PopBloop
//			$friendlist_member = $facebook->api('/' . $friendlist_id . '/members');
//			print_r($friendlist_member);
			
			
/*
Array
(
    [data] => Array
        (
            [0] => Array
                (
                    [id] => 1023302095
                    [name] => Qurrota Ayun
                )

        )

    [paging] => Array
        (
            [next] => https://graph.facebook.com/2302957433431/members?method=GET&access_token=AAAFBxR81IbUBAEB1YiakANrRNHQVBhckRZATAKKg65mVPeAPRWtsJ23XTYPbITULYdKp0XLOPnUkF7rE8dwSLhZAvkT38oPlskqzSlr35ByYQMQvpv&limit=5000&offset=5000&__after_id=1023302095
        )

)
*/
//	tulis di wall qurrota
//			$friend_id = '1023302095';
//			$post_id = $facebook->api('/' . $friend_id . '/feed', 'POST', array('message' => 'Gabung ke PopBloop yuk...'));
//			print("\n\r\n\rPost ID: $post_id");
			
		?>
    </pre>
    <!--[*
		<input type="text" size="40" value="<?php echo print_r($status_['id'], true); ?>" id="post_id" />&nbsp;<input type="button" id="delete" value="Delete this post!" />
		<textarea style="width:500px; height:200px;" id="txtcomment"></textarea><br />
    <input type="button" id="comment" value="Comment!" />
		*]-->
    <div class="fbgreybox" style="width:680px; height:20px;">
    	<div style="width:500px; float:left;">Invite your friend to join PopBloop&nbsp;&nbsp;[<span style="width:200px; text-align:center;" id="toggle_check_all_friends"><a style="cursor:pointer;" id="check_all_friends">Select all friends</a></span>]</div>
    	<div style="width:180px; float:left; text-align:right;"><a id="btn_invite" class="uibutton large confirm">Invite selected friends</a></div>
    </div>
			<?php
			for($idx = 0; $idx < count($friendlists['data']); $idx++){
      	$friendlist_id = $friendlists['data'][$idx]['id'];
				$friendlist_members = $facebook->api('/' . $friendlist_id . '/members');

				if(count($friendlist_members['data'])){

			?>
		      <div class="fbbluebox" style="width:680px; float:left;"><?php print($friendlists['data'][$idx]['name']); ?></div>
      <?php
			
				}
				
				$even_div = false;
				for($i = 0; $i < count($friendlist_members['data']); $i++){
					$div_width = "329px;";
					if(!isset($friendlist_members['data'][$i + 1]['id']) && !$even_div){
						$div_width = "680px;";
					}
			?>
          <div class="fbgreybox" style="width:<?php echo $div_width; ?> float:left; height:9px; font-weight:normal; font-size:11px; padding-top:2px; padding-bottom:10px;">
            <input class="checkbox_friend" id="invite_<?php print($friendlists['data'][$idx]['id'] . '_' . $friendlist_members['data'][$i]['id']); ?>" 
              title="Check to invite <?php print($friendlist_members['data'][$i]['name']); ?>" type="checkbox" name="invite_friend[]" 
              value="<?php print($friendlists['data'][$idx]['id'] . '_' . $friendlist_members['data'][$i]['id']); ?>" />
            &nbsp;
            <label for="invite_<?php print($friendlists['data'][$idx]['id'] . '_' . $friendlist_members['data'][$i]['id']); ?>">
              <?php print($friendlist_members['data'][$i]['name']); ?>
            </label>
            
          </div>
      
      <?php
					$even_div = !$even_div;
				}
				
			?>
      
      
      <?php
			}	// end for friendlists data
			?>

		<div style="height:150px; width:680px; float:left;">&nbsp;</div>

	</div>
  
	<script>
  
    window.fbAsyncInit = function(){
      FB.Canvas.setAutoResize();
    };
    (function(){
      var e = document.createElement('script');
      e.async = true;
      e.src = document.location.protocol +
        '//connect.facebook.net/en_US/all.js';
      document.getElementById('fb-root').appendChild(e);
    }());
  
  </script>

    
  </body>
</html>
