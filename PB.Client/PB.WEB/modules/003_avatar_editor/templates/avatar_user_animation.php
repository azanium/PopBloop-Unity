<?php
//print_r($this->anim_conf);
//print("<hr />");
//print_r($this->anim_array);
//print("<hr />");
?>

<form method="post" action="<?php echo $this->basepath; ?>avatar/user/set_animation">
<input type="hidden" name="redirect_url" value="avatar/user/animation" />
<div style="width:960px; background-color:#FFF;" class="centered shadow transparent_70">

<table class="lilo_form">

<?php
$i = 1;
foreach($this->anim_array as $aa){
?>
	<tr>
  	<td>
			<input type="checkbox" name="animation_conf[]" id="animation_conf_<?php echo $i;?>" value="<?php echo $aa; ?>" <?php if(in_array($aa, (array)$this->anim_conf)){ ?> checked="checked" <?php } ?> />
  	</td>
  	<td>
      <label for="animation_conf_<?php echo $i; ?>"><?php echo $aa; ?></label>
  	</td>
	</tr>
<?php
	$i++;
}
?>
</table>
<input type="submit" value="Save" />



</div>
</form>






<div class="centered transparent_70" style="width:960px; height:540px; border:none;">
	<div style="float:left; width:960px; height:80px;">

  </div>

  <div id="tabs" style="float:left; width:960px;">
    <ul>
      <li><a href="#tabs-1">Your Emo!</a></li>
      <li><a href="#tabs-2">Get New Emo!</a></li>
    </ul>
    <div id="tabs-1">
			<div style="width:100%; text-align:left">
        <div style="width:100%; text-align:left" id="animation_list">
  
        </div>
      </div>
    </div>
    <div id="tabs-2">


    </div>
	</div>
  
</div>
