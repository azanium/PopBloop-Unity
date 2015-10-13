<div class="withjs">
<?php /*
<script src="<?php echo $this->basepath; ?>libraries/js/jquery.ui.popbloop.dark/development-bundle/ui/jquery.ui.core.js"></script>
<script src="<?php echo $this->basepath; ?>libraries/js/jquery.ui.popbloop.dark/development-bundle/ui/jquery.ui.widget.js"></script>
<script src="<?php echo $this->basepath; ?>libraries/js/jquery.ui.popbloop.dark/development-bundle/ui/jquery.ui.mouse.js"></script>
<script src="<?php echo $this->basepath; ?>libraries/js/jquery.ui.popbloop.dark/development-bundle/ui/jquery.ui.draggable.js"></script>
<script src="<?php echo $this->basepath; ?>libraries/js/jquery.ui.popbloop.dark/development-bundle/ui/jquery.ui.droppable.js"></script>
*/ ?>

<script type="text/javascript" src="<?php print($this->basepath); ?>libraries/js/simplemodal-demo-basic-1.4.2/js/jquery.simplemodal.js"></script>

<!-- Page styles 
<link type='text/css' href='<?php print($this->basepath); ?>libraries/js/simplemodal-demo-basic-1.4.2/css/demo.css' rel='stylesheet' media='screen' />
-->
<!-- Contact Form CSS files -->
<link type='text/css' href='<?php print($this->basepath); ?>libraries/js/simplemodal-demo-basic-1.4.2/css/basic.css' rel='stylesheet' media='screen' />

<!-- IE6 "fix" for the close png image -->
<!--[if lt IE 7]>
<link type='text/css' href='<?php print($this->basepath); ?>libraries/js/simplemodal-demo-basic-1.4.2/css/basic_ie.css' rel='stylesheet' media='screen' />
<![endif]-->


<script type="text/javascript" src="<?php echo $this->basepath; ?>libraries/js/jquery_drag_drop_devongovett/jquery.draggable.js"></script>

<style>

.circle{
  width: 94px;
  height: 88px;
  float: left;
  margin-right: 5px;
  margin-bottom: 5px;
/*  background-color: #00f;*/
}

.circle:hover{  /* doesn't work */
  background-color: #fff;
}

.add_new_circle{
  width: 90px;
  height: 84px;
  float: left;
  margin-right: 5px;
  margin-bottom: 5px;
  background-color: transparent;
  border: 2px solid #333333;
  font-size: 9px;
  cursor: pointer;
}

.add_new_circle_plus{
  width: 90px;
  height: 48px;
  background: url(<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/icons/people.plus.png) center no-repeat;
}

.circle_count{
  width: 90px;
  height: 45px;
  text-align: right;
  color: #fff;
  font-size: 28px;
  font-weight: bold;
  padding-right: 4px;
}

.circle_name{
  width: 94px;
  min-height: 30px;
  max-height: 30px;
  text-align: center;
  color: #fff;
  font-size: 11px;
  overflow: hidden;
}


.circle_edit_delete{
  width: 94px;
  height: 11px;
  text-align: center;
  color: #fff;
  font-size: 10px;
  
  display: none;
}

.circle_edit, .circle_delete{
  color: #fff;
  text-decoration: none;
}


.friend{
  width: 75px;
  height: 74px;
  float: left;
  padding-right: 5px;
  padding-bottom: 5px;
  cursor: pointer;
}

.friend_icon{
  width: 75px;
  height: 54px;
}
  
.friend_name{
  width: 75px;
  height: 20px;
  color: #fff;
  background-color: #000;
  font-size: 9px;
  overflow: hidden;
  text-align: center;
  display: table;
}

.table_cell_middle{
  display: table-cell;
  vertical-align: middle;
}


		.draghovered {
			background: #343434;
		}

    .droptarget {
/*        background: none repeat scroll 0 0 gray;
        color: white;
        height: 100px;
        margin-left: 300px;
        padding: 5px;
        width: 100px;*/
    }


  /* Container */
  /*#simplemodal-container {height:360px; width:600px; color:#bbb; background-color:#333; border:4px solid #444; padding:12px;}*/
  #simplemodal-container {height:62px; width:300px; color:#bbb; background-color:#333; border:1px solid #444; padding:0px;}
  /*#simplemodal-container .simplemodal-data {padding:8px;}*/
  #simplemodal-container .simplemodal-data {padding:0px 0 0 0;}
  #simplemodal-container code {background:#141414; border-left:3px solid #65B43D; color:#bbb; display:block; font-size:12px; margin-bottom:12px; padding:4px 6px 6px;}
  #simplemodal-container a {color:#ddd;}
  #simplemodal-container a.modalCloseImg {background:url(../img/basic/x.png) no-repeat; width:25px; height:29px; display:inline; z-index:3200; position:absolute; top:-15px; right:-16px; cursor:pointer;}
  #simplemodal-container h3 {color:#84b8d9;}


</style>

<script type="text/javascript">
  function loadCircle(){
    $.post("<?php echo $this->basepath; ?>friend/user/ws_circlecount", {}, function(data){
      var circle = eval('(' + data + ')');
      var circle_length = circle.length;
      //alert(circle[0]);
      //alert(circle[1]);
      //alert(circle_length);
      
      // html_ defaultnya berisi [+] add new box
      var html_ = '<div class="add_new_circle">';
      html_ = html_ + '<div class="add_new_circle_plus">&nbsp;</div>';
      html_ = html_ + '<div class="circle_name">Add New<br/ >Box</div>';
      html_ = html_ + '</div>';
      
      for(var i = 0; i < circle_length/3; i++){
        
        //$.post("<?php echo $this->basepath; ?>friend/user/circlemembercount", {'circle_name':circle[i]}, function(data_){
        //  alert(data_);
        //});
        var i_ = i + (circle_length/3);
        var i__ = i + (circle_length*2/3);
        var circle_name_nospace = circle[i].replace(/\s/g, '_');
        html_ = html_ + '<div class="circle droptarget" id="circlecontainer__'+circle_name_nospace+'" style="background-color:' + circle[i__] + '" title="' + circle[i] + '">'; // 
          html_ = html_ + '<div class="circle_count" id="circle__'+circle_name_nospace+'">' + circle[i_] + '</div>';
          html_ = html_ + '<div class="circle_name">' + circle[i] + '</div>';
          html_ = html_ + '<div class="circle_edit_delete" id="circleeditdelete__'+circle_name_nospace+'" style="display:none;">';
            html_ = html_ + '<div style="width:50%; text-align:left; float:left">&nbsp;<a class="circle_edit" oldname="' + circle[i] + '" warna="' + circle[i__] + '" id="circleedit__'+circle_name_nospace+'">Edit</a></div>';
            html_ = html_ + '<div style="width:50%; text-align:right; float:left"><a class="circle_delete" circletodelete="'+circle_name_nospace+'" id="circledelete__'+circle_name_nospace+'">Delete</a>&nbsp;</div>';
          html_ = html_ + '</div>';
          
        html_ = html_ + '</div>';
      }
      $('#circle_container').html(html_);
      $('.droptarget').droppable({
        drop: function(data){
          var title_ = $(this).attr('title');
          //alert('Title: ' + title_);
//          console.log(data);
//          alert(data.position.x);//OK
          //alert(data.draggable[0].id);
          var _id = data.draggable[0].id;
          var _id_split = _id.split('_');
          $(".loading_div").show();
          $.post("<?php echo $this->basepath; ?>friend/user/addtocircle", {'friend_id':_id_split[1], 'circle_name':title_}, function(data){alert('added');
            var circle_name_nospace_ = title_.replace(/\s/g, '_');
            $('#circle__' + circle_name_nospace_).html(data);
            //alert('#circle__' + circle_name_nospace_);
            //alert(data);
            //loadFriendList();
            $(".loading_div").hide();
          });
        }
      });
    });
  }
  loadCircle();
  function loadPeople(){
    $.post("<?php echo $this->basepath; ?>friend/user/ws_search", {}, function(data){
      //alert(data);
      var circle = eval('(' + data + ')');
      //alert(circle[0].email);
      //alert(circle.length);
      var circle_length = circle.length;
      var html_ = '';
      for(var i = 0; i < circle_length; i++){
        html_ = html_ + '<div class="friend draggable" id="userid_'+ circle[i].lilo_id +'">';
          html_ = html_ + '<div class="friend_icon" style="background:url(<?php echo $this->basepath; ?>user_generated_data/profile_picture/'+circle[i].profile_picture+') center no-repeat">';
//          html_ = html_ + circle[i].profile_picture;
          html_ = html_ + '</div>';
          html_ = html_ + '<div class="friend_name">';
          html_ = html_ + "<span class='table_cell_middle'>" + circle[i].fullname + "</span>";
          html_ = html_ + '</div>';
        html_ = html_ + '</div>';
      }
      $('#people_container').html(html_);
      $('.draggable').draggable();
    });
  }
  loadPeople();
  
  

  
  $(document).ready(function(){
    //alert('sdfdsf');
    //$('.draggable').draggable();
    //$('.droppable').droppable();
    //$('.droptarget').droppable({
    //  drop: function(){
    //    alert('sdfdsf');
    //  }
    //});
    
    //$('.draggable').liveDraggable();
    // $('.draggable').draggable();
    //$('.draggable').live('mouseover', function(){
    //  $('.draggable').draggable();
    //  //$('.droptarget').droppable();
    //});
    //$('.droptarget').live('mouseover', function(){
    //  $('.droptarget').droppable({
    //    drop: function(){
    //      alert('Daym');
    //      return;
    //    }
    //  });
    //});
    //$('.droptarget').live('mouseover', function(){
    //  $(this).droppable({
    //    drop: function(){
    //      alert('dropped');
    //    }
    //  });
    //});
    
    $('.add_new_circle').live('click', function(){
      // reset value add_new_box_name = '' dan add_new_box_color = 'rgb(126, 126, 126)'
      $('#add_new_box_name').val('');
      $('#add_new_box_color').val('rgb(126, 126, 126)');
      
      $('#add_new_box_div').modal({closeClass:'close_reply_modal_dialog', close: false, escClose: true});
    });
    
    // jquery live hover
    $('.circle').live({
      mouseenter:
        function(){
          var _id = $(this).attr('id');
//          alert(_id);
          var _id_split = _id.split('__');
//          alert(_id_split[1]);
          $('#circleeditdelete__' + _id_split[1]).show();
        },
      mouseleave:
        function(){
          var _id = $(this).attr('id');
          var _id_split = _id.split('__');
//          alert(_id_split[1]);
          $('#circleeditdelete__' + _id_split[1]).hide();
        }
    });
    
    
    $('.circle_delete').live('click', function(){
      var _id = $(this).attr('id');
//          alert(_id);
      var _id_split = _id.split('__');
      var circle_name = _id_split[1];
      var circle_name_spaced = circle_name.replace(/_/g, ' ');
      
      $('#circle_to_delete').val(circle_name_spaced);
      
      $('#delete_confirm').modal({closeClass:'close_delete_confirm', close: false, escClose: true});
      
      return;
      
      if(confirm('Are you sure to delete "' + circle_name_spaced + '" box?')){
        $('loading_div').show();
        $.post('<?php echo $this->basepath; ?>friend/user/ws_circle/delete/' + circle_name_spaced, {}, function(data){
          // alert(data);
          loadCircle();
          $('loading_div').hide();
        });
      }
    });
    
    $('#delete_confirmed').live('click', function(){
        $('loading_div').show();
        var circle_name_spaced = $('#circle_to_delete').val();
        $.post('<?php echo $this->basepath; ?>friend/user/ws_circle/delete/' + circle_name_spaced, {}, function(data){
          // alert(data);
          loadCircle();
          $.modal.close();
          $('loading_div').hide();
        });
    });
    
    $('.circle_edit').live('click', function(){
      // alert('edit');
      var warna = $(this).attr('warna');
      var oldname = $(this).attr('oldname');
      // alert(warna);
      // alert(oldname);
      $('#edit_box_color').val(warna);
      $('#edit_box_name').val(oldname);
      $('#oldname').val(oldname);
      $('.edit_color_chooser').css('border', '1px solid #333');
      $('.edit_color_chooser').each(function(){
        if($(this).css('background-color') == warna){
          $(this).css('border', '1px solid #fff');
        }
      });
      $('#edit_box_div').modal({closeClass:'close_edit_modal_dialog', close: false, escClose: true});

    });
    
  });
  
</script>


<!--[new]-->
<div class="container_12 pop_20_spacer">
</div>

<div class="container_12">
  <div class="grid_8">
    <div style="height: 30px; border-bottom: solid 2px #333;">
      <div style="float: right; width: 90px; padding: 8px; color: #fff; font-size: 12px; background-color: #333; text-align: center; position: relative; bottom: 0; border: 0; margin-left: 2px;"><a href="<?php echo $this->basepath; ?>myavatar" style="text-decoration: none; color: #FFF;">Avatar Editor</a></div>
      <div style="float: right; width: 90px; padding: 8px; color: #fff; font-size: 12px; background-color: #333; text-align: center; position: relative; bottom: 0; border: 0;"><a href="<?php echo $this->basepath; ?>profile/<?php echo $_SESSION['username']; ?>" style="text-decoration: none; color: #FFF;">Profile</a></div>
      <div style="float: right; font-size: 14px; color: #fff; width: 215px; padding-top:8px;">
        <a href="<?php echo $this->basepath; ?>friend/user/facebook_invite" style="text-decoration:none;">Invite your <span style="color:#23487E; font-weight:bold;">Facebook</span> Friends</a>
      </div>
      <div style="float: right; font-size: 28px; color: #fff; width: 190px;">People</div>
    </div>
	</div>
  <div class="grid_4">
    <div style="height: 30px; display: table; text-align: center; width: 100%;">
      <div style="display: table-cell; vertical-align: middle; text-align: center; width: 100%;"><img src="<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/icons/people.drag.png" />&nbsp;&nbsp;Drag people to your box to follow and share</div>
    </div>
	</div>
  <div class="clear"></div>
  
  <div class="grid_12 pop_20_spacer"></div>
  <div class="clear"></div>
  
  <div class="grid_8">
    <div id="people_container"></div>
	</div>
  <div class="grid_4">
    <div id="circle_container"></div>
	</div>
  <div class="clear"></div>
  
<!--
  <div class="grid_12">
    <div class="draggable" style="width: 100px; height: 100px; background-color: #f00;">Drag me</div>
    <div class="droptarget">Drop here</div>
    
  </div>
-->  
</div>

<div class="container_12 pop_20_spacer">
</div>



<style>
  .color_chooser, .edit_color_chooser{
    width: 11px; height: 10px; float:left; border: 1px solid #333; cursor: pointer;
  }
  
</style>

<script type="text/javascript">
  //function rgbToHex(R,G,B) {return toHex(R)+toHex(G)+toHex(B)}
  //function toHex(n) {
  //  n = parseInt(n,10);
  //  if (isNaN(n)) return "00";
  //  n = Math.max(0,Math.min(n,255));
  //  return "0123456789ABCDEF".charAt((n-n%16)/16)
  //      + "0123456789ABCDEF".charAt(n%16);
  //}

  $(document).ready(function(){
    $('.color_chooser').live('click', function(){
      $('.color_chooser').css('border', '1px solid #333');
      $(this).css('border', '1px solid #fff');
      
      var cur_bg_color = $(this).css('background-color');
      //alert(cur_bg_color);
      $('#add_new_box_color').val(cur_bg_color);
      
    });
    
    $('.edit_color_chooser').live('click', function(){
      $('.edit_color_chooser').css('border', '1px solid #333');
      $(this).css('border', '1px solid #fff');
      
      var cur_bg_color = $(this).css('background-color');
      //alert(cur_bg_color);
      $('#edit_box_color').val(cur_bg_color);
      
    });
    
    
    $('#add_new_box_save').live('click', function(){
      var add_new_box_name = $('#add_new_box_name').val();
      var add_new_box_color = $('#add_new_box_color').val();
      
      if($.trim(add_new_box_name) == ''){
        alert("Box name cannot be empty.");
        return false;
      }
      
      if($.trim(add_new_box_color) == ''){
        alert("Choose color for the box.");
        return false;
      }
      
      // alert("Name: " + add_new_box_name + ", Color: " + add_new_box_color);
      
      // jika berhasil disimpan: reset value add_new_box_name = '' dan add_new_box_color = 'rgb(126, 126, 126)' kemudian tutup modal dan reload circle
      $.post("<?php echo $this->basepath; ?>friend/user/ws_circle/create/" + add_new_box_name + "/" + add_new_box_color, {}, function(data){
        // alert(data);
        loadCircle();
        $.modal.close();
      });
    });
    
    
    $('#edit_box_save').live('click', function(){
      var edit_box_name = $('#edit_box_name').val();
      var edit_box_color = $('#edit_box_color').val();
      var oldname = $('#oldname').val();
      // alert("oldname: " + oldname + ", newname: " + edit_box_name + ", color: " + edit_box_color);
      
      $.post("<?php echo $this->basepath; ?>friend/user/ws_circle/update/"+oldname+"/"+edit_box_name+"/"+edit_box_color+"", {}, function(data){
        // alert(data);
        loadCircle();
        $.modal.close();
      });
      
    });
    
  });
  
</script>

<div id="add_new_box_div" style="display: none; width: 300px;">
  
  <div style="width: 278px; height: 50px; float: left; padding: 4px 0 0 6px; text-align: center">
    <div style="float: left; width: 210px; height: 33px; background-color: #fff;">
      <input type="text" name="add_new_box_name" id="add_new_box_name" value="" style="width: 210px; height: 31px; border: 0; background-color: #fff; text-align: center; font-family: 'Droid Sans',sans-serif;" />
      <input type="hidden" name="add_new_box_color" id="add_new_box_color" value="rgb(126, 126, 126)" />
    </div>
    <div id="add_new_box_save" style="float: left; cursor: pointer; width: 66px; height: 33px; background: url(<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/icons/add_new_box_save.png) center no-repeat #fff; ">&nbsp;</div>
    <div class="clear"></div>
    <div style="width: 100%; height: 20px; padding-top: 4px;">
      <div class="color_chooser" style="background-color: #7e7e7e; border: 1px solid #fff;">&nbsp;</div>
      <div class="color_chooser" style="background-color: #4a4848;">&nbsp;</div>
      <div class="color_chooser" style="background-color: #1f1f1f;">&nbsp;</div>
      <div class="color_chooser" style="background-color: #a9b802;">&nbsp;</div>
      <div class="color_chooser" style="background-color: #79b004;">&nbsp;</div>
      
      <div class="color_chooser" style="background-color: #5b8404;">&nbsp;</div>
      <div class="color_chooser" style="background-color: #0294b8;">&nbsp;</div>
      <div class="color_chooser" style="background-color: #064295;">&nbsp;</div>
      <div class="color_chooser" style="background-color: #900bce;">&nbsp;</div>
      <div class="color_chooser" style="background-color: #7a07b0;">&nbsp;</div>
      
      <div class="color_chooser" style="background-color: #520675;">&nbsp;</div>
      <div class="color_chooser" style="background-color: #b80264;">&nbsp;</div>
      <div class="color_chooser" style="background-color: #c20a0a;">&nbsp;</div>
      <div class="color_chooser" style="background-color: #f67106;">&nbsp;</div>
      <div class="color_chooser" style="background-color: #f6af06;">&nbsp;</div>
      
      <div class="color_chooser" style="background-color: #0033ff;">&nbsp;</div>
      <div class="color_chooser" style="background-color: #6633cc;">&nbsp;</div>
      <div class="color_chooser" style="background-color: #006600;">&nbsp;</div>
      <div class="color_chooser" style="background-color: #cc0099;">&nbsp;</div>
      <div class="color_chooser" style="background-color: #990099;">&nbsp;</div>
      
      <div class="color_chooser" style="background-color: #009999;">&nbsp;</div>
      
    </div>
  </div>
  <div style="width: 14px; height: 50px; float: left; padding-top: 1px; text-align: center">
    <img style="max-width: 8px; cursor: pointer;" title="Close" style="cursor: pointer;" class="close_reply_modal_dialog" src="<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/icons/x.969595.png" />
  </div>
  
  <?php /* ?>
  
  <div style="width: 90px; height: 78px; float: left; text-align: center; padding-top: 3px; display: table;">
    <div style="display: table-cell; vertical-align: middle;">
      <img style="max-height: 75px; max-width: 75px;" src="<?php echo $this->basepath; ?>user_generated_data/profile_picture/<?php echo $this->user_property->profile_picture; ?>" />
    </div>
  </div>
  <div style="width: 8px; height: 78px; float: left; padding-top: 3px; background: url(<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/icons/left.tips.969595.png) center no-repeat;">&nbsp;
  </div>
  <div style="width: 388px; height: 78px; float: left; padding-top: 3px; text-align: right;">
    <input type="hidden" name="shout_id" id="shout_id" value="" />
    <input type="hidden" name="circle" id="circle" value="" />
    <textarea name="comment" id="comment" spellcheck="false" style="width: 388px; height: 75px; color: #FFF; background-color: #969595; border-radius:0px; border: 0; font-family: 'Droid Sans',sans-serif; font-size: 10px;" maxlength="500" placeholder="Reply shout"></textarea>
    <div style="height: 6px; width: 100%;">&nbsp;</div>
    <input type="button" name="btn_reply" id="btn_reply" value="Reply Shout" style="border: 0; cursor: pointer; font-family: 'Droid Sans',sans-serif; font-size: 14px; background-color: #1f1f1f; color: #999; padding: 5px 15px;" />
  </div>
  <div style="width: 24px; height: 78px; float: left; padding-top: 3px; text-align: center">
    <img title="Close" style="cursor: pointer;" class="close_reply_modal_dialog" src="<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/icons/x.969595.png" />
  </div>
  
  <?php */ ?>

  
  <div class="clear"></div>
  
</div>



<div id="edit_box_div" style="display: none; width: 300px;">
  
  <div style="width: 278px; height: 50px; float: left; padding: 4px 0 0 6px; text-align: center">
    <div style="float: left; width: 210px; height: 33px; background-color: #fff;">
      <input type="text" name="edit_box_name" id="edit_box_name" value="" style="width: 210px; height: 31px; border: 0; background-color: #fff; text-align: center; font-family: 'Droid Sans',sans-serif;" />
      <input type="hidden" name="edit_box_color" id="edit_box_color" value="rgb(126, 126, 126)" />
      <input type="hidden" name="oldname" id="oldname" value="" />
    </div>
    <div id="edit_box_save" style="float: left; cursor: pointer; width: 66px; height: 33px; background: url(<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/icons/add_new_box_save.png) center no-repeat #fff; ">&nbsp;</div>
    <div class="clear"></div>
    <div style="width: 100%; height: 20px; padding-top: 4px;">
      <div class="edit_color_chooser" style="background-color: #7e7e7e;">&nbsp;</div>
      <div class="edit_color_chooser" style="background-color: #4a4848;">&nbsp;</div>
      <div class="edit_color_chooser" style="background-color: #1f1f1f;">&nbsp;</div>
      <div class="edit_color_chooser" style="background-color: #a9b802;">&nbsp;</div>
      <div class="edit_color_chooser" style="background-color: #79b004;">&nbsp;</div>
      
      <div class="edit_color_chooser" style="background-color: #5b8404;">&nbsp;</div>
      <div class="edit_color_chooser" style="background-color: #0294b8;">&nbsp;</div>
      <div class="edit_color_chooser" style="background-color: #064295;">&nbsp;</div>
      <div class="edit_color_chooser" style="background-color: #900bce;">&nbsp;</div>
      <div class="edit_color_chooser" style="background-color: #7a07b0;">&nbsp;</div>
      
      <div class="edit_color_chooser" style="background-color: #520675;">&nbsp;</div>
      <div class="edit_color_chooser" style="background-color: #b80264;">&nbsp;</div>
      <div class="edit_color_chooser" style="background-color: #c20a0a;">&nbsp;</div>
      <div class="edit_color_chooser" style="background-color: #f67106;">&nbsp;</div>
      <div class="edit_color_chooser" style="background-color: #f6af06;">&nbsp;</div>
      
      <div class="edit_color_chooser" style="background-color: #0033ff;">&nbsp;</div>
      <div class="edit_color_chooser" style="background-color: #6633cc;">&nbsp;</div>
      <div class="edit_color_chooser" style="background-color: #006600;">&nbsp;</div>
      <div class="edit_color_chooser" style="background-color: #cc0099;">&nbsp;</div>
      <div class="edit_color_chooser" style="background-color: #990099;">&nbsp;</div>
      
      <div class="edit_color_chooser" style="background-color: #009999;">&nbsp;</div>
      
    </div>
  </div>
  <div style="width: 14px; height: 50px; float: left; padding-top: 1px; text-align: center">
    <img style="max-width: 8px; cursor: pointer;" title="Close" style="cursor: pointer;" class="close_edit_modal_dialog" src="<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/icons/x.969595.png" />
  </div>
</div>


<div id="delete_confirm" style="display: none; width: 300px;">
  <div style="width: 100%; height: 24px; padding-top: 5px; color: #fff; font-size: 14px; text-align: center">Are you sure to delete this box?</div>
  <div class="clear"></div>
  <div style="width: 50%; height: 25px; float: left;">
    <div id="delete_confirmed" style="float: right; width: 60px; height: 25px; text-align: center; background-color: #666666; font-size: 15px; margin-right: 5px; display: table; cursor: pointer;">
      <span style="display: table-cell; vertical-align: middle;"><strong>Yes</strong></span>
    </div>
  </div>
  <div style="width: 50%; height: 25px; float: left;">
    <div class="close_delete_confirm" style="float: left; width: 60px; height: 25px; text-align: center; background-color: #1f1f1f; font-size: 15px; margin-left: 5px; display: table; cursor: pointer;">
      <span style="display: table-cell; vertical-align: middle;"><strong>No</strong></span>
    </div>
  </div>
  
  <input type="hidden" name="circle_to_delete" id="circle_to_delete" value="" />
  
</div>


</div><!--[ end withjs ]-->