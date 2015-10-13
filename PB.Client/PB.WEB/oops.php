<html>
  <head>
	
		<meta name="author" content="mukhtarhudaya@gmail.com" />
		<meta name="description" content="Social Massive Multi-player Online Game" />
		<meta name="keywords" content="3D Online Game, PopBloop, MMORPG" />
		
		
		<!-- Add the following three tags inside head -->
		<meta itemprop="name" content="PopBloop 3D Social Game">
		<meta itemprop="description" content="Let's join PopBloop, make friends in virtual world and have fun!">
		<meta itemprop="image" content="http://www.popbloop.com/tide/popbloop.img/popbloop.png">
		
		<meta charset="utf-8" />
		<title>PopBloop: It's Nothing! Yet Everything</title>
		
		
		<meta property="og:title" content="PopBloop - Create your world and get new friends!" />
		<meta property="og:type" content="game" />
		<meta property="og:url" content="http://popbloop.com" />
		<meta property="og:image" content="http://popbloop.com/tide/popbloop.img/popbloop.fb.jpg" />
		<meta property="og:site_name" content="PopBloop" />
		<meta property="fb:admins" content="1834091583" />
		
		<script type="text/javascript">
		
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-29770706-1']);
			_gaq.push(['_setDomainName', 'popbloop.com']);
			_gaq.push(['_trackPageview']);
		
			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
		
		</script>

	
	
		<script language="javascript" src="/libraries/js/jquery-1.6.1.min.js"></script>
    <title>PopBloop: It's Nothing! Yet Everything</title>
		<style>
			.dwp_normal{
				background: url(/images/npc_wall_DOWNLOAD.jpg) center right no-repeat;
			}
			.dwp_hover{
				background: url(/images/npc_wall_DOWNLOAD_HOVER.jpg) center right no-repeat;
			}
			.dwp_click{
				background: url(/images/npc_wall_DOWNLOAD_CLICK.jpg) center right no-repeat;
			}
		</style>
		<script type="text/javascript">
			
		$(document).ready(function(){
			$('#dwp').live({
				mousedown:
					function(){
						$('#dwp').removeClass('dwp_normal');
						$('#dwp').removeClass('dwp_hover');
						$('#dwp').addClass('dwp_click');
					},
				mouseup:
					function(){
						$('#dwp').removeClass('dwp_normal');
						$('#dwp').removeClass('dwp_click');
						$('#dwp').addClass('dwp_hover');
					},
				mouseenter:
					function(){
						$('#dwp').removeClass('dwp_normal');
						$('#dwp').removeClass('dwp_click');
						$('#dwp').addClass('dwp_hover');
					},
				mouseleave:
					function(){
						$('#dwp').removeClass('dwp_hover');
						$('#dwp').removeClass('dwp_click');
						$('#dwp').addClass('dwp_normal');
					}
			});
		});
			
		</script>
  </head>
<body style="background-color:#83b3b5;">
  <div style="text-align:center; display: table; background:url(images/npc_wall_2.jpg) center no-repeat; width: 100%;" >
		<div class="dwp_normal_error" style="width: 920px; height: 900px; margin:0 auto; vertical-align: middle; text-align: center;"><!--[  background: url(/images/npc_wall_DOWNLOAD.jpg) center right no-repeat; ]-->
			<!--[<div style="position: fixed; right: 920px; top: 400px; width: 158px; height: 45px;" id="dwp"></div>]-->
			<div style="position: relative; height: 480px; background-color: transparent; float: left; opacity: 0.7; width: 920px;"></div>
			<div style="position: relative; height: 45px; background-color: transparent; float: left; opacity: 0.7; width: 730px;"></div>
			<a href="/images/npc_wall.jpg" target="_blank"><div class="dwp_normal" id="dwp" style="position: relative; height: 45px; background-color: transparent; float: left; opacity: 0.7; width: 160px;"></div></a>
			<div style="position: relative; height: 45px; background-color: transparent; float: left; opacity: 0.7; width: 30px;"></div>
		</div>
	</div>
	
	<div style="display: none;">
		<img src="/images/npc_wall_DOWNLOAD.jpg" />
		<img src="/images/npc_wall_DOWNLOAD_HOVER.jpg" />
		<img src="/images/npc_wall_DOWNLOAD_CLICK.jpg" />
	</div>
	
<body>
</html>