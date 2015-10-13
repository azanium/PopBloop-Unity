<style>
  .welcometo{
    height: 82px;
    background: url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/welcome.png) center no-repeat;
  }
  
  .popbloopbiglogo{
    height: 284px;
    background: url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/popbloopbiglogo.png) center no-repeat;
  }
  
  .popbloopworld{
    height: 545px;
    margin-top: 4px;
    background: url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/popbloop_world.png) center no-repeat;
  }
  
  .popbloowhatispmenu{
    height: 125px;
    margin-bottom: 4px;
    cursor: pointer;
  }

  .menu_blooper{
    float: left; height: 125px; width: 313px; background-repeat: no-repeat;
    background-image: url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/Bloopes_button_normal.png); background-position: center;
  }  

  .menu_influencer{
    float: left; height: 125px; width: 314px; background-repeat: no-repeat;
    background-image: url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/influencer_button_normal.png); background-position: center;
  }
  
  .menu_brand{
    float: left; height: 125px; width: 313px; background-repeat: no-repeat;
    background-image: url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/brand_button_normal.png); background-position: center;
  }
  
  .image_menu_blooper, .image_menu_influencer, .image_menu_brand{
    height: 735px;
    background-color: #f3dfc4;
    margin-top: 4px;
    background-position: center;
    background-repeat: no-repeat;
  }

  .image_menu_blooper{
    background-image: url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/menu_blooper.jpg);
  }
  
  .image_menu_influencer{
    background-image: url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/menu_influencer.jpg);
  }
  
  .image_menu_brand{
    background-image: url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/menu_brand.jpg);
  }
  
  .backtotop{
    width: 67px; height: 67px;
    position: absolute;
    bottom: 5px; right: 5px;
    cursor: pointer;
  }

  .backtotop_normal{
    background-image: url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/back_to_top_button_normal.png);
  }
  
  .backtotop_hover{
    background-image: url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/back_to_top_button_hover.png);
  }
  
  .backtotop_click{
    background-image: url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/back_to_top_button_click.png);
  }

</style>

<script type="text/javascript">
  $('.menu_blooper').live({
    mousedown: function(){
      $(this).css('background-image', 'url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/Bloopes_button_click.png)');
    },
    mouseup: function(){
      $(this).css('background-image', 'url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/Bloopes_button_hover.png)');
    },
    mouseenter: function(){
      $(this).css('background-image', 'url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/Bloopes_button_hover.png)');
    },
    mouseleave: function(){
      $(this).css('background-image', 'url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/Bloopes_button_normal.png)');
    },
    
    click: function(){
      $('#image_1').removeClass('image_menu_influencer');
      $('#image_1').removeClass('image_menu_brand');
      
      $('#image_1').addClass('image_menu_blooper');
      
      
      $('#image_2').removeClass('image_menu_blooper');
      $('#image_2').removeClass('image_menu_brand');
      
      $('#image_2').addClass('image_menu_influencer');


      $('#image_3').removeClass('image_menu_influencer');
      $('#image_3').removeClass('image_menu_blooper');
      
      $('#image_3').addClass('image_menu_brand');
    }

  });
  
  $('.menu_influencer').live({
    mousedown: function(){
      $(this).css('background-image', 'url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/influencer_button_click.png)');
    },
    mouseup: function(){
      $(this).css('background-image', 'url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/influencer_button_hover.png)');
    },
    mouseenter: function(){
      $(this).css('background-image', 'url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/influencer_button_hover.png)');
    },
    mouseleave: function(){
      $(this).css('background-image', 'url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/influencer_button_normal.png)');
    },

    click: function(){
      $('#image_1').removeClass('image_menu_blooper');
      $('#image_1').removeClass('image_menu_brand');
      
      $('#image_1').addClass('image_menu_influencer');
      
      $('#image_2').removeClass('image_menu_influencer');
      $('#image_2').removeClass('image_menu_brand');
      
      $('#image_2').addClass('image_menu_blooper');


      $('#image_3').removeClass('image_menu_influencer');
      $('#image_3').removeClass('image_menu_blooper');
      
      $('#image_3').addClass('image_menu_brand');

    }

  });
  
  $('.menu_brand').live({
    mousedown: function(){
      $(this).css('background-image', 'url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/brand_button_click.png)');
    },
    mouseup: function(){
      $(this).css('background-image', 'url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/brand_button_hover.png)');
    },
    mouseenter: function(){
      $(this).css('background-image', 'url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/brand_button_hover.png)');
    },
    mouseleave: function(){
      $(this).css('background-image', 'url(<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/brand_button_normal.png)');
    },

    click: function(){
      $('#image_1').removeClass('image_menu_blooper');
      $('#image_1').removeClass('image_menu_influencer');
      
      $('#image_1').addClass('image_menu_brand');
      
      
      $('#image_2').removeClass('image_menu_blooper');
      $('#image_2').removeClass('image_menu_brand');
      
      $('#image_2').addClass('image_menu_influencer');


      $('#image_3').removeClass('image_menu_influencer');
      $('#image_3').removeClass('image_menu_brand');
      
      $('#image_3').addClass('image_menu_blooper');

      
    }

  });

  $('.backtotop').live({
    mousedown: function(){
      $(this).removeClass('backtotop_normal');
      $(this).removeClass('backtotop_hover');
      
      $(this).addClass('backtotop_click');
    },
    mouseup: function(){
      $(this).removeClass('backtotop_normal');
      $(this).removeClass('backtotop_click');
      
      $(this).addClass('backtotop_hover');
    },
    mouseenter: function(){
      $(this).removeClass('backtotop_normal');
      $(this).removeClass('backtotop_click');
      
      $(this).addClass('backtotop_hover');
    },
    mouseleave: function(){
      $(this).removeClass('backtotop_hover');
      $(this).removeClass('backtotop_click');
      
      $(this).addClass('backtotop_normal');
    }
  });  
</script>


<div class="container_12" style="display: none;">
  <div class="grid_12 welcometo"></div>
  <div class="clear"></div>
</div>

<div class="container_12 pop_20_spacer">
</div>

<div class="container_12">
  <div class="grid_12">
    <img style="width: 940px;" src="<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/popbloopbiglogo.jpg" />
  </div><!--[ popbloopbiglogo]-->
</div>


<div class="container_12">
  <div class="grid_12" style="height: 500px; margin-top: 4px;" id="whatis_top">
    <img style="width: 940px;" src="<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/popbloop_world.jpg" />
  </div>
  <!--[popbloopworld]-->
</div>


<div class="container_12">
  <div class="grid_12 popbloowhatispmenu" id="image_x">
    <a href="#image_x"><div class="menu_blooper"></div></a>
    <a href="#image_x"><div class="menu_influencer"></div></a>
    <a href="#image_x"><div class="menu_brand"></div></a>
  </div>
  <div class="clear"></div>
</div>


<div class="container_12">
  <div class="grid_12 image_menu_blooper" id="image_1" style="position: relative;">
    <a href="#whatis_top"><div class="backtotop backtotop_normal">&nbsp;</div></a>
  </div>
</div>

<div class="container_12">
  <div class="grid_12 image_menu_influencer" id="image_2" style="position: relative;">
    <a href="#whatis_top"><div class="backtotop backtotop_normal">&nbsp;</div></a>
  </div>
</div>

<div class="container_12">
  <div class="grid_12 image_menu_brand" id="image_3" style="position: relative;">
    <a href="#whatis_top"><div class="backtotop backtotop_normal">&nbsp;</div></a>
  </div>
</div>

<div class="image_cache" style="display: none;">
  <!--[* load all image *]-->
  <img src="<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/Bloopes_button_hover.png" />
  <img src="<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/Bloopes_button_click.png" />
  
  <img src="<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/influencer_button_hover.png" />
  <img src="<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/influencer_button_click.png" />
  
  <img src="<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/brand_button_hover.png" />
  <img src="<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/brand_button_click.png" />
  
  <img src="<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/back_to_top_button_hover.png" />
  <img src="<?php echo $this->basepath; ?>modules/011_articles/images_popbloopdark/whatis/back_to_top_button_click.png" />
</div>