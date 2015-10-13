<script type="text/javascript">
	$(document).ready(function(){
		$('#tabs').tabs({selected: <?php echo $this->active_tabs; ?>});
		
		function loadArticles(){
//			alert('load articles');
			$('.loading_div').show();
			$.post("<?php echo $this->basepath; ?>article/admin/getarticles", {}, function(data){
				var all_articles = eval('('+data+')');
				
				var html = "<table style='width:100%;'>";
				html = html + "<tr>";
				html = html + "<th>No</th>";
				html = html + "<th>Title</th>";
        html = html + "<th>Alias</th>";
				html = html + "<th>Text</th>";
				html = html + "<th>Operation</th>";
				html = html + "</tr>";
				
				
				
				var no = 1;
				for(idx = 0; idx < all_articles.length; idx++){
					var _title = all_articles[idx]['title'];
					var _text = all_articles[idx]['text'];
          var _alias = all_articles[idx]['alias'];
					var _text_short = all_articles[idx]['text_short'];
					var _lilo_id = all_articles[idx]['lilo_id'];
					html = html + "<tr>";
					html = html + "<td style=width:10px;>"+no+"</td>";
					html = html + "<td style='width:200px;'>"+_title+"</td>";
          html = html + "<td style='width:100px;'>"+_alias+"</td>";
					html = html + "<td style='width:500px;'><textarea disabled=\"disabled\" style='min-width:500px; min-height:100px; max-width:500px; max-height:100px; font-size:7px; background:transparent; border:0; color:#666;'>"+_text_short+" (...)</textarea></td>";
					html = html + "<td style='width:150px;'><a style='text-decoration:none;' class='view_article' href='<?php echo $this->basepath; ?>article/guest/get/"+_alias+"' target='_blank'>View</a>&nbsp;|&nbsp;<a class='edit_article' id='editarticle_"+_lilo_id+"'>Edit</a>&nbsp;|&nbsp;<a class='delete_article' id='deletearticle_"+_lilo_id+"'>Delete</a></td>";
					html = html + "</tr>";
					no++;
				}
				
				html = html + "</html>";
				
				$('#list_articles').html(html);
				
			});
			$('.loading_div').hide();
		}
		
		loadArticles();
		
		$('#new_article_save').live('click', function(){
			var _title = $.trim($('#new_article_title').val());
			var _text = $.trim($('#new_article_text').val());
			var _alias = $.trim($('#new_article_alias').val());
			
			if(_title == '' || _text == ''){
				alert("Title and Text should not be empty.");
				return false;
			}
			
			$.post("<?php echo $this->basepath; ?>article/admin/addarticle", {title: _title, text: _text, alias: _alias}, function(data){
				if(data.length > 0){
					$('#new_article_title').val('');
					$('#new_article_text').val('');
					$('#new_article_alias').val('');
					
					alert('Artikel baru tersimpan');
					
					loadArticles();
					$('#tabs').tabs('select', 0);
				}
			});
		});
		
		$('#new_slide_save').live('click', function(){
			var _no = $.trim($('#new_slide_no').val());
			var _title = $.trim($('#new_slide_title').val());
			var _image = $.trim($('#new_slide_image').val());
			var _description = $.trim($('#new_slide_description').val());
			var _link = $.trim($('#new_slide_link').val());

			if(_no == '' || _title == ''){
				alert("No and Title should not be empty.");
				return false;
			}
			

			$.post("<?php echo $this->basepath; ?>article/admin/addslide", {no: _no, title: _title, image: _image, description: _description, 'link': _link}, function(data){
				if(data.length > 0){
					$('#new_slide_no').val('');
					$('#new_slide_title').val('');
					$('#new_slide_image').val('');
					$('#new_slide_description').val('');
					$('#new_slide_link').val('');
					
					alert('Slide baru tersimpan');
					
					loadSlides();
					$('#tabs').tabs('select', 2);
				}
			});


		


		});


			function loadSlides(){
	//			alert('load articles');
				$('.loading_div').show();
				$.post("<?php echo $this->basepath; ?>article/admin/getslides", {}, function(data){
					var all_slides = eval('('+data+')');
					
					var html = "<table style='width:100%;'>";
					html = html + "<tr>";
					html = html + "<th>No</th>";
					html = html + "<th>Image</th>";
					html = html + "<th>Title & Description</th>";
					html = html + "<th>Operation</th>";
					html = html + "</tr>";
					
					
					
					for(idx = 0; idx < all_slides.length; idx++){
						var _no = all_slides[idx]['no'];
						var _title = all_slides[idx]['title'];
						var _description = all_slides[idx]['description'];
						var _image = all_slides[idx]['image'];
						var _link = all_slides[idx]['link'];
						var _lilo_id = all_slides[idx]['lilo_id'];
						html = html + "<tr>";
						html = html + "<td style=width:10px;>"+_no+"</td>";
						// html = html + "<td style='width:200px;'>"+_title+"</td>";
						html = html + "<td style='width:300px;'><img src='"+_image+"' style='max-width:250; max-height:100px;' /></td>";
						html = html + "<td style='width:300px;'><br /><strong>"+_title+"</strong><br /><textarea disabled='disabled' style='min-width:300px; min-height:100px; max-width:300px; max-height:100px; font-size:7px; background:transparent; border:0; color:#666;'>"+_description+"</textarea></td>";
						html = html + "<td style='width:200px;'><a class='edit_slide' id='editslide_"+_lilo_id+"'>Edit</a>&nbsp;|&nbsp;<a class='delete_slide' id='deleteslide_"+_lilo_id+"'>Delete</a></td>";
						html = html + "</tr>";
					}
					
					html = html + "</html>";
					
					$('#list_slides').html(html);
					
				});
				$('.loading_div').hide();
			}

		loadSlides();

    $("#editor_div_article").dialog({
      autoOpen: false, 
      minWidth: 820, 
      minHeight: 400,
      modal: true,

			buttons: [
				{
					text: "Save Changes",
					click: function() {
            var lilo_id = $("#article_update_lilo_id").val();
            var alias = $("#article_update_alias").val();
            var title = $("#article_update_title").val();
            var text = $("#article_update_text").val();
            
            $.post("<?php echo $this->basepath; ?>article/admin/updatearticle", {lilo_id: lilo_id, alias: alias, title: title, text: text}, function(data){
              if($.trim(data) == 'OK'){
                alert("Data updated succesfully");
                loadArticles();
                $("#editor_div_article").dialog('close');
              } else {
                alert(data);
                $("#editor_div_article").dialog('close');
                return false;
              }
            });
            
						return;
					}
				},
				{
					text: "Cancel",
					click: function() {
						$("#editor_div_article").dialog('close');
						return;
					}
				},
        
      ]
    });
    
    
    $("#editor_div").dialog({
      autoOpen: false, 
      minWidth: 820, 
      minHeight: 400,
      modal: true,

			buttons: [
				{
					text: "Save Changes",
					click: function() {
            var lilo_id = $("#slide_update_lilo_id").val();
            var no = $("#slide_update_no").val();
            var title = $("#slide_update_title").val();
            var description = $("#slide_update_description").val();
            var image = $("#slide_update_image").val();
            var link = $("#slide_update_link").val();
            
            $.post("<?php echo $this->basepath; ?>article/admin/updateslide", {lilo_id: lilo_id, no: no, title: title, description: description, image: image, link: link}, function(data){
              if($.trim(data) == 'OK'){
                alert("Data updated succesfully");
                loadSlides();
                $("#editor_div").dialog('close');
              } else {
                alert(data);
                $("#editor_div").dialog('close');
                return false;
              }
            });
            
						return;
					}
				},
				{
					text: "Cancel",
					click: function() {
						$("#editor_div").dialog('close');
						return;
					}
				},
        
      ]
    });
    
    $('.delete_slide').live('click', function(data){
      var _id = $(this).attr('id');
      // alert(_id);
      
      var _id_split = _id.split('_');
      var lilo_id = _id_split[1];
      
      if(confirm('Are you sure to delete this slide?')){
        $.post("<?php echo $this->basepath; ?>article/admin/deleteslide", {lilo_id: lilo_id}, function(data){
          if($.trim(data) == "OK"){
            alert("Data successfully updated");
            loadSlides();
          } else {
            alert(data);
            return false;
          }
        });
      }
      
    });
    
    $('.delete_article').live('click', function(){
      var _id = $(this).attr('id');
      // alert(_id);
      
      var _id_split = _id.split('_');
      var lilo_id = _id_split[1];
      
      if(confirm('Are you sure to delete this article?')){
        $.post("<?php echo $this->basepath; ?>article/admin/deletearticle", {lilo_id: lilo_id}, function(data){
          if($.trim(data) == "OK"){
            alert("Data successfully updated");
            loadArticles();
          } else {
            alert(data);
            return false;
          }
        });
      }
      
    });
    
    $('.edit_article').live('click', function(data){
      var _id = $(this).attr('id');
      // alert(_id);
      
      var _id_split = _id.split('_');
      var lilo_id = _id_split[1];
      // alert(lilo_id);
      
      $.post("<?php echo $this->basepath; ?>article/admin/getonearticle", {id: lilo_id}, function(data){
        // alert(data);
        var article = eval('(' + data + ')');
        // alert(slide.lilo_id);
        
        $("#article_update_lilo_id").val(article.lilo_id);
        $("#article_update_alias").val(article.alias);
        $("#article_update_title").val(article.title);
        $("#article_update_text").val(article.text);
        
        $("#editor_div_article").dialog('open');
        
      });
      
    });
    
    $('.edit_slide').live('click', function(data){
      var _id = $(this).attr('id');
      // alert(_id);
      
      var _id_split = _id.split('_');
      var lilo_id = _id_split[1];
      // alert(lilo_id);
      
      $.post("<?php echo $this->basepath; ?>article/admin/getoneslide", {id: lilo_id}, function(data){
        // alert(data);
        var slide = eval('(' + data + ')');
        // alert(slide.lilo_id);
        
        $("#slide_update_lilo_id").val(slide.lilo_id);
        $("#slide_update_no").val(slide.no);
        $("#slide_update_title").val(slide.title);
        $("#slide_update_description").val(slide.description);
        $("#slide_update_image").val(slide.image);
        $("#slide_update_link").val(slide.link);
        
        $("#editor_div").dialog('open');
        
      });
      
    });
	});
</script>

<div class="centered shadow transbg" style="width:960px;">
	<div style="float:left; width:960px; height:40px;"></div>
  
	<div style="float:left; width:960px; text-align:center" id="slideshow_management">
    <div id="tabs" style="float:left; width:952px; min-height:440px">
      <ul>
        <li><a href="#tabs-1">Articles</a></li>
        <li><a href="#tabs-2">New Article</a></li>
        <li><a href="#tabs-3">Slideshow</a></li>
        <li><a href="#tabs-4">New Slide</a></li>
			</ul>
		  <div id="tabs-1">
      	<div style="width:100%" id="list_articles">
        	
        </div>
      </div>
		  <div id="tabs-2">
        <table style="width:100%; text-align:left; border:0">
          <tr>
            <th>Title</th>
            <td><input type="text" name="new_article_title" id="new_article_title" value="" style="width:90%;" maxlength="100" /></td>
          </tr>
          <tr>
            <th>Alias</th>
            <td><input type="text" name="new_article_alias" id="new_article_alias" value="" style="width:90%;" maxlength="100" /></td>
          </tr>
          <tr>
            <th>Text</th>
            <td><textarea name="new_article_text" id="new_article_text" style="width:90%; height:300px" ></textarea></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input type="button" name="new_article_save" id="new_article_save" value="Save" /></td>
          </tr>
        </table>
      </div>
		  <div id="tabs-3">
      	<div style="width:100%" id="list_slides">
        
        </div>
      </div>
		  <div id="tabs-4">
        <table style="width:100%; text-align:left; border:0">
          <tr>
            <th>No</th>
            <td><input type="text" name="new_slide_no" id="new_slide_no" value="" size="3" maxlength="3" /></td>
          </tr>
          <tr>
            <th>Title</th>
            <td><input type="text" name="new_slide_title" id="new_slide_title" value="" style="width:90%;" maxlength="100" /></td>
          </tr>
          <tr>
            <th>Image URL</th>
            <td><input type="text" name="new_slide_image" id="new_slide_image" value="" style="width:90%;" maxlength="100" /></td>
          </tr>
          <tr>
            <th>Description</th>
            <td><textarea name="new_slide_description" id="new_slide_description" style="width:90%; height:100px" ></textarea></td>
          </tr>
          <tr>
            <th>Link</th>
            <td><input type="text" name="new_slide_link" id="new_slide_link" value="" style="width:90%;" maxlength="100" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input type="button" name="new_slide_save" id="new_slide_save" value="Save" /></td>
          </tr>
        </table>
      </div>
    </div>
  </div>



</div>


<div style="width: auto; min-height: 58.4px; height: auto; min-width:600px; " class="ui-dialog-content ui-widget-content" id="editor_div" 
	title="Edit Slide">
  <input type="hidden" name="slide_update_lilo_id" id="slide_update_lilo_id" value="" />
  <table style="width:100%;">
    <tr>
      <th style="width:200px;">No</th>
      <td><input type="text" style="width:90%;" name="slide_update_no" id="slide_update_no" /></td>
    </tr>
    <tr>
      <th style="width:200px;">Title</th>
      <td><input type="text" style="width:90%;" name="slide_update_title" id="slide_update_title" /></td>
    </tr>
    <tr>
      <th style="width:200px;">Description</th>
      <td>
        <textarea style="width:90%; height:100px;" name="slide_update_description" id="slide_update_description"></textarea>
      </td>
    </tr>
    <tr>
      <th style="width:200px;">Image</th>
      <td><input type="text" style="width:90%;" name="slide_update_image" id="slide_update_image" /></td>
    </tr>
    <tr>
      <th style="width:200px;">Link</th>
      <td><input type="text" style="width:90%;" name="slide_update_link" id="slide_update_link" /></td>
    </tr>
  </table>
</div>

<div style="width: auto; min-height: 58.4px; height: auto; min-width:600px; " class="ui-dialog-content ui-widget-content" id="editor_div_article" 
	title="Edit Article">
  <input type="hidden" name="article_update_lilo_id" id="article_update_lilo_id" value="" />
  <table style="width:100%;">
    <tr>
      <th style="width:100px;">Title</th>
      <td><input type="text" style="width:90%;" name="article_update_title" id="article_update_title" /></td>
    </tr>
    <tr>
      <th style="width:100px;">Alias</th>
      <td><input type="text" style="width:90%;" name="article_update_alias" id="article_update_alias" /></td>
    </tr>
    <tr>
      <th style="width:100px;">Text</th>
      <td>
        <textarea style="width:90%; height:250px;" name="article_update_text" id="article_update_text"></textarea>
      </td>
    </tr>
  </table>
</div>


