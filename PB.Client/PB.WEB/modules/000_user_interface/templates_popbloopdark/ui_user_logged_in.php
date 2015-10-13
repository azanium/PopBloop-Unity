<div class="withjs">

<script type="text/javascript">
	$(document).ready(function(){
		
		
		$.post("<?php echo $this->basepath; ?>article/admin/getslides", {}, function(data){
			var all_slides = eval('('+data+')');
			
			var html = '';
			
			for(idx = 0; idx < all_slides.length; idx++){
				var _no = all_slides[idx]['no'];
				var _title = all_slides[idx]['title'];
				var _description = all_slides[idx]['description'];
				var _image = all_slides[idx]['image'];
				var _link = all_slides[idx]['link'];
				var _lilo_id = all_slides[idx]['lilo_id'];
				
				html = html + "
				<a href='"+_link+"'>
					<div style='background:url("+_image+") top left no-repeat;'>
						<div style='float:left; width: 440px;'>&nbsp;</div>
						<div style='float:left; width: 500px;'>
							<div style='height:80px; width:100%; font-size:14px; font-weight:normal;'>"+_title+"</div>
							<div style='height:100px; width:100%; font-size:10px; font-weight:normal;'>"+_description+"</div>
						</div>
					</div>
				</a>";
				
//				html = html + "<tr>";
//				html = html + "<td style=width:10px;>"+_no+"</td>";
//				html = html + "<td style='width:200px;'>"+_title+"</td>";
//				html = html + "<td style='width:250px;'><img src='"+_image+"' style='max-width:250; max-height:100px;' /></td>";
//				html = html + "<td style='width:300px;'><textarea style='min-width:300px; min-height:100px; max-width:300px; max-height:100px; font-size:7px; background:transparent; border:0; color:#666;'>"+_description+"</textarea></td>";
//				html = html + "<td style='width:100px;'>Edit<br />Delete</td>";
//				html = html + "</tr>";
			}
			
			html = html + "</html>";
			
			$('#list_slides').html(html);
			
		});

	}

		
	});

</script>

<div class="container_12 pop_20_spacer">
</div>

<div class="container_12">
  <div class="grid_12 pop_news" style="text-align:center">
  	<div id="list_slides">
			<img src="<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/slideshow/001.png" />
		</div>
	</div>
  <div class="clear"></div>
</div>

<div class="container_12 pop_20_spacer">
</div>

<div class="container_12">
	<a href="<?php echo $this->basepath; ?>feature/player-of-the-month">
	<div class="grid_3 feature_box" id="fb_2">
  	<div id="feature_box_2"></div>
    <div class="feature_box_content"><div class="feature_box_content_text">Player of The Month</div></div>
  </div>
  </a>
	<a href="<?php echo $this->basepath; ?>feature/featured-islands">
	<div class="grid_3 feature_box" id="fb_3">
  	<div id="feature_box_3"></div>
    <div class="feature_box_content"><div class="feature_box_content_text">Featured Islands</div></div>
  </div>
  </a>
	<a href="<?php echo $this->basepath; ?>feature/invite-friends">
	<div class="grid_3 feature_box" id="fb_1">
  	<div id="feature_box_1"></div>
    <div class="feature_box_content"><div class="feature_box_content_text">Invite Friends to PopBloop!</div></div>
  </div>
  </a>
	<a href="<?php echo $this->basepath; ?>feature/social">
	<div class="grid_3 feature_box" id="fb_4">
  	<div id="feature_box_4"></div>
    <div class="feature_box_content"><div class="feature_box_content_text">Make Friends and Have Fun!</div></div>
  </div>
  </a>
  <div class="clear"></div>
</div>

<div class="container_12 pop_20_spacer">
</div>

<div class="container_12">
  <div class="grid_6">
		<div class="fb-like" data-href="http://popbloop.com" data-send="true" data-width="450" data-show-faces="true" data-colorscheme="dark" data-font="segoe ui"></div>
    <!--[
    <div class="fb-like" data-href="http://popbloop.com" data-send="true" data-layout="button_count" data-width="450" data-show-faces="true" data-colorscheme="dark" data-font="segoe ui"></div>
    ]-->
	</div>
  <div class="grid_6">
  	&nbsp;
	</div>
</div>


</div><!--[ end withjs ]-->