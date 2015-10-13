<script language='JavaScript' src='<?php echo $this->basepath; ?>libraries/js/jquery-1.5.1.min.js'></script>

<script language="javascript">
$(document).ready(function(){

	$("#login_form_submit_button").click(function(){
		var username = $("#login_form_username").val();
		var password = $("#login_form_password").val();
		
		$.get("<?php echo $this->basepath; ?>" + "user/user/login/" + username + "/" + password, {}, function(data){
			if(data == "NOAUTH" || data == "0" || data == ""){
				alert("Data yang Anda masukkan salah: " + data);
				return false;
			} else {
				window.location.replace("<?php echo $this->basepath; ?>");
			}

		});
		
	});
	
	$("#login_form").submit(function(){
		return false;
	});
	
});
</script>

<form id='login_form' action=''>
<div class='div_login_form'>
  <div style='float:left;width:40%'>Username</div>
  <div style='float:left;width:60%'>
    <input type='text' class='login_form_text_input' size='20' name='login_form_username' id='login_form_username' />
  </div>
  <div style='float:left;width:40%'>Password</div>
  <div style='float:left;width:60%'>
    <input type='password' class='login_form_text_input' size='20' name='login_form_password' id='login_form_password' />
  </div>
  <div style='float:left;width:100%'>
    <input type='submit' id='login_form_submit_button' name='login_form_submit_button' value='Submit' />
  </div>
</div>
</form>
