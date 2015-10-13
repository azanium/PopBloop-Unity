<!DOCTYPE html>
<!-- Update your html tag to include the itemscope and itemtype attributes -->
<html lang="en" itemscope itemtype="http://schema.org/Online Game">
<head>

<meta name="author" content="mukhtarhudaya@gmail.com" />
<meta name="description" content="Social Massive Multi-player Online Game" />
<meta name="keywords" content="3D Online Game, PopBloop, MMORPG" />


<!-- Add the following three tags inside head -->
<meta itemprop="name" content="PopBloop 3D Social Game">
<meta itemprop="description" content="Let's join PopBloop, make friends in virtual world and have fun!">
<meta itemprop="image" content="http://www.popbloop.com/tide/popbloop.img/popbloop.png">

<meta charset="utf-8" />
<title>PopBloop: It's Nothing! Yet Everything</title>

<!--link rel="stylesheet" href="<?php print($this->basepath); ?>tide/css/960/reset.css" /-->
<!--link rel="stylesheet" href="<?php print($this->basepath); ?>tide/css/960/960.css" /-->
<link rel="shortcut icon" href="<?php print($this->basepath); ?>tide/popbloop.img/favicon.gif">

<!--link rel="stylesheet" type="text/css" href="<?php print($this->basepath); ?>libraries/js/jquery.ui/css/custom-theme/jquery-ui-1.8.14.custom.css" media="screen" /-->
<script language="javascript" src="<?php print($this->basepath); ?>libraries/js/jquery-1.6.1.min.js"></script>
<!--script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script-->

<!--script language="javascript" src="<?php print($this->basepath); ?>libraries/js/jquery.ui/js/jquery-ui-1.8.14.custom.min.js"></script-->


<meta property="og:title" content="PopBloop - Create your world and get new friends!" />
<meta property="og:type" content="game" />
<meta property="og:url" content="http://popbloop.com" />
<meta property="og:image" content="http://popbloop.com/tide/popbloop.img/popbloop.fb.jpg" />
<meta property="og:site_name" content="PopBloop" />
<meta property="fb:admins" content="1834091583" />

<link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'>
  
<?php if(isset($_SESSION['brand_page_id'])){ ?>
  <link rel="stylesheet" type="text/css" href="<?php print($this->basepath); ?>tide/css/popbloop.fb.css" media="screen">
<?php } else { ?>
  <link rel="stylesheet" type="text/css" href="<?php print($this->basepath); ?>tide/css/popbloopdark.css" media="screen">
<?php } ?>
  
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-29770706-1']);
  _gaq.push(['_setDomainName', 'popbloop.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</head>

<!--
<body style="background:url(<?php print($this->basepath); ?>modules/000_user_interface/images/lilo.logo.big.png) center repeat-x; background-position:top;">
-->
<body spellcheck="false" style="overflow: hidden">

<div id="fb-root"></div>
<script src="https://connect.facebook.net/en_US/all.js"></script>
<script>
FB.init({
appId : '353789864649141',
status : true, // check login status
cookie : true, // enable cookies to allow the server to access the session
xfbml : true // parse XFBML
});
</script>

<?php /* http://www.hyperarts.com/blog/facebook-iframe-apps-getting-rid-of-scrollbars/ */ ?>

<script type="text/javascript">
window.fbAsyncInit = function() {
FB.Canvas.setAutoGrow();
}
</script>

<?php echo $this->top; ?>

<?php echo $this->middle; ?>


<?php echo $this->left; ?>
<?php echo $this->right; ?>
<?php echo $this->bottom; ?>

<div class="loading_div"></div>

</body>
</html>
