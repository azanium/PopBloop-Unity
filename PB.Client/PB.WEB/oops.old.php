<html>
  <head>
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
			<!--[<a href="/images/npc_wall.jpg" target="_blank"><div style="position: fixed; right: 920px; top: 400px; width: 158px; height: 45px;" class="dwp_normal" id="dwp"></div></a>]-->
		</div>
	</div>
	
	<div style="display: none;">
		<img src="/images/npc_wall_DOWNLOAD.jpg" />
		<img src="/images/npc_wall_DOWNLOAD_HOVER.jpg" />
		<img src="/images/npc_wall_DOWNLOAD_CLICK.jpg" />
	</div>
	
<body>
</html>