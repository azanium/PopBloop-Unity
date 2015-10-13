<script type="text/javascript" src="<?php echo $this->basepath; ?>libraries/js/claviska-jquery-miniColors/jquery.miniColors.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo $this->basepath; ?>libraries/js/claviska-jquery-miniColors/jquery.miniColors.css" />


<style type="text/css">
.preview_avatar {font-size:9px; z-index:9;}
</style>
<script language="javascript">
	function capitaliseFirstLetter(string){
		return string.charAt(0).toUpperCase() + string.slice(1);
	}

	function hexToR(h) {return parseInt((cutHex(h)).substring(0,2),16)}
	function hexToG(h) {return parseInt((cutHex(h)).substring(2,4),16)}
	function hexToB(h) {return parseInt((cutHex(h)).substring(4,6),16)}
	function cutHex(h) {return (h.charAt(0)=="#") ? h.substring(1,7):h}

	$(document).ready(function(){
		// color picker di add avatar item
		$('#avatar_add_color').miniColors();
		
		// Tabs
		$('#tabs').tabs({selected: <?php echo $this->active_tabs; ?>});
		$('#face_part_tabs').tabs({selected: <?php echo $this->active_face_part_tabs; ?>});
		
		$("#editor_div").dialog({
			autoOpen: false, 
			minWidth: 820, 
			minHeight: 400,
			modal: true,

			buttons: [
				{
					text: "Save Changes",
					click: function() {
						$('#avatar_update').submit();
						return;
//						alert('save...'); 
					}
				},
				
				{
					text: "Delete This Avatar Element",
					click: function() { 
						if(confirm('Are you sure to delete this avatar element?')){
							var active_avatar_part = $('#active_avatar_part').val();	// face__female_female_head__
																																				// pants__female_female_longpants_medium__
							alert('Hapus...' + active_avatar_part);
							$.post("<?php echo $this->basepath; ?>asset/admin/avatar_delete/" + active_avatar_part, {}, function(data){
								if(data == "1"){
									$('#' + active_avatar_part).hide('slow');
								} else {
									alert('Data gagal dihapus...' + data);
								}
							});
							$(this).dialog("close");
						} else {
							alert('Ga dihapus...!!');
						}
					}
				},
				
				{
					text: "Cancel",
					click: function() { $(this).dialog("close"); }
				},
				
			]

		});
		
		$('.preview_avatar').hover(
			function(){
				$(this).addClass('shadow');
//				alert($(this).offset().left);
//				alert($(this).offset().top);
//				$("#editor_div").show();
//				$("#editor_div").offset({top: $(this).offset().top + 2, left: $(this).offset().left + 2});

				
			}, 
			function(){
				$(this).removeClass("shadow");
//				$("#editor_div").hide();
//				$("#active_avatar_part").val('');
			}
		);
		
		$('.preview_avatar').click(function(){
			$("#active_avatar_part").val($(this).attr('id'));
//			alert($("#active_avatar_part").val());	//	hair__male_hair-1__male_hair-1_brown
			
			// dapatkan detail avatar part terpilih
			var lilo_id = $(this).attr('title');
			// alert(lilo_id);
			
			$('#lilo_id').val(lilo_id);
			
			var avatar_part = $(this).attr('id');
			$.post("<?php echo $this->basepath; ?>asset/admin/avatar_detail", {lilo_id: lilo_id}, function(data){
				// alert(data);
				var avatar_detail = eval('(' + data + ')');
				
				$('#editor_div_tipe').val(avatar_detail.tipe);
				$('#editor_div_gender').val(avatar_detail.gender);
				$('#editor_div_name').val(avatar_detail.name);
				$('#editor_div_element').html(avatar_detail.element);
				$('#editor_div_material').html(avatar_detail.material);
				$('#editor_div_size').val(avatar_detail.size);
                                $("#lilo_id").val(avatar_detail.lilo_id);
                                $('#editor_edit_brand').val(avatar_detail.brand);
                                $('#editor_edit_payment').val(avatar_detail.payment);
                                $.get("<?php echo $this->basepath; ?>asset/admin/avatar_categorized/"+$("#editor_div_tipe").val(), {}, function(data){
                                    $("#editor_div_category").html(data);
                                    $('#editor_div_category').val(avatar_detail.category);
                                }); 				
				$('#editor_div_preview_image').html(avatar_detail.preview_image + "<br />" + "<img src='<?php echo $this->basepath; ?>bundles/preview_images/"+avatar_detail.preview_image+"' />");
				

				// color picker
				$('#editor_div_color').miniColors({disabled: false, readonly: false, value: avatar_detail.color});
//				alert(avatar_detail.color);
				$('#editor_div_color').val(avatar_detail.color);
				
				var rgb_color = avatar_detail.color;
				
				var r_ = hexToR(avatar_detail.color);
				var g_ = hexToG(avatar_detail.color);
				var b_ = hexToB(avatar_detail.color);
				
				rgb_color = 'rgb('+r_+', '+g_+', '+b_+')';
//				alert(rgb_color);
				
				$('.miniColors-trigger').css('background-color', rgb_color);
				
				$('#editor_div_color').trigger('change');

				
				var _td = avatar_detail.tipe;
				_td = _td.replace('face_part_', '');
				_td = _td.replace('_', ' ');
				_td = capitaliseFirstLetter(_td);

				
				$('#td_category').html(_td + ' Category');
				
//				$('#editor_div').append('<hr />' + data);
				
			});
			
			
			$('#editor_div').dialog('open');
		});
		
		$("#avatar_add_tipe").change(function(){
//			var tipe = $(this).val();
//			if(tipe == 'face_part_eye_brows' || tipe == 'face_part_eyes' || tipe == 'face_part_lip'){
//				$("#hide_for_face_part").hide();
//			} else {
//				$("#hide_for_face_part").show();
//			}
			
			var _th = $("#avatar_add_tipe").val();
			_th = _th.replace('face_part_', '');
			_th = _th.replace('_', ' ');
			_th = capitaliseFirstLetter(_th);
                        $.get("<?php echo $this->basepath; ?>asset/admin/avatar_categorized/"+$("#avatar_add_tipe").val(), {}, function(data){
                            $("#avatar_add_category").html(data)
                        }); 
			$('#category_th').html(_th + ' Category');
			
		});
		
		$('#editor_div_tipe').live('change', function(){
			
			var _th = $("#editor_div_tipe").val();
			_th = _th.replace('face_part_', '');
			_th = _th.replace('_', ' ');
			_th = capitaliseFirstLetter(_th);
			$.get("<?php echo $this->basepath; ?>asset/admin/avatar_categorized/"+$("#editor_div_tipe").val(), {}, function(data){
                            $("#editor_div_category").html(data)
                        }); 
			$('#td_category').html(_th + ' Category');
		});

	});
</script>

<input type="hidden" name="active_avatar_part" id="active_avatar_part" value="" />

<div style="width: auto; min-height: 58.4px; height: auto; min-width:600px; " class="ui-dialog-content ui-widget-content" id="editor_div" 
	title="Edit Avatar Element">
	<form enctype="multipart/form-data" id="avatar_update" method="post" action="<?php echo $this->basepath; ?>asset/admin/avatar_update">
  <input type="hidden" name="lilo_id" id="lilo_id" value="" />
	<table style="width:100%" class="input_form">
  	<tr>
    	<td style="width:100px;">Type</td>
      <td>
        <select name="editor_div_tipe" id="editor_div_tipe">
          <option value="face">Face</option>
          <option value="hat">Hat</option>
          <optgroup label="Face Part">
            <option value="face_part_eye_brows">Eye Brows</option>
            <option value="face_part_eyes">Eyes</option>
            <option value="face_part_lip">Lip</option>	
          </optgroup>
          <option value="hair">Hair</option>
          <option value="hand">Hand</option>
          <option value="pants">Pants</option>
          <option value="shoes">Shoes</option>
          <option value="body">Body</option>
        </select>
      </td>
      <td style="width:100px;" id="td_category">Lib Category</td>
      <td>
          <select name="editor_div_category" id="editor_div_category">
            <?php
                foreach($this->category as $result)
                {
                  echo "<option value='".$result['name']."'>".$result['name']."</option>";
                }
            ?>
          </select></td>
    </tr>
  	<tr>
    	<td>Gender</td>
      <td>
        <select name="editor_div_gender" id="editor_div_gender">
        	<option value="male">Male</option>
        	<option value="female">Female</option>
        	<option value="unisex">Unisex</option>
				</select>
      </td>
    	<td>Name</td>
      <td>
      	<input type="text" name="editor_div_name" id="editor_div_name" value="" style="width:90%" />
      </td>
    </tr>
    <tr>
    	<th style="width:100px;">Brand</th>
        <td>
          <select name="editor_div_brand" id="editor_edit_brand">
              <option value=''>&nbsp;</option>
            <?php
                foreach($this->brand as $result)
                {
                  echo "<option value='".$result['name']."'>".$result['name']."</option>";
                }
            ?>
          </select>
        </td>
        <th style="width:100px;">Payment</th>
        <td>
            <select name="editor_div_payment" id="editor_edit_payment">
                <option value="Default">Default</option>
              	<option value="Free">Free</option>
              	<option value="Paid">Paid</option>
                <option value="Unlock">Unlock</option>
            </select>
        </td>
      </tr>
  	<tr>
    	<td>Element</td>
      <td colspan="2" id="editor_div_element"></td>
      <td><input type="file" name="edit_element" id="edit_element" style="width:100%" /></td>
    </tr>
  	<tr>
    	<td>Material</td>
      <td colspan="2" id="editor_div_material"></td>
      <td><input type="file" name="edit_material" id="edit_material" style="width:100%" /></td>
    </tr>
  	<tr>
    	<td>Size</td>
      <td>
        <select name="editor_div_size" id="editor_div_size">
          <option value="">All Size</option>
          <option value="thin">Thin</option>
          <option value="medium">Medium</option>
          <option value="fat">Fat</option>
        </select>
      </td>
      <td>Color</td>
      <td><input type="text" size="7" name="editor_div_color" id="editor_div_color" value="" /></td>
    </tr>
  	<tr>
    	<td>Preview Image</td>
      <td colspan="2" id="editor_div_preview_image"></td>
      <td><input type="file" name="edit_preview_image" id="edit_preview_image" style="width:100%" /></td>
    </tr>
  </table>
	</form>
</div>


<form enctype="multipart/form-data" method="post" action="<?php echo $this->basepath; ?>asset/admin/avatar">
<input type="hidden" name="add_new_avatar" value="1" />
<div class="centered shadow transbg" style="width:960px;">

	<div style="float:left; width:960px; height:40px;">

  </div>

	<div style="float:left; width:960px; text-align:center" id="div_asset_chooser">
    <div id="tabs" style="float:left; width:952px; min-height:440px">
      <ul>
        <li><a href="#tabs-1">Head</a></li>
        <li><a href="#tabs-8">Hat</a></li>
        <li><a href="#tabs-2">Face Part</a></li>
        <li><a href="#tabs-3">Hair</a></li>
        <li><a href="#tabs-0">Hand</a></li>
        <li><a href="#tabs-4">Pants</a></li>
        <li><a href="#tabs-5">Shoes</a></li>
        <li><a href="#tabs-6">Body</a></li>
        <li><a href="#tabs-7">&bull;&nbsp;Add New Avatar Component&nbsp;&bull;</a></li>
      </ul>
      <div id="tabs-1">
        <!--[setiap opsi adalah gabungan element dan material]-->
        <!--[*face*]-->
        <!--[*face__male_face-2__male_face-2*]-->
        <?php
	$items_array = $this->avatar_array['face'];
        foreach($items_array as $item){
				?>
        <div 
          id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
          class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> preview_avatar" 
          title="<?php echo $item['id']; ?>"
          style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
          <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
        </div>
        <?php
				}
				?>
        
      </div>

      <div id="tabs-8">
        <!--[setiap opsi adalah gabungan element dan material]-->
        <!--[*face*]-->
        <!--[*face__male_face-2__male_face-2*]-->
        <?php
				$items_array = $this->avatar_array['hat'];
        foreach($items_array as $item){
				?>
        <div 
          id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
          class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> preview_avatar" 
          title="<?php echo $item['id']; ?>"
          style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
          <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
        </div>
        <?php
				}
				?>
        
      </div>

      <div id="tabs-2">
      	<!--[*********************************************************]-->
        <div id="face_part_tabs" style="float:left; width:100%;">
          <ul>
            <li><a href="#tabs-eye-brows">Eye Brows</a></li>
            <li><a href="#tabs-eyes">Eyes</a></li>
            <li><a href="#tabs-lip">Lip</a></li>
          </ul>
          <div id="tabs-eye-brows">
						<?php
            $items_array = $this->avatar_array['face_part_eye_brows'];
            foreach($items_array as $item){
            ?>
            <div 
              id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
              class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> preview_avatar" 
		          title="<?php echo $item['id']; ?>"
              style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
              <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
            </div>
            <?php
            }
            ?>

          </div>
          <div id="tabs-eyes">
						<?php
            $items_array = $this->avatar_array['face_part_eyes'];
            foreach($items_array as $item){
            ?>
            <div 
              id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
              class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> preview_avatar" 
		          title="<?php echo $item['id']; ?>"
              style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
              <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
            </div>
            <?php
            }
            ?>

          </div>
          <div id="tabs-lip">
						<?php
            $items_array = $this->avatar_array['face_part_lip'];
            foreach($items_array as $item){
            ?>
            <div 
              id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
              class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> preview_avatar" 
		          title="<?php echo $item['id']; ?>"
              style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
              <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
            </div>
            <?php
            }
            ?>

          </div>

        </div>
      </div>
      
      
      <div id="tabs-3">
        <?php
				$items_array = $this->avatar_array['hair'];
        foreach($items_array as $item){
				?>
        <div 
          id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
          class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> preview_avatar" 
          title="<?php echo $item['id']; ?>"
          style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
          <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
        </div>
        <?php
				}
				?>
      </div>
      
      <div id="tabs-0">
        <?php
				$items_array = (array)$this->avatar_array['hand'];
        foreach($items_array as $item){
				?>
        <div 
          id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
          class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> preview_avatar" 
          title="<?php echo $item['id']; ?>"
          style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
          <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
        </div>
        <?php
				}
				?>
      </div>
      
      
      
      <div id="tabs-4">
        <?php
				$items_array = (array)$this->avatar_array['pants'];
        foreach($items_array as $item){
				?>
        <div 
          id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
          class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> preview_avatar" 
          title="<?php echo $item['id']; ?>"
          style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
          <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
        </div>
        <?php
				}
				?>
      </div>
      <div id="tabs-5">
        <?php
				$items_array = $this->avatar_array['shoes'];
        foreach($items_array as $item){
				?>
        <div 
          id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
          class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> preview_avatar" 
          title="<?php echo $item['id']; ?>"
          style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
          <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
        </div>
        <?php
				}
				?>
      </div>
      <div id="tabs-6">
        <?php
				$items_array = $this->avatar_array['body'];
        foreach($items_array as $item){
				?>
        <div 
          id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
          class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> preview_avatar" 
          title="<?php echo $item['id']; ?>"
          style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
          <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
        </div>
        <?php
				}
				?>
      </div>
      <div id="tabs-7">
        <table class="input_form" style="width:100%;" align="center">
        	<tr>
          	<th style="width:120px;">Gender</td>
            <td colspan="3">
            	<input type="radio" name="gender" value="male" id="gender_male" /><label for="gender_male">Male</label>
              &nbsp;
            	<input type="radio" name="gender" value="female" id="gender_female" /><label for="gender_female">Female</label>
              &nbsp;
            	<input type="radio" name="gender" value="unisex" id="gender_unisex" /><label for="gender_unisex">Unisex</label>
            </td>
          </tr>
        	<tr>
          	<th>Type</th>
            <td>
            	<select name="avatar_add_tipe" id="avatar_add_tipe">
              	<option value="face">Face</option>
              	<option value="hat">Hat</option>
                <optgroup label="Face Part">
                  <option value="face_part_eye_brows">Eye Brows</option>
                  <option value="face_part_eyes">Eyes</option>
                  <option value="face_part_lip">Lip</option>	
                </optgroup>
              	<option value="hair">Hair</option>
              	<option value="hand">Hand</option>
              	<option value="pants">Pants</option>
              	<option value="shoes">Shoes</option>
              	<option value="body">Body</option>
              </select>
            </td>
            <th style="width:120px;" id="category_th">Category</th>
            <td>
            	<select name="avatar_add_category" id="avatar_add_category">
                    <?php
                        foreach($this->category as $result)
                        {
                          echo "<option value='".$result['name']."'>".$result['name']."</option>";
                        }
                    ?>
                  </select>
            </td>
          </tr>
          <tr>
    	<th style="width:100px;">Brand</th>
        <td>
          <select name="editor_div_brand" id="editor_div_brand">
              <option value=''>&nbsp;</option>
            <?php
                foreach($this->brand as $result)
                {
                  echo "<option value='".$result['name']."'>".$result['name']."</option>";
                }
            ?>
          </select>
        </td>
        <th style="width:100px;">Payment</th>
        <td>
            <select name="editor_div_payment" id="editor_div_payment">
                <option value="Default">Default</option>
              	<option value="Free">Free</option>
              	<option value="Paid">Paid</option>
                <option value="Unlock">Unlock</option>
            </select>
        </td>
      </tr>
          <tr>
          	<th>Size</th>
            <td>
            	<select name="avatar_add_size" id="avatar_add_size">
              	<option value="">All Size</option>
              	<option value="thin">Thin</option>
              	<option value="medium">Medium</option>
              	<option value="fat">Fat</option>
              </select>
            </td>
          	<th>Name</th>
            <td>
              <input type="text" name="avatar_add_name" id="avatar_add_name" value="" style="width:90%" />
            </td>
          </tr>
          <tr><!--[* id="hide_for_face_part"*]-->
          	<th>Element</th>
            <td>
              <input type="file" name="avatar_add_element" id="avatar_add_element" />
            </td>
          	<th>Material</th>
            <td>
              <input type="file" name="avatar_add_material" id="avatar_add_material" />
            </td>
          </tr>
          <tr>
          	<th>Preview Image</th>
            <td>
              <input type="file" name="avatar_add_preview_image" id="avatar_add_preview_image" />
            </td>
          	<th>Color</th>
            <td><input type="text" size="7" name="avatar_add_color" id="avatar_add_color" value="" /></td>
          </tr>
          <tr>
            <td colspan="4" style="text-align:center;">
            	<input type="submit" value="Save" />
            </td>
          </tr>
        </table>
      </div>
    </div>

  </div>

</div>
</form>

<!--[
<div>
<pre>
<?php
	print_r($this->avatar_array);
?>
</pre>
</div>
]-->