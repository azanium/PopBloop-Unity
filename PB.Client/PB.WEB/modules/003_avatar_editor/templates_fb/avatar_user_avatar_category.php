<script type="text/javascript">
$(document).ready(function(){
	$('#avatarcategoryback_<?php echo $this->tipe; ?>').live('click', function(){
//		alert('yoi..<?php echo $this->tipe; ?>');
		$('#loading_div').show();
		$('#<?php echo $this->tipe; ?>_items').hide();
		$('#<?php echo $this->tipe; ?>_categories').show();
		$('#loading_div').hide();
	});
});
</script>

<?php
if($this->tipe != 'face_part_eye_brows' && $this->tipe != 'face_part_eyes' && $this->tipe != 'face_part_lip'){
?>


  <!--div title="Click to back to category list" class="avatar_category_back" id="avatarcategoryback_<?php echo $this->tipe; ?>"
    style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:center; background-repeat:no-repeat; background-image:<?php if($this->background_image != ''){ echo $this->background_image; } else {?> url('<?php echo $this->basepath . 'modules/000_user_interface/images/avatar_editor/button_back.png'; ?>')<?php } ?>"><?php  ?>
  </div-->

	<?php
	foreach($this->avatar_array as $item){
	?>
			<div 
				id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); //echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']) . '__XSPACEX__' .  str_ireplace('.unity3d', '', $item['element2']) . '__' . str_ireplace('.unity3d', '', $item['material2']); ?>"
				class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> avatarChange" 
				style="width:100px; height:100px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
				<div style="position:relative; top:85px;"><?php echo $item['name']; ?></div>
			</div>
	<?php
	}
	?>

<?php
} else {
?>

				<?php
        foreach($this->avatar_array as $item){
					$item['tipe__'] = $item['tipe'];
					if($item['tipe__'] == 'face_part_eye_brows' || $item['tipe__'] == 'eye_brows'){
						$item['tipe__'] = 'eyeBrows';
					}

        ?>
          <div title="<?php echo $item['name']; ?>"
            id="<?php echo $item['tipe__'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
            class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> facePartChange" 
            style="width:50px; height:50px; margin:3px; float:left; text-align:center; cursor:pointer; background-color:<?php if($item['color'] != ''){ echo $item['color']; } else { echo '#000'; } ?>"><!--[  background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>') ]-->
            <!--[*
            <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
            *]-->
          </div>
        <?php
          }
        ?>


<?php
}
?>


