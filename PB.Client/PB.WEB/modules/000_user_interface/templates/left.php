<?php
if($this->logged_in){
?>
	
<script language="javascript">
	$(document).ready(function(){

		$(".arrow_left").click(function(){
			// dapatkan current id
			var _id = $(this).attr('id');
			var _id_split = _id.split('_');
			
			// current div
			var _div_id = "float_div_left_" + _id_split[1];
			
			$("#" + _div_id).animate({'left':'0','width':'30'});
			
			$("#al_" + _id_split[1]).hide();
			$("#ar_" + _id_split[1]).show();
		});
		
		$(".arrow_right").click(function(){
			var _id = $(this).attr('id');
			var _id_split = _id.split('_');

			// current div
			var _div_id = "float_div_left_" + _id_split[1];

			$(".float_div_left").each(function(){
				var cur_id = $(this).attr('id');
				if(cur_id != _div_id){
					$(this).animate({'left':'0','width':'30'});
					// ubah arrow jadi arrow right
					var cur_arrow = cur_id;
					cur_arrow = cur_arrow.replace("float_div_left_", "al_");
					$("#" + cur_arrow).hide();
					cur_arrow = cur_arrow.replace("al_", "ar_");
					$("#" + cur_arrow).show();
				} else {
					$(this).animate({'width':'+=200','right':'-=200'});
				}
			});
			
			// current arrow
			$("#ar_" + _id_split[1]).hide();
			$("#al_" + _id_split[1]).show();
			
			
		});

		$(".arrow_right").hover(
			function(){
				$(this).attr("src", "<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/arrow.right.hover.png");
			},
			function(){
				$(this).attr("src", "<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/arrow.right.png");
			}
		);

		$(".arrow_left").hover(
			function(){
				$(this).attr("src", "<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/arrow.left.hover.png");
			},
			function(){
				$(this).attr("src", "<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/arrow.left.png");
			}
		);

		
	});
</script>

<div id="float_div_left_statistics" class="float_div_left shadow transparent_70">
	<img class="arrow_right" id="ar_statistics" style="right:4px; top:4px; position:absolute; cursor:pointer;" src="<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/arrow.right.png" />
	<img class="arrow_left" id="al_statistics" style="right:4px; top:4px; position:absolute; display:none; cursor:pointer;" src="<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/arrow.left.png" />
  
  <div id="content_statistics" style="right:0px; top:22px; width:32px; position:absolute;">
		<img src="<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/left.menu.statistics.png" />
  </div>
  
</div>

<div id="float_div_left_sponsors" class="float_div_left shadow transparent_70" style="top:230px; position:fixed; background-color:#ff6633;">
	<img class="arrow_right" id="ar_sponsors" style="right:4px; top:4px; position:absolute; cursor:pointer;" src="<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/arrow.right.png" />
	<img class="arrow_left" id="al_sponsors" style="right:4px; top:4px; position:absolute; display:none; cursor:pointer;" src="<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/arrow.left.png" />
  
  <div id="content_sponsors" style="right:40px; width:200px; position:absolute;">Ads</div>

</div>

<div id="float_div_left_news" class="float_div_left shadow transparent_70" style="top:370px; position:fixed; background-color:#C6F;">
	<img class="arrow_right" id="ar_news" style="right:4px; top:4px; position:absolute; cursor:pointer;" src="<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/arrow.right.png" />
	<img class="arrow_left" id="al_news" style="right:4px; top:4px; position:absolute; display:none; cursor:pointer;" src="<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/arrow.left.png" />
  
  <div id="content_news" style="right:40px; width:200px; position:absolute;">News</div>

</div>

<?php
}	// end if logged_in
?>
