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
				
				$('#comment').click(function(){
					var post_id = $("#post_id").val();
					var message = $("#txtcomment").val();
					$.post("action_comment.php", {post_id: post_id, message: message}, function(data){
						if($.trim(data) != ''){
							alert('Comment posted. Comment ID: ' + $data);
						} else {
							alert('Operation failed! Data: ' + data);
						}
					});
				});
				$('#delete').click(function(){
					var post_id = $("#post_id").val();
					$.post("action_delete.php", {post_id: post_id}, function(data){
						if($.trim(data) != ''){
							alert('Comment deleted. Data: ' + $data);
						} else {
							alert('Operation failed! Data: ' + data);
						}
					});
				});
			});
    </script>
  
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

  </head>
	<body>


  	<pre>
  	<?php
//			print_r($me);
		?>
    </pre>

<?php
	function getAllPosts($feeds){
		return $feeds;
	}

	function getFirstPost($feeds){
		return $feeds['data'][0];
	}
	
	function getFirstPostId($feeds){
		return $feeds['data'][0]['id'];
	}
	
	// OK
	//	$firstPost = getFirstPost($facebook->api('/me/posts'));
	//	$allPosts = getAllPosts($facebook->api('/me/posts'));
	
	//	$firstPostId = getFirstPostId($facebook->api('/me/posts'));
	
//	$like_id = $facebook->api('/' . $firstPostId . '/likes', 'POST');
//	$delete_like = $facebook->api('/' . $like_id, 'DELETE');

	// OK
	//	$status_ = $facebook->api('/me/feed', 'POST', array('message' => "http://localhost/lilo3a, Post to wall programmatically testing... " . date("Y-m-d H:i:s")));

// OAuthException
//	$like_status_ = $facebook->api('/' . $status_['id'] . '/likes', 'POST');
// OAuthException
//	$comment_id = $facebook->api('/' . $status_['id'] . '/comments', 'POST', array('message' => "Wih, keren banget tuh Gan! " . date("Y-m-d H:i:s")));
	
	
	
?>

  	<pre>
  	<?php
//			print_r($firstPost);
//			print_r($allPosts);
//			print("First Post ID: " . $firstPostId . ", Like ID: " . $like_id);
			
			// OK
			// access friendlists
//			$friendlists = $facebook->api('/me/friendlists');
//			print_r($friendlists);
//	[6] => Array
//			(
//					[id] => 1704140023370
//					[name] => SMUN 1 Yogyakarta
//					[list_type] => user_created
//			)
			$friendlist_id = '1704140023370';
			$friendlist = $facebook->api('/' . $friendlist_id . '/members');
			print_r($friendlist);
		?>
    </pre>
    <!--[*
		<input type="text" size="40" value="<?php echo print_r($status_['id'], true); ?>" id="post_id" />&nbsp;<input type="button" id="delete" value="Delete this post!" />
		<textarea style="width:500px; height:200px;" id="txtcomment"></textarea><br />
    <input type="button" id="comment" value="Comment!" />
		*]-->
  </body>
</html>
