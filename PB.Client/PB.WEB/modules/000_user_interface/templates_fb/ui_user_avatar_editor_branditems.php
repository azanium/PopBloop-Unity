<link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>

<?php
if(!isset($_SESSION['avatarname'])){
?>

	<script>
		$(document).ready(function(){
			$('#new_avatar_submit').live('click', function(){
				var new_avatar_name = $('#new_avatar_name').val();
				
				if($.trim(new_avatar_name) == ''){
					alert('Dude, please...');
					return;
				}
				
				location.href = '<?php echo $_SESSION['basepath']; ?>ui/user/new_avatar_name/' + new_avatar_name;
			});
		});
	</script>

	<div style="width:817px;">
		<div style="width:817px; height: 393px; margin: 0px auto; background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/input_avatar_name.jpg) top left no-repeat;">
			&nbsp;
			
			<div style="float:left; width:485px; height: 393px;">
				<input type="text" value="<?php echo $this->user_property->avatarname; ?>" name="new_avatar_name" id="new_avatar_name" style="border:0; background-color:#fff; margin-left:202px; margin-top:165px; width: 271px; height:45px; padding-left:10px; font-size:16px; color:#666;" />
			</div>
			<div style="float:left; width:300px; height: 393px;">
				<div id="new_avatar_submit" style="cursor:pointer; margin-top: 165px; margin-left:3px; width: 135px; height:45px;">&nbsp;</div>
			</div>
			
		</div>
	</div>
	
<?php
} else {
?>
	
	<script type="text/javascript">
	
	$(document).ready(function(){
		
		jQuery('.top_menu').live({	// size: 152 x 43	=> 160 x 47, margin-top: 50px => 48px
			mouseup: function(){
				
				// top_menu_avatar, top_menu_items, top_menu_island
				var _id = $(this).attr('id');
				switch(_id){
					case 'top_menu_avatar':
						// set bg pos top_menu_items di top right
						$('#top_menu_items').css('background-position', 'top right');
						// pendekkan top_menu_items
						$('#top_menu_items').css('width', '144px');
						break;
					case 'top_menu_items':
						$('#top_menu_avatar').css('background-position', 'top left');
						$('#top_menu_avatar').css('width', '148px');

						$('#top_menu_island').css('background-position', 'top right');
						$('#top_menu_island').css('width', '148px');

						break;
					case 'top_menu_island':
						$('#top_menu_items').css('background-position', 'top left');
						$('#top_menu_items').css('width', '144px');
						break;
				}

				$(this).css('margin-top', '48px');
				$(this).css('width', '160px');
				$(this).css('height', '47px');
				$(this).css('box-shadow', '0 0 15px #000000');

			},
			mousedown: function(){
				
				// top_menu_avatar, top_menu_items, top_menu_island
				var _id = $(this).attr('id');
				switch(_id){
					case 'top_menu_avatar':
						// set bg pos top_menu_items di top right
						$('#top_menu_items').css('background-position', 'top right');
						// pendekkan top_menu_items
						$('#top_menu_items').css('width', '152px');
						break;
					case 'top_menu_items':
						$('#top_menu_avatar').css('background-position', 'top left');
						$('#top_menu_avatar').css('width', '152px');

						$('#top_menu_island').css('background-position', 'top right');
						$('#top_menu_island').css('width', '152px');
						
						break;
					case 'top_menu_island':
						$('#top_menu_items').css('background-position', 'top left');
						$('#top_menu_items').css('width', '152px');
						break;
				}

				$(this).css('margin-top', '50px');
				$(this).css('width', '152px');
				$(this).css('height', '43px');
				$(this).css('box-shadow', '0 0 0px #000000');


			},
			
			mouseenter: function(){
				// top_menu_avatar, top_menu_items, top_menu_island
				var _id = $(this).attr('id');
				switch(_id){
					case 'top_menu_avatar':
						// set bg pos top_menu_items di top right
						$('#top_menu_items').css('background-position', 'top right');
						// pendekkan top_menu_items
						$('#top_menu_items').css('width', '144px');
						break;
					case 'top_menu_items':
						$('#top_menu_avatar').css('background-position', 'top left');
						$('#top_menu_avatar').css('width', '148px');

						$('#top_menu_island').css('background-position', 'top right');
						$('#top_menu_island').css('width', '148px');

						break;
					case 'top_menu_island':
						$('#top_menu_items').css('background-position', 'top left');
						$('#top_menu_items').css('width', '144px');
						break;
				}

				$(this).css('margin-top', '48px');
				$(this).css('width', '160px');
				$(this).css('height', '47px');
				$(this).css('box-shadow', '0 0 15px #000000');
			},
			
			mouseleave: function(){
				// top_menu_avatar, top_menu_items, top_menu_island
				var _id = $(this).attr('id');
				switch(_id){
					case 'top_menu_avatar':
						// set bg pos top_menu_items di top right
						$('#top_menu_items').css('background-position', 'top right');
						// pendekkan top_menu_items
						$('#top_menu_items').css('width', '152px');
						break;
					case 'top_menu_items':
						$('#top_menu_avatar').css('background-position', 'top left');
						$('#top_menu_avatar').css('width', '152px');

						$('#top_menu_island').css('background-position', 'top right');
						$('#top_menu_island').css('width', '152px');
						
						break;
					case 'top_menu_island':
						$('#top_menu_items').css('background-position', 'top left');
						$('#top_menu_items').css('width', '152px');
						break;
				}

				$(this).css('margin-top', '50px');
				$(this).css('width', '152px');
				$(this).css('height', '43px');
				$(this).css('box-shadow', '0 0 0px #000000');
			}
			
		});
		
	//	// jquery live hover
		jQuery('.hovered').live({
			// string to replace: '_click.', '_hover.', '_normal.'
			
			mouseenter: function(){
				var cur_background = $(this).css('background-image');
				
				cur_background = cur_background.replace('_click.', '_hover.');
				cur_background = cur_background.replace('_normal.', '_hover.');
				
				$(this).css('background-image', cur_background);
				$(this).css('font-size', '20px');
				$(this).css('color', '#ffffff');
	//			alert(cur_background);
	//			jQuery(this).css('background', 'url(<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/landing/button_sign_in_hover.png) no-repeat center transparent');
			},
			mouseleave: function(){
				var cur_background = $(this).css('background-image');
				
				cur_background = cur_background.replace('_click.', '_normal.');
				cur_background = cur_background.replace('_hover.', '_normal.');
				$(this).css('background-image', cur_background);
				$(this).css('font-size', '17px');
				$(this).css('color', '#666666');
	//			jQuery(this).css('background', 'url(<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/landing/button_sign_in_normal.png) no-repeat center transparent');
			},
			mousedown: function(){
				var cur_background = $(this).css('background-image');
				
				cur_background = cur_background.replace('_normal.', '_click.');
				cur_background = cur_background.replace('_hover.', '_click.');
				$(this).css('background-image', cur_background);
				$(this).css('font-size', '17px');
				$(this).css('color', '#ffffff');
	//			jQuery(this).css('background', 'url(<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/landing/button_sign_in_active.png) no-repeat center transparent');
			},
			mouseup: function(){
				var cur_background = $(this).css('background-image');
				
				cur_background = cur_background.replace('_click.', '_hover.');
				cur_background = cur_background.replace('_normal.', '_hover.');
				$(this).css('background-image', cur_background);
				$(this).css('font-size', '17px');
				$(this).css('color', '#ffffff');
	//			jQuery(this).css('background', 'url(<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/landing/button_sign_in_hover.png) no-repeat center transparent');
			}
		});
	});
	</script>
	
	<style>
		.hovered {
			cursor: pointer;
			background-position: 50% 50%;
			font-size: 17px;
		}
		
		.top_banner {
			background: url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/banner.jpg) top center no-repeat;
			height: 133px;
			margin-top: 0;
			margin-bottom: 10px;
		}
		
		.left_pane {
			width: 320px;
			float: left;
			margin-right: 10px;
		}
		.right_pane {
			width: 458px;
			float: left;
		}
		.top_menu{
			height: 43px;
			width: 152px;
			float: right;
			margin-top:50px;
			cursor:pointer;
			
			background-position:left top;
		}
		
		.right_pane_top_menu{
			height: 40;
			margin-bottom: 10px;
		}
		
		.right_pane_top_menu_back{
			width: 112px;
			height: 37px;
			background: url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/back_normal.png) center no-repeat;
			margin-right: 6px;
			float: left;
			margin-bottom: 10px;
		}
		
		.right_pane_top_menu_line{
			width: 340px;
			height: 37px;
			background: url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/left_pane_bg.png) repeat-x;
			float: left;
			margin-bottom: 10px;
		}
		
		.avatar_editor_container{
			margin-bottom: 10px;
		}
		
		.right_pane_content, .right_pane_content_{
			height: 112px;
			width: 112px;
			float: left;
			display: table;
			color:#666;
		}
		
		.right_pane_content_text{
			display: table-cell;
			vertical-align:middle;
			text-align:center;
			/*font-size: 17px;*/
			font-weight:700;
		}
		
		.btn_save{
			width: 122px;
			height: 37px;
			margin: 0 10px 10px 0;
			float:left;
			background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/save_normal.png) center bottom no-repeat;
		}
		
		.btn_save_play{
			width: 185px;
			height: 37px;
			margin: 0 0 10px 0;
			float:left;
			background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/save_play_normal.png) center bottom no-repeat;
		}
		
		.avatar_desc{
			width: 317px;
			height: 37px;
			margin: 0 0 10px 0;
			float:left;
			background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/bar_1_normal.png) center bottom no-repeat;
			display: table;
		}
		
		.avatar_desc_text{
			display: table-cell;
			vertical-align:middle;
			color: #666;
			font-size:9px;
			text-align:center;
		}
		
		.left_pane_divider{
			border:0;
			width:317px;
			background-color:#999;
			height:2px;
			margin: 0 0 10px 0;
			float:left;
		}
		
		.redeem_title{
			border:0;
			width:317px;
			height:20px;
			margin: 0 0 10px 0;
			float:left;
			color:#333;
			font-size:20px;
			text-align:left;
		}
		
		.input_redeem_code{
			width: 207px;
			height: 37px;
			margin: 0 10px 10px 0;
			float:left;
			background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/bar_redeem_code_normal.png) center bottom no-repeat;
			display: table;
		}
	
		.input_redeem_code_text{
			display: table-cell;
			vertical-align:middle;
			color: #333;
			font-size:9px;
			text-align:center;
		}
		
		.button_redeem_code{
			width: 101px;
			height: 37px;
			margin: 0 0 10px 0;
			float:left;
			background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/redeem_code_normal.png) center bottom no-repeat;
		}
		
		.avatarChange, .avatarCategoryChange{
			margin: 5px;
		}
	</style>
	
	<div class style="width:788px;">
		<div style="width:788px; margin: 0px auto;">
			<div class="top_banner" style="text-align:right;">
				
				<div style="height:40px;">
					<!--div style="height:5px;">&nbsp;</div-->
					<!--div class="clear"></div-->
					
					<div style="width:30%; float:left; font-size:30px; color:#fff; text-align:left;"><span style="margin-left:20px;">Avatar Editor</span></div>
					<div style="width:70%; float:left; font-size:18px; color:#fff;">
						<div style="margin-right:10px; display:table; width: 500px; height:40px; float:left;">
							<span style="display:table-cell; vertical-align:middle;">Hi, <?php echo $_SESSION['avatarname']; ?></span>
						</div>
						<div style="float:left;width:40px; height:40px;">
							<img src="<?php echo $_SESSION['basepath']; ?>user_generated_data/profile_picture/<?php echo $this->user_property->profile_picture ?>" style="width:34px; height:34px; margin-top:4px; margin-right:4px; border: 1px solid #fff;" />
						</div>
					</div>
				</div>
				
				<a href="<?php echo $_SESSION['basepath']; ?>ui/user/avatar_editor_categorized"><div class="top_menu hovered" id="top_menu_island" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/edit_your_avatar_normal.png) center bottom no-repeat;">&nbsp;</div></a>
				<div class="top_menu hovered" id="top_menu_items" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/cur_our_items_normal.png) center bottom no-repeat;">&nbsp;</div>
				<div class="top_menu hovered" id="top_menu_avatar" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/visit_the_island_normal.png) center bottom no-repeat;">&nbsp;</div>
			</div>
			<div class="clear"></div>
			
			
			<div class="left_pane">
				<div class="avatar_editor_container">
					<!--[img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/sample.avatar.editor.png" /]-->
					
					<div id="unityPlayer" style="width:319px; height:455px; overflow:hidden;">
						<div class="missing">
							<a href="http://unity3d.com/webplayer/" title="Unity Web Player. Install now!">
								<img alt="Unity Web Player. Install now!" src="<?php print($this->basepath); ?>bundles/webplayer/images/getunity.png" width="380" height="456" />
							</a>
						</div>
					</div>

					
				</div>
				<div class="clear"></div>
				
				<div class="btn_save hovered" id="dialog_link"></div>
				<div class="btn_save_play hovered"></div>
				
				<div class="avatar_desc">
					<span class="avatar_desc_text">Change your look and choose your style! Be what you like!</span>
				</div>
				
				<div class="left_pane_divider">&nbsp;</div>
				
				<div class="redeem_title">Redeem Code</div>
				
				<div class="input_redeem_code">
					<span class="input_redeem_code_text">With redeem code you can purchase and<br>unlock special items. Enter your code here!</span>
				</div>
				<div class="button_redeem_code hovered"></div>
			</div>
			
			
			<div class="right_pane">
				<div class="right_pane_top_menu">
					<div class="right_pane_top_menu_back hovered" id="goto-tab-style">&nbsp;</div>
					<div class="right_pane_top_menu_line">
						<div style="height:14px; font-size: 10px; font-weight: bold; margin-top:2px; color: #000; margin-left: 3px; display:table;">
							<div style="display:table-cell; vertical-align: middle;">
								&nbsp;
							</div>
						</div>
					</div>
					<div class="clear"></div>
					
					<div id="items_container">
						<div class="right_pane_content hovered" id="goto-tab-body" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
							<div class="right_pane_content_text">Top</div>
						</div>
						<div class="right_pane_content hovered" id="goto-tab-pants" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
							<div class="right_pane_content_text">Bottom</div>
						</div>
					
						<div class="right_pane_content hovered" id="goto-tab-shoes" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
							<div class="right_pane_content_text">Foot</div>
						</div>
						<div class="right_pane_content hovered" id="goto-tab-prop" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
							<div class="right_pane_content_text">Prop</div>
						</div>
					</div>
					
					
					
				</div>
			</div>
		</div>
	</div>

	
	
	
	
	
	<input type="hidden" name="skin" id="skin" value="" />
	
	<input type="hidden" name="select_gender" id="select_gender" value="" />
	<input type="hidden" name="select_size" id="select_size" value="" />
	<input type="hidden" name="face_element" id="face_element" value="" />
	<input type="hidden" name="face_material" id="face_material" value="" />

	<input type="hidden" name="hat_element" id="hat_element" value="" />
	<input type="hidden" name="hat_material" id="hat_material" value="" />

	<input type="hidden" name="hand_element" id="hand_element" value="" />
	<input type="hidden" name="hand_material" id="hand_material" value="" />
	
	<input type="hidden" name="hair_element" id="hair_element" value="" />
	<input type="hidden" name="hair_material" id="hair_material" value="" />
	
	<input type="hidden" name="body_element" id="body_element" value="" />
	<input type="hidden" name="body_material" id="body_material" value="" />
	<input type="hidden" name="hand_element" id="hand_element" value="" />
	<input type="hidden" name="hand_material" id="hand_material" value="" />
	<input type="hidden" name="pants_element" id="pants_element" value="" />
	<input type="hidden" name="pants_material" id="pants_material" value="" />
	<input type="hidden" name="shoes_element" id="shoes_element" value="" />
	<input type="hidden" name="shoes_material" id="shoes_material" value="" />

	<input type="hidden" name="eye_brows" id="eye_brows" value="" />
	<input type="hidden" name="eyes" id="eyes" value="" />
	<input type="hidden" name="lip" id="lip" value="" />


	
	




<div style="display:none;">

	<div id="tab-home">
		<input type="hidden" name="tab-home-previous-tab" id="tab-home-previous-tab" value="tab-home" />
		<div class="right_pane_content hovered" id="goto-tab-features" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/feature_normal.png) center no-repeat;">
			<div class="right_pane_content_text">&nbsp;</div>
		</div>
		<div class="right_pane_content hovered" id="goto-tab-style" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/style_normal.png) center no-repeat;">
			<div class="right_pane_content_text">&nbsp;</div>
		</div>
	</div>

	<div id="tab-style">
		<!--input type="hidden" name="tab-style-previous-tab" id="tab-style-previous-tab" value="tab-home" /-->
		<input type="hidden" name="tab-style-previous-tab" id="tab-style-previous-tab" value="tab-style" />
		<div class="right_pane_content hovered" id="goto-tab-body" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
			<div class="right_pane_content_text">Top</div>
		</div>
		<div class="right_pane_content hovered" id="goto-tab-pants" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
			<div class="right_pane_content_text">Bottom</div>
		</div>
	
		<div class="right_pane_content hovered" id="goto-tab-shoes" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
			<div class="right_pane_content_text">Foot</div>
		</div>
		<div class="right_pane_content hovered" id="goto-tab-prop" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
			<div class="right_pane_content_text">Prop</div>
		</div>

	</div>
	
	
	<div id="tab-top">
	</div>
	<div id="tab-bottom">
	</div>
	<div id="tab-prop">
		<input type="hidden" name="tab-prop-previous-tab" id="tab-prop-previous-tab" value="tab-style" />
		<div class="right_pane_content hovered" id="goto-tab-hat" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
			<div class="right_pane_content_text">Hat</div>
		</div>
	</div>
	

	<div id="tab-features">
		<input type="hidden" name="tab-features-previous-tab" id="tab-features-previous-tab" value="tab-home" />
		<div class="right_pane_content hovered" id="goto-tab-gender" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
			<div class="right_pane_content_text">Gender</div>
		</div>
		<div class="right_pane_content hovered" id="goto-tab-bodytype" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
			<div class="right_pane_content_text">Body Type</div>
		</div>
		<div class="right_pane_content hovered" id="goto-tab-skincolor" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
			<div class="right_pane_content_text">Skin Color</div>
		</div>
		<div class="right_pane_content hovered" id="goto-tab-hair" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
			<div class="right_pane_content_text">Hair</div>
		</div>
		<div class="right_pane_content hovered" id="goto-tab-eyes" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
			<div class="right_pane_content_text">Eyes</div>
		</div>
		<div class="right_pane_content hovered" id="goto-tab-eyebrows" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
			<div class="right_pane_content_text">Brows</div>
		</div>
		<div class="right_pane_content hovered" id="goto-tab-lip" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
			<div class="right_pane_content_text">Lips</div>
		</div>
	</div>

	<div id="tab-gender">
		<input type="hidden" name="tab-gender-previous-tab" id="tab-gender-previous-tab" value="tab-features" />
		<div class="right_pane_content_ hovered" id="radio_male" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
			<div class="right_pane_content_text">Male</div>
		</div>
		<div class="right_pane_content_ hovered" id="radio_female" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
			<div class="right_pane_content_text">Female</div>
		</div>
	</div>

	<div id="tab-bodytype">
		<input type="hidden" name="tab-bodytype-previous-tab" id="tab-bodytype-previous-tab" value="tab-features" />
		<div class="right_pane_content_ hovered" id="radio_small" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
			<div class="right_pane_content_text">Small</div>
		</div>
		<div class="right_pane_content_ hovered" id="radio_medium" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
			<div class="right_pane_content_text">Medium</div>
		</div>
		<div class="right_pane_content_ hovered" id="radio_big" style="background:url(<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png) center no-repeat;">
			<div class="right_pane_content_text">Big</div>
		</div>
	</div>

	<div id="tab-skincolor">
		<input type="hidden" name="tab-skincolor-previous-tab" id="tab-skincolor-previous-tab" value="tab-features" />
			<?php
			$color_count = 10;
			$color_width = (540 / $color_count);
			
			for($idx = 1; $idx <= 19; $idx++){
			?>
			
			<div class="skinChange" id="skinChange_<?php echo $idx; ?>" style="width: <?php echo $color_width; ?>px; height: <?php echo $color_width; ?>px; float: left;
						text-align: center; cursor: pointer; background-position: center top;
						background-repeat: no-repeat; margin: 1px; background-color:#FFF;
						background-image: url('<?php echo $this->basepath; ?>bundles/skintones/skin<?php echo $idx; ?>_icon.jpg');" >
						
					</div>
			
			<?php
			}
			?>
	</div>
	
	
	
	<div id="tab-hair">
		<input type="hidden" name="tab-hair-previous-tab" id="tab-hair-previous-tab" value="tab-features" />
		<div id="hair_categories">
			<?php
			$items_array = $this->avatar_array['hair'];
			foreach($items_array as $item){
			?>
			<div 
				id="<?php echo $item['tipe'] . '__' . $item['gender'] . '__' . $item['size']; ?>" title="<?php echo $item['category']; ?>"
				class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> avatarCategoryChange" 
				style="width:100px; height:100px; float:left; text-align:center; margin:5px; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
				<div style="position:relative; top:85px;"><?php echo $item['name']; ?></div>
			</div>
			<?php
			}
			?>
		</div>
		<div id="hair_items" style="display:none">
		
		</div>

	</div>


	<div id="tab-hat">
		<input type="hidden" name="tab-hat-previous-tab" id="tab-hat-previous-tab" value="tab-prop" />
		<!--[setiap opsi adalah gabungan element dan material]-->
		<!--[*face*]-->
		<div id="hat_categories">
			<?php
			$items_array = $this->avatar_array['hat'];
			foreach($items_array as $item){
			?>
			<div 
				id="<?php echo $item['tipe'] . '__' . $item['gender'] . '__' . $item['size']; ?>" title="<?php echo $item['category']; ?>"
				class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> avatarCategoryChange" 
				style="width:100px; height:100px; float:left; text-align:center; margin:5px; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
				<div style="position:relative; top:85px;"><?php echo $item['name']; ?></div>
			</div>
			<?php
			}
			?>
		</div>
		<div id="hat_items" style="display:none">
		
		</div>
		
	</div>

	
	<div id="tab-eyebrows">
		<input type="hidden" name="tab-eyebrows-previous-tab" id="tab-eyebrows-previous-tab" value="tab-features" />
		<div id="face_part_eye_brows_categories">
			<?php
			$items_array = $this->avatar_array['face_part_eye_brows'];
			foreach($items_array as $item){
			?>
			<div 
				id="<?php echo $item['tipe'] . '__' . $item['gender'] . '__' . $item['size']; ?>" title="<?php echo $item['category']; ?>"
				class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> facePartCategoryChange" 
				style="width:100px; height:100px; float:left; text-align:center; margin:5px; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
				<div style="position:relative; top:85px;"><?php echo $item['name']; ?></div>
			</div>
			<?php
			}
			?>
		</div>
		<div id="face_part_eye_brows_items" style="display:none">
		
		</div>

	</div>

	
	<div id="tab-eyes">
		<input type="hidden" name="tab-eyes-previous-tab" id="tab-eyes-previous-tab" value="tab-features" />
		<div id="face_part_eyes_categories">
			<?php
			$items_array = $this->avatar_array['face_part_eyes'];
			foreach($items_array as $item){
			?>
			<div 
				id="<?php echo $item['tipe'] . '__' . $item['gender'] . '__' . $item['size']; ?>" title="<?php echo $item['category']; ?>"
				class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> facePartCategoryChange" 
				style="width:100px; height:100px; float:left; text-align:center; margin:5px; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
				<div style="position:relative; top:85px;"><?php echo $item['name']; ?></div>
			</div>
			<?php
			}
			?>
		</div>
		<div id="face_part_eyes_items" style="display:none">
		
		</div>

	</div>



	<div id="tab-lip" style="overflow-y:auto; height:260px;">
		<input type="hidden" name="tab-lip-previous-tab" id="tab-lip-previous-tab" value="tab-features" />
		<div id="face_part_lip_categories">
			<?php
			$items_array = $this->avatar_array['face_part_lip'];
			foreach($items_array as $item){
			?>
			<div 
				id="<?php echo $item['tipe'] . '__' . $item['gender'] . '__' . $item['size']; ?>" title="<?php echo $item['category']; ?>"
				class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> facePartCategoryChange" 
				style="width:100px; height:100px; float:left; text-align:center; margin:5px; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
				<div style="position:relative; top:85px;"><?php echo $item['name']; ?></div>
			</div>
			<?php
			}
			?>
		</div>
		<div id="face_part_lip_items" style="display:none">
		
		</div>

	</div>


	

	<div id="tab-body">
		<input type="hidden" name="tab-body-previous-tab" id="tab-body-previous-tab" value="tab-style" />
		<div id="body_categories">
			<?php
			$items_array = $this->avatar_array['body'];
			foreach($items_array as $item){
			?>
			<div 
				id="<?php echo $item['tipe'] . '__' . $item['gender'] . '__' . $item['size']; ?>" title="<?php echo $item['category']; ?>"
				class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> avatarCategoryChange" 
				style="width:100px; height:100px; float:left; text-align:center; margin:5px; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
				<div style="position:relative; top:85px;"><?php echo $item['name']; ?></div>
			</div>
			<?php
			}
			?>
		</div>
		<div id="body_items" style="display:none">
		
		</div>
	</div>
	
	<div id="tab-pants">
		<input type="hidden" name="tab-pants-previous-tab" id="tab-pants-previous-tab" value="tab-style" />
		<div id="pants_categories">
			<?php
			$items_array = $this->avatar_array['pants'];
			foreach($items_array as $item){
			?>
			<div 
				id="<?php echo $item['tipe'] . '__' . $item['gender'] . '__' . $item['size']; ?>" title="<?php echo $item['category']; ?>"
				class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> avatarCategoryChange" 
				style="width:100px; height:100px; float:left; text-align:center; margin:5px; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
				<div style="position:relative; top:85px;"><?php echo $item['name']; ?></div>
			</div>
			<?php
			}
			?>
		</div>
		<div id="pants_items" style="display:none">
		
		</div>
	</div>
	<div id="tab-shoes">
		<input type="hidden" name="tab-shoes-previous-tab" id="tab-shoes-previous-tab" value="tab-style" />
		<div id="shoes_categories">
			<?php
			$items_array = $this->avatar_array['shoes'];
			foreach($items_array as $item){
			?>
			<div 
				id="<?php echo $item['tipe'] . '__' . $item['gender'] . '__' . $item['size']; ?>" title="<?php echo $item['category']; ?>"
				class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> avatarCategoryChange" 
				style="width:100px; height:100px; float:left; text-align:center; margin:5px; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
				<div style="position:relative; top:85px;"><?php echo $item['name']; ?></div>
			</div>
			<?php
			}
			?>
		</div>
		<div id="shoes_items" style="display:none">
		
		</div>
	</div>

	
</div>


<div class="image_cache" style="display: none;">
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/back_normal.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/back_hover.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/back_click.png" style="width: 1px; height: 1px;" />
	
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_normal.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_hover.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/item_bg_click.png" style="width: 1px; height: 1px;" />
	
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/bar_1_normal.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/bar_1_hover.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/bar_1_click.png" style="width: 1px; height: 1px;" />

	
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/bar_redeem_code_normal.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/bar_redeem_code_hover.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/bar_redeem_code_click.png" style="width: 1px; height: 1px;" />

	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/edit_your_avatar_normal.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/edit_your_avatar_hover.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/edit_your_avatar_click.png" style="width: 1px; height: 1px;" />

	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/cur_edit_your_avatar_normal.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/cur_edit_your_avatar_hover.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/cur_edit_your_avatar_click.png" style="width: 1px; height: 1px;" />

	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/icon_feature_normal.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/icon_feature_hover.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/icon_feature_click.png" style="width: 1px; height: 1px;" />
	
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/feature_normal.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/feature_hover.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/feature_click.png" style="width: 1px; height: 1px;" />

	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/icon_style_normal.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/icon_style_hover.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/icon_style_click.png" style="width: 1px; height: 1px;" />

	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/style_normal.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/style_hover.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/style_click.png" style="width: 1px; height: 1px;" />

	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/our_items_normal.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/our_items_hover.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/our_items_click.png" style="width: 1px; height: 1px;" />

	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/cur_our_items_normal.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/cur_our_items_hover.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/cur_our_items_click.png" style="width: 1px; height: 1px;" />

	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/redeem_code_normal.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/redeem_code_hover.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/redeem_code_click.png" style="width: 1px; height: 1px;" />

	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/save_normal.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/save_hover.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/save_click.png" style="width: 1px; height: 1px;" />

	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/save_play_normal.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/save_play_hover.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/save_play_click.png" style="width: 1px; height: 1px;" />

	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/visit_the_island_normal.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/visit_the_island_hover.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/visit_the_island_click.png" style="width: 1px; height: 1px;" />

	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/cur_visit_the_island_normal.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/cur_visit_the_island_hover.png" style="width: 1px; height: 1px;" />
	<img src="<?php echo $_SESSION['basepath']; ?>images/fb.pages/airbotol/cur_visit_the_island_click.png" style="width: 1px; height: 1px;" />

	
</div>








<script type="text/javascript">
	$(document).ready(function(){
		
		$('.right_pane_content').live('click', function(){
			var _id = $(this).attr('id');
			if($.trim(_id) != ''){
				var tab_to_load = _id.substring(5);
				
				var _html = $('#' + tab_to_load).html();
				$("#items_container").html(_html);
				
				var prev_tab = $('#' + tab_to_load + '-previous-tab').val();
				
				$('.right_pane_top_menu_back').attr('id', 'goto-' + prev_tab);
				
				// alert(_id.substring(0, 5));
			}
		});
		
		//$('#goto-tab-features').live('click', function(){
		//	var _html = $('#tab-features').html();
		//	$("#items_container").html(_html);
		//	
		//	// ubah id goto-tab-home
		//	$('#goto-tab-home').attr('id', 'goto-tab-home');		// struktur id: goto-[tab-to-load]__[previous-tab-to-load-1]__[previous-tab-to-load-2]
		//	
		//});
		//
		//$('#goto-tab-style').live('click', function(){
		//	var _html = $('#tab-style').html();
		//	$("#items_container").html(_html);
		//	
		//	// ubah id goto-tab-home
		//	$('#goto-tab-home').attr('id', 'goto-tab-home');		// struktur id: goto-[tab-to-load]__[previous-tab-to-load-1]__[previous-tab-to-load-2]
		//	
		//});
		
		$('.right_pane_top_menu_back').live('click', function(){
			var _id = $(this).attr('id');	// goto-tab-home	=> load #tab-home, ubah id-nya jadi apa?
			var _id = _id.replace('goto-', '');
			
			
			
			var _html = $('#' + _id).html();
			$("#items_container").html(_html);
			
			
			// dapatkan previous-tab
			var prev_tab = $('#' + _id + '-previous-tab').val();
			
			$('.right_pane_top_menu_back').attr('id', 'goto-' + prev_tab);
			
			// ubah id goto-tab-home
			// $('#goto-tab-home').attr('id', 'goto-tab-home');
		});
		
		
	});
</script>



	
	
	
	
<script type="text/javascript" src="<?php print($this->basepath); ?>libraries/js/UnityObject.js"></script>
<script type="text/javascript">
<!--
function GetUnity() {
	if (typeof unityObject != "undefined") {
		return unityObject.getObjectById("unityPlayer");
	}
	return null;
}
if (typeof unityObject != "undefined") {

	var params = {
		disableContextMenu: true,
		backgroundcolor: "FFFFFF",/*2c2c2c*/
		bordercolor: "FFFFFF",
		textcolor: "000000",
		logoimage: "<?php print($this->basepath); ?>bundles/webplayer/images/lilologo.small.png",
	};

	unityObject.embedUnity("unityPlayer", "<?php print($this->basepath); ?>bundles/webplayer/AvatarEditor.unity3d?<?php echo time(); ?>", 319, 455, params);
	
}

function sleep(milliseconds) {
	var start = new Date().getTime();
	for (var i = 0; i < 1e7; i++) {
		if ((new Date().getTime() - start) > milliseconds){
			alert('Loading...');
			break;
		}
	}
}



function OnLiloLoaded() {
	GetUnity().SendMessage("_DressRoom", "ChangePlayerId", "<?php echo $_SESSION['user_id']; ?>");
	
	$("#config_form").show();

	//$.post("<?php echo $this->basepath; ?>avatar/user/get_configuration", {}, function(data){
	//	GetUnity().SendMessage("_DressRoom", "ChangeCharacterEvent", data);
	//	
	//	var config_obj = eval('(' + data + ')');
	//	
	//	// hat di-reset dulu
	//	$("#hat_element").val('');
	//	$("#hat_material").val('');
	//	
	//	for(i = 0; i < config_obj.length; i++){
	//		var tipe = config_obj[i].tipe.toLowerCase();
	//		if(tipe == 'gender'){//sleep(2000);
	//			var gender = config_obj[i].element;
	//			var gender_ = gender.replace("_base", "");
	//			$("#select_gender").val(gender_);
	//			
	//			$('#current_gender').html(gender_);
	//		} else if(tipe == 'face'){//sleep(2000);
	//			// 'tipe':'Face','element':'male_face-1','material':'male_face-1','eye_brows':'brows','eyes':'eyes-2','lip':'lip'
	//			var element 	= config_obj[i].element;
	//			var material 	= config_obj[i].material;
	//			var gender_ = $("#select_gender").val();
	//			var message = "{'gender':'"+gender_+"','tipe':'"+tipe+"','element':'"+element+"','material':'"+material+"'}";
	//			GetUnity().SendMessage("_DressRoom", "ChangeElementEvent", message);
	//
	//			$("#" + tipe + "_element").val(element);
	//			$("#" + tipe + "_material").val(material);
	//
	//			// yg diperlukan Hendra:
	//			// {'tipe':'face','element':'male_face-1','eye_brows':'brows','eyes':'eyes','lip':'lip'},
	//			var eye_brows = config_obj[i].eye_brows;
	//			var eyes 			= config_obj[i].eyes;
	//			var lip 			= config_obj[i].lip;
	//			// ChangeFacePartEvent("{'tipe':'eyes','element':'eyes'}");
	//			message = "{'tipe':'eye_brows','element':'"+eye_brows+"'}";
	//			GetUnity().SendMessage("_DressRoom", "ChangeFacePartEvent", message);
	//			message = "{'tipe':'eyes','element':'"+eyes+"'}";
	//			GetUnity().SendMessage("_DressRoom", "ChangeFacePartEvent", message);
	//			message = "{'tipe':'lip','element':'"+lip+"'}";
	//			GetUnity().SendMessage("_DressRoom", "ChangeFacePartEvent", message);
	//
	//			$("#eye_brows").val(eye_brows);
	//			$("#eyes").val(eyes);
	//			$("#lip").val(lip);
	//		} else if(tipe == 'skin'){//alert('Downloading ' + tipe + '...');//sleep(2000);
	//			var color = config_obj[i].color;
	//			color = parseInt(color);
	//			$("#skin").val(color);
	//			GetUnity().SendMessage("_DressRoom", "ChangeSkinColor", color);
	//		} else {
	//			var element = config_obj[i].element;
	//			var material = config_obj[i].material;
	//			$("#" + tipe + "_element").val(element);
	//			$("#" + tipe + "_material").val(material);
	//			
	//			var gender_ = $("#select_gender").val();
	//			var message = "{'gender':'"+gender_+"','tipe':'"+tipe+"','element':'"+element+"','material':'"+material+"'}";
	//			GetUnity().SendMessage("_DressRoom", "ChangeElementEvent", message);
	//			
	//		}
	//	}
	//});

}


function get_session_id(){
	var session_id = "<?php echo $this->session_id; ?>";

	GetUnity().SendMessage("_DressRoom", "GetUserId", session_id);
}

function get_session(){
  return "<?php echo $this->session_id; ?>";
}

-->
</script>


<script language="javascript">
	$(document).ready(function(){
		

		$('#unityPlayer').live('click', function(){
			$('#unityPlayer').css('height', '456px');
		});
		
		$('#dialog_link, ul#icons li').hover(
			function() { $(this).addClass('ui-state-hover'); }, 
			function() { $(this).removeClass('ui-state-hover'); }
		);
		
		$('.radio_gender, ul#icons li').hover(
			function() { $(this).addClass('ui-state-hover'); }, 
			function() { $(this).removeClass('ui-state-hover'); }
		);
		
//		$('#tabs').tabs();
//		$('#face_part_tabs').tabs();

		$.post("<?php echo $this->basepath; ?>avatar/user/get_gender", {}, function(data){// alert('avatar/user/get_gender: ' + data);
			$("body").css("cursor", "progress");
			$('.loading_div').show();
			$('#select_gender').val(data);
			if(data == 'male'){
				$(".male").show();
				$(".female").hide();
			} else {
				$(".female").show();
				$(".male").hide();
			}
			$("body").css("cursor", "auto");
			$('.loading_div').hide();
		});

		$.post("<?php echo $this->basepath; ?>avatar/user/get_size", {}, function(data){
			$("body").css("cursor", "progress");
			$('.loading_div').show();
			var gender = $('#select_gender').val();
			var gender_op = (gender == 'male') ? 'female' : 'male';
			$('#select_size').val(data);
			if(data == 'small' || data == 'thin'){
				$(".big, .fat").hide();
				$(".medium").hide();
				$(".small, .thin").show();
			} else if(data == 'medium'){
				$(".big, .fat").hide();
				$(".small, .thin").hide();
				$(".medium").show();
			} else if(data == 'big' || data == 'fat'){
				$(".small, .thin").hide();
				$(".medium").hide();
				$(".big, .fat").show();
			}
			$("." + gender_op).hide();
			$("body").css("cursor", "auto");
			$('.loading_div').hide();
		});
		

		function avatar_config_update_form(avatar_config){	// ga ngeset gender dan size, krn udah di set di fungsi yg akan panggil fungsi ini
//			contoh message = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'male_hair_02','material':'male_hair_02','element2':'male_hair_02_bottom','material2':'male_hair_02'},{'tipe':'Body','element':'male_body_thin','material':'male_body'},{'tipe':'Pants','element':'male_short_pant_thin','material':'male_short_pant'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";

			$("#hat_element").val('');
			$("#hat_material").val('');

			var message_eval = eval('(' + avatar_config + ')');
			for(i = 0; i < message_eval.length; i++){
				var tipe = message_eval[i].tipe.toLowerCase();

				switch(tipe){
					case 'face':
						$("#face_element").val(message_eval[i].element);
						$("#face_material").val(message_eval[i].material);
						$("#eye_brows").val(message_eval[i].eye_brows);
						$("#eyes").val(message_eval[i].eyes);
						$("#lip").val(message_eval[i].lip);
					break;
					
					case 'hair':
					case 'hat':
					case 'body':
					case 'pants':
					case 'shoes':
						$("#" + tipe + "_element").val(message_eval[i].element);
						$("#" + tipe + "_material").val(message_eval[i].material);//alert(tipe + " - " + message_eval[i].element);
					break;
					
					case 'skin':
						$("#skin").val(message_eval[i].color);
					break;
				}

			}
		}
		

		$('#radio_small').live('click', function(){
			$("body").css("cursor", "progress");
			var gender = $("#select_gender").val();
			
			var gender_op = (gender == 'male') ? 'female' : 'male';
			
			$("#select_size").val('small');
			
			$('.' + gender).show();
			$('.medium').hide();
			$('.big, .fat').hide();
			$('.small, .thin').show();
			$('.' + gender_op).hide();
			
			// load avatar sesuai gender dan size ('small')

			
			// versi baca dari setting sebelumnya
			var body_element = $('#body_element').val();
			body_element = body_element.replace('_fat', '_thin');
			body_element = body_element.replace('_medium', '_thin');
			
			var pants_element = $('#pants_element').val();
			pants_element = pants_element.replace('_fat', '_thin');
			pants_element = pants_element.replace('_medium', '_thin');
			
			// hat, cek dulu apakah ada atau tidak
			var hat_element			= $("#hat_element").val();
			var hat_material		= $("#hat_material").val();
			
			var message_male = '';
			var message_female = '';
			
			if($.trim(hat_element) == ''){
				message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
				message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
			} else {
				message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Hat','element':'"+$('#hat_element').val()+"','material':'"+$('#hat_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
				message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Hat','element':'"+$('#hat_element').val()+"','material':'"+$('#hat_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
			}
			
			
			

			var message = (gender == 'male') ? message_male : message_female;

			GetUnity().SendMessage("_DressRoom", "ChangeCharacterEvent", message);
			
			avatar_config_update_form(message);
			$('#current_size').html('thin');
			$("body").css("cursor", "auto");
		});
		
		
		$('#radio_medium').live('click', function(){
			$("body").css("cursor", "progress");
			var gender = $("#select_gender").val();
			
			var gender_op = (gender == 'male') ? 'female' : 'male';
			
			$("#select_size").val('medium');
			
			$('.' + gender).show();
			$('.small, .thin').hide();
			$('.big, .fat').hide();
			$('.medium').show();
			$('.' + gender_op).hide();
			
			// load avatar sesuai gender dan size ('medium')


			// versi baca dari setting sebelumnya
			var body_element = $('#body_element').val();
			body_element = body_element.replace('_fat', '_medium');
			body_element = body_element.replace('_thin', '_medium');
			
			var pants_element = $('#pants_element').val();
			pants_element = pants_element.replace('_fat', '_medium');
			pants_element = pants_element.replace('_thin', '_medium');
			
			// hat, cek dulu apakah ada atau tidak
			var hat_element			= $("#hat_element").val();
			var hat_material		= $("#hat_material").val();
			
			var message_male = '';
			var message_female = '';
			
			if($.trim(hat_element) == ''){
				message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
				message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
			} else {
				message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Hat','element':'"+$('#hat_element').val()+"','material':'"+$('#hat_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
				message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Hat','element':'"+$('#hat_element').val()+"','material':'"+$('#hat_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
			}

			var message = (gender == 'male') ? message_male : message_female;
			
			GetUnity().SendMessage("_DressRoom", "ChangeCharacterEvent", message);

			avatar_config_update_form(message);
			$('#current_size').html('medium');
			$("body").css("cursor", "auto");
		});
		
		
		$('#radio_big').live('click', function(){
			$("body").css("cursor", "progress");
			var gender = $("#select_gender").val();

			var gender_op = (gender == 'male') ? 'female' : 'male';
			
			$("#select_size").val('big');
			
			$('.' + gender).show();
			$('.medium').hide();
			$('.small, .thin').hide();
			$('.big, .fat').show();
			$('.' + gender_op).hide();
			
			// load avatar sesuai gender dan size ('big')
			

			// versi baca dari setting sebelumnya
			var body_element = $('#body_element').val();
			body_element = body_element.replace('_thin', '_fat');
			body_element = body_element.replace('_medium', '_fat');
			
			var pants_element = $('#pants_element').val();
			pants_element = pants_element.replace('_thin', '_fat');
			pants_element = pants_element.replace('_medium', '_fat');
			

			// hat, cek dulu apakah ada atau tidak
			var hat_element			= $("#hat_element").val();
			var hat_material		= $("#hat_material").val();
			
			var message_male = '';
			var message_female = '';
			
			if($.trim(hat_element) == ''){
				message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
				message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
			} else {
				message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Hat','element':'"+$('#hat_element').val()+"','material':'"+$('#hat_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
				message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Hat','element':'"+$('#hat_element').val()+"','material':'"+$('#hat_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
			}




			var message = (gender == 'male') ? message_male : message_female;
			
			GetUnity().SendMessage("_DressRoom", "ChangeCharacterEvent", message);

			avatar_config_update_form(message);
			$('#current_size').html('fat');
			$("body").css("cursor", "auto");
		});
		
		// alert('woi...');
		
		$("#radio_female").live('click', function(){	// tambahkan selector size disini!
			
			$("body").css("cursor", "progress");
			var size = $("#select_size").val();
			
			if($.trim(size) == ''){
				$("#select_size").val('medium');
				size = 'medium';
			}
			
			// alert(size);
			var size_op = (size == 'big' || size == 'fat') ? ".medium, .small, .thin" : ((size == 'medium') ? ".big, .fat, .small, .thin" : ".big, .fat, .medium" ) ;
			//alert(size_op);
			$('.' + size).show();
			$(".female").show();
			$(size_op).hide();
			$(".male").hide();
			
			
			$("#select_gender").val("female");
			
			
			//	'avatar_config_' . $gender . '_' . $size
			var message_fat = "<?php echo $this->avatar_config_female_big; ?>";
			var message_medium = "<?php echo $this->avatar_config_female_medium; ?>";
			var message_thin = "<?php echo $this->avatar_config_female_small; ?>";


			var message = message_fat;
			var size_ = 'fat';
			if(size == 'medium'){
				message = message_medium;
				size_ = 'medium';
			} else if(size == 'small'){
				message = message_thin;
				size_ = 'thin';
			}
			
			
			GetUnity().SendMessage("_DressRoom", "ChangeCharacterEvent", message);
			
			updateAvatarConfInput(message);
			$("body").css("cursor", "auto");
			
		});
		
		$("#radio_male").live('click', function(){
			$("body").css("cursor", "progress");
			var size = $("#select_size").val();
			
			if($.trim(size) == ''){
				$("#select_size").val('medium');
				size = 'medium';
			}
			// alert(size);
			
			var size_op = (size == 'big' || size == 'fat') ? ".medium, .small, .thin" : ((size == 'medium') ? ".big, .fat, .small, .thin" : ".big, .fat, .medium" ) ;
			
			//alert(size_op);
			$('.' + size).show();
			$(".male").show();
			$(size_op).hide();
			$(".female").hide();
			$("#select_gender").val("male");


			//	'avatar_config_' . $gender . '_' . $size
			var message_fat = "<?php echo $this->avatar_config_male_big; ?>";
			var message_medium = "<?php echo $this->avatar_config_male_medium; ?>";
			var message_thin = "<?php echo $this->avatar_config_male_small; ?>";

			
			var message = message_fat;
			var size_ = 'fat';
			if(size == 'medium'){
				message = message_medium;
				size_ = 'medium';
			} else if(size == 'small'){
				message = message_thin;
				size_ = 'thin';
			}
			
			
			GetUnity().SendMessage("_DressRoom", "ChangeCharacterEvent", message);
			
			updateAvatarConfInput(message);
			$("body").css("cursor", "auto");
			
		});

		$('.facePartChange, .avatarChange, .facePartCategoryChange, .avatarCategoryChange').hover(
			function(){
				$(this).addClass('shadow');
				$(this).addClass('light_blue_bg');
			}, 
			function(){
				$(this).removeClass("shadow");
				$(this).removeClass('light_blue_bg');
			}
		);

		$(".facePartChange").live('click', function(){
			$("body").css("cursor", "progress");
			$('#saved_notification').hide();
			// ChangeFacePartEvent("{'tipe':'eyes','element':'eyes'}");
			
			// ubah value di hidden input: 
			//					eye_brows
			//					eyes
			//					lip
			var _id = $(this).attr('id');	// tipe__element? no, yg bener: tipe____material
			var _id_split = _id.split('____');
			
			// face_part_eyes -> eyes
			_id_split[0] = _id_split[0].replace('face_part_', '');

			var message = "{'tipe':'"+_id_split[0]+"','element':'"+_id_split[1]+"'}";
			
			GetUnity().SendMessage("_DressRoom", "ChangeFacePartEvent", message);
			
			if(_id_split[0] == 'eyeBrows'){
				_id_split[0] = 'eye_brows';
			}
			
			$("#" + _id_split[0]).val(_id_split[1]);
			$("body").css("cursor", "auto");
			
		});

		$(".avatarChange").live('click', function(){
			$("body").css("cursor", "progress");
			$('#saved_notification').hide();
			var _id = $(this).attr('id');	// format id: eyes-male_eyes-male_eyes_blue, tipe-element-material
																		// eyes__male_eyes__male_eyes_green
			var _id_split = _id.split('__');
			var gender = $("#select_gender").val();
			
				var message = "{'gender':'"+gender+"','tipe':'"+_id_split[0]+"','element':'"+_id_split[1]+"','material':'"+_id_split[2]+"'}";
			
			GetUnity().SendMessage("_DressRoom", "ChangeElementEvent", message);
			
			$("#" + _id_split[0] + "_element").val(_id_split[1]);
			$("#" + _id_split[0] + "_material").val(_id_split[2]);
			$("body").css("cursor", "auto");
			
		});
		
		$('.facePartCategoryChange').live('click', function(){
			$("body").css("cursor", "progress");
			$('.loading_div').show();
			var _id = $(this).attr('id');
			var _id_split = _id.split('__');
			var tipe = _id_split[0];
			var gender = _id_split[1];
			var size = _id_split[2];
			
			var category = $(this).attr('title');
			
			var background_image = $(this).css('background-image');
			
			$.post("<?php echo $this->basepath; ?>avatar/user/avatar_category", {tipe: tipe, gender: gender, category: category, size: size, background_image: background_image}, function(data){
				$('#' + tipe + '_categories').hide();
				$('#' + tipe + '_items').html(data);
				$('#' + tipe + '_items').show();
				
				var prev_tab = '';
				
				switch(tipe){
					case 'face_part_eyes':
						prev_tab = 'tab-eyes';
						break;
					
					case 'face_part_eye_brows':
						prev_tab = 'tab-eyebrows';
						break;
					
					case 'face_part_lip':
						prev_tab = 'tab-lip';
						break;
				}
				
				$('.right_pane_top_menu_back').attr('id', 'goto-' + prev_tab);
			});

			$('.loading_div').hide();
			$("body").css("cursor", "auto");
			
		});
		
		$('.avatarCategoryChange').live('click', function(){
			$("body").css("cursor", "progress");
			$('.loading_div').show();
			var _id = $(this).attr('id');
			var _id_split = _id.split('__');
			var tipe = _id_split[0];
			var gender = _id_split[1];
			var size = _id_split[2];
			
			var category = $(this).attr('title');

			
			$.post("<?php echo $this->basepath; ?>avatar/user/avatar_category", {tipe: tipe, gender: gender, category: category, size: size/*, background_image: background_image*/}, function(data){
				$('#' + tipe + '_categories').hide();
				$('#' + tipe + '_items').html(data);
				$('#' + tipe + '_items').show();
				
//				alert(tipe);
				
				var prev_tab = 'tab-' + tipe;
				//switch(tipe){	// shoes, body, pants, hat
				//	case 'shoes':
				//		prev_tab = 'tab-shoes';
				//		break;
				//	case 'body':
				//		prev_tab = 'tab-body';
				//		break;
				//	case 'pants':
				//		prev_tab = 'tab-pants';
				//		break;
				//	case 'hat':
				//		prev_tab = 'tab-hat';
				//		break;
				//}
				
				$('.right_pane_top_menu_back').attr('id', 'goto-' + prev_tab);
			
			});

			$('.loading_div').hide();
			$("body").css("cursor", "auto");

		});
		

		$("#updateBtn").live('click', function(){
			var gender = $("#select_gender").val();
			var tipe = $("#select_tipe").val();
			var element = $("#select_element_" + gender).val();
			var material = $("#select_material_" + gender).val();
			
			var message = "{'gender':'"+gender+"','tipe':'"+tipe+"','element':'"+element+"','material':'"+material+"'}";
			
			GetUnity().SendMessage("_DressRoom", "ChangeElementEvent", message);

		});
		
		$("#dialog_link").live('click', function(){	// belum selesai......
			$("body").css("cursor", "progress");
			$('.loading_div').show();
			var gender 					= $("#select_gender").val();
			var size 						= $("#select_size").val();
			var tipe 						= $("#select_tipe").val();
			var element 				= $("#select_element_" + gender).val();
			var material 				= $("#select_material_" + gender).val();
			
			var skin						= $("#skin").val();
			
			var hat_element			= $("#hat_element").val();
			var hat_material		= $("#hat_material").val();

			var face_element		= $("#face_element").val();
			var face_material		= $("#face_material").val();
			var hair_element		= $("#hair_element").val();
			var hair_material		= $("#hair_material").val();
			//var hair_element2		= $("#hair_element2").val();
			//var hair_material2	= $("#hair_material2").val();
			var body_element		= $("#body_element").val();
			var body_material		= $("#body_material").val();
			var hand_element		= $("#hand_element").val();
			var hand_material		= $("#hand_material").val();
			var pants_element		= $("#pants_element").val();
			var pants_material	= $("#pants_material").val();
			var shoes_element		= $("#shoes_element").val();
			var shoes_material	= $("#shoes_material").val();
			var eye_brows				= $("#eye_brows").val();
			var eyes						= $("#eyes").val();
			var lip							= $("#lip").val();

			
			var message = "[{'tipe':'gender','element':'"+ gender +"_base'},{'tipe':'Face','element':'"+face_element+"','material':'"+face_material+"','eye_brows':'"+eye_brows+"','eyes':'"+eyes+"','lip':'"+lip+"'},{'tipe':'Hair','element':'"+hair_element+"','material':'"+hair_material+"'},{'tipe':'Hat','element':'"+hat_element+"','material':'"+hat_material+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+body_material+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+pants_material+"'},{'tipe':'Shoes','element':'"+shoes_element+"','material':'"+shoes_material+"'},{'tipe':'Hand','element':'"+hand_element+"','material':'"+hand_material+"'}, {'tipe':'Skin','color':'"+skin+"'}]";

			if($.trim(hat_element) == ''){
				var message = "[{'tipe':'gender','element':'"+ gender +"_base'},{'tipe':'Face','element':'"+face_element+"','material':'"+face_material+"','eye_brows':'"+eye_brows+"','eyes':'"+eyes+"','lip':'"+lip+"'},{'tipe':'Hair','element':'"+hair_element+"','material':'"+hair_material+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+body_material+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+pants_material+"'},{'tipe':'Shoes','element':'"+shoes_element+"','material':'"+shoes_material+"'},{'tipe':'Hand','element':'"+hand_element+"','material':'"+hand_material+"'}, {'tipe':'Skin','color':'"+skin+"'}]";
			}

			// update the database
			$.post("<?php echo $_SESSION['basepath']; ?>avatar/user/set_configuration", {avatar_conf: message, size: size}, function(data){
				if(data == "1"){
					$('#saved_notification').show();
					var t = setTimeout("$('#saved_notification').hide()", 3000);
				}
				$("body").css("cursor", "auto");
				$('.loading_div').hide();
			});
		});

		$('.skinChange').live('click', function(){
			var _id = $(this).attr('id');
			var _id_split = _id.split('_');
			skin_idx = parseInt(_id_split[1]);
			GetUnity().SendMessage("_DressRoom", "ChangeSkinColor", skin_idx);
			$('#skin').val(skin_idx);
		});
		
		
		function updateAvatarConfInput(data){
			var config_obj = eval('(' + data + ')');
			
			// hat di-reset dulu
			$("#hat_element").val('');
			$("#hat_material").val('');
			
			for(i = 0; i < config_obj.length; i++){
				var tipe = config_obj[i].tipe.toLowerCase();
				if(tipe == 'gender'){//sleep(2000);
					var gender = config_obj[i].element;
					var gender_ = gender.replace("_base", "");
					$("#select_gender").val(gender_);
					
					$('#current_gender').html(gender_);
				} else if(tipe == 'face'){//sleep(2000);
					// 'tipe':'Face','element':'male_face-1','material':'male_face-1','eye_brows':'brows','eyes':'eyes-2','lip':'lip'
					var element 	= config_obj[i].element;
					var material 	= config_obj[i].material;
					var gender_ = $("#select_gender").val();
//					var message = "{'gender':'"+gender_+"','tipe':'"+tipe+"','element':'"+element+"','material':'"+material+"'}";
//					GetUnity().SendMessage("_DressRoom", "ChangeElementEvent", message);
	
					$("#" + tipe + "_element").val(element);
					$("#" + tipe + "_material").val(material);
	
					// yg diperlukan Hendra:
					// {'tipe':'face','element':'male_face-1','eye_brows':'brows','eyes':'eyes','lip':'lip'},
					var eye_brows = config_obj[i].eye_brows;
					var eyes 			= config_obj[i].eyes;
					var lip 			= config_obj[i].lip;
					// ChangeFacePartEvent("{'tipe':'eyes','element':'eyes'}");
//					message = "{'tipe':'eye_brows','element':'"+eye_brows+"'}";
//					GetUnity().SendMessage("_DressRoom", "ChangeFacePartEvent", message);
//					message = "{'tipe':'eyes','element':'"+eyes+"'}";
//					GetUnity().SendMessage("_DressRoom", "ChangeFacePartEvent", message);
//					message = "{'tipe':'lip','element':'"+lip+"'}";
//					GetUnity().SendMessage("_DressRoom", "ChangeFacePartEvent", message);
	
					$("#eye_brows").val(eye_brows);
					$("#eyes").val(eyes);
					$("#lip").val(lip);
				} else if(tipe == 'skin'){//alert('Downloading ' + tipe + '...');//sleep(2000);
					var color = config_obj[i].color;
					color = parseInt(color);
					$("#skin").val(color);
//					GetUnity().SendMessage("_DressRoom", "ChangeSkinColor", color);
				} else {
					var element = config_obj[i].element;
					var material = config_obj[i].material;
					$("#" + tipe + "_element").val(element);
					$("#" + tipe + "_material").val(material);
//					var gender_ = $("#select_gender").val();
//					var message = "{'gender':'"+gender_+"','tipe':'"+tipe+"','element':'"+element+"','material':'"+material+"'}";
//					GetUnity().SendMessage("_DressRoom", "ChangeElementEvent", message);
					
				}
			}

		}
		

	});

</script>

	
	
	
	
	
	
	
	
	
	
	
	
	
	
<?php
}
?>
