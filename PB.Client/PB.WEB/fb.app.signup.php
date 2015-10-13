<?php
$basepath = "https://" . $_SERVER['SERVER_NAME'] . "/";	// jangan lupa / di akhir
//	$basepath = $_SERVER['SERVER_NAME'] . "/";	// jangan lupa / di akhir
if(isset($_SESSION['brand_page_id'])){
	$basepath = "https://www.facebook.com/pages/Airbotol/".$_SESSION['brand_page_id']."?sk=app_353789864649141";
}
/*
<iframe src="https://www.facebook.com/plugins/registration?
             client_id=353789864649141&
             redirect_uri=<?php echo $basepath; ?>&
             fields=name,birthday,gender,location,email,captcha"
        scrolling="auto"
        frameborder="no"
        style="border:none"
        allowTransparency="true"
        width="100%"
        height="900">
</iframe>
*/

?>


<iframe src="https://www.facebook.com/plugins/registration?
             client_id=353789864649141&
             redirect_uri=<?php echo $basepath; ?>&
             fields=[{'name':'name'},{'name':'birthday'},{'name':'gender'},{'name':'location'},{'name':'email'},{'name':'via_fb_app', 'default':'1', 'type':'hidden', 'description':'Via Facebook App'}]"
        scrolling="auto"
        frameborder="no"
        style="border:none"
        allowTransparency="true"
        width="100%"
        height="900">
</iframe>