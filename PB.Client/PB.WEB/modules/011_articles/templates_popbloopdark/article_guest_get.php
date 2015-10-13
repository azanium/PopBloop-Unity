<style type="text/css">

h1{
  color: #fff;
  font-size: 36px;
  height: 40px;
  text-align: left;
  text-decoration: none;
  font-weight: normal;
}

h2{
  color: #eb2f8a;
  font-size: 17px;
  text-align: left;
  text-decoration: none;
  font-weight: normal;
  
  margin-left: 10px;
}

p, h3{
  color: #666;
  font-size: 13px;
  text-align: left;
  text-decoration: none;
  font-weight: normal;
  
  margin-left: 10px;
  margin-bottom: 10px;
}

table{
  margin-left: 10px;
}

th{
  font-size:12px; font-weight:bold; margin:6px;
}
td, th{
  border:#999 thin solid; margin:4px;
}


.left_menu_container{
  height: 40px;
  display: table;
  cursor: pointer;
  width: 100%;
}

.left_menu_container:hover, .left_menu_content:hover, .left_menu_content_current:hover{
  color: #fff;
}

.left_menu_content{
  color: #999;
  font-size: 16px;
  height: 40px;
  display: table-cell;
  vertical-align: middle;
  text-align: left;
}

.left_menu_content_current{
  color: #333;
  font-size: 16px;
  height: 40px;
  display: table-cell;
  vertical-align: middle;
  text-align: left;
}



</style>

<div class="container_12 pop_20_spacer"></div>

<div class="container_12">
  <div class="grid_12">
    <div><h1><?php echo $this->title; ?></h1></div>
  </div>
  <div class="clear"></div>
</div>

<div class="container_12 pop_20_spacer">&nbsp;</div>
<div class="clear"></div>
<div class="container_12 pop_20_spacer">&nbsp;</div>
<div class="clear"></div>


<div class="container_12">
  <div class="grid_2">
    <div style="min-height:250px; border-right: #333 solid 1px;">
      <a href="<?php echo $this->basepath; ?>faq"><div class="left_menu_container"><div class="left_menu_content<?php if($this->alias == 'faq'){ ?>_current<?php } ?>">FAQ</div></div></a>
      <a href="<?php echo $this->basepath; ?>howto"><div class="left_menu_container"><div class="left_menu_content<?php if($this->alias == 'howto'){ ?>_current<?php } ?>">How To</div></div></a>
      <a href="<?php echo $this->basepath; ?>troubleshooting"><div class="left_menu_container"><div class="left_menu_content<?php if($this->alias == 'troubleshooting'){ ?>_current<?php } ?>">Troubleshooting</div></div></a>
      <div class="left_menu_container"><div class="left_menu_content">Feedback</div></div>
    </div>
  </div>
  <div class="grid_10">
  <?php  echo $this->text; ?>
  <?php /* ?>
    <h2>What is Popbloop?</h2>
    <h3>Popbloop is a 3D massively multiplayer online virtual world</h3>
  <?php */ ?>
  </div>
</div>