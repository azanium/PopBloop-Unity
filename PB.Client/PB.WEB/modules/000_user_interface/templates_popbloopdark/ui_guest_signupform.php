<?php
	include_once('modules/000_user_interface/guest.php');
	include_once('modules/000_user_interface/user.php');
	
	// recaptcha
	require_once('libraries/recaptcha/recaptchalib.php');
	$publickey = '6Lc4rc0SAAAAABnStfbcMto4QuRhJzMPU4Hq5UfV';
	
	// count page views
	include_once('modules/009_log/guest.php');
?>

<noscript>
	<style>
  	.withjs {display:none;}
  </style>
  <font face="Palatino Linotype, Book Antiqua, Palatino, serif" size="+2">
		Enable JavaScript in your browser to access this site properly!
  </font>
</noscript>

<div class="withjs">


<script language="javascript" src="<?php print($this->basepath); ?>libraries/js/jquery.ui.popbloop.dark/js/jquery-ui-1.8.17.custom.min.js"></script>

<link rel="stylesheet" type="text/css" href="<?php print($this->basepath); ?>libraries/js/jquery.ui.popbloop.dark/css/custom-theme/jquery-ui-1.8.17.custom.css" />

<link rel="stylesheet" type="text/css" media="screen" href="<?php print($this->basepath); ?>modules/000_user_interface/css/default_popbloopdark.css" />


<?php
	// TipTip ToolTip
?>
<script language="javascript" src="<?php print($this->basepath); ?>libraries/js/tipTipv13/jquery.tipTip.minified.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="<?php print($this->basepath); ?>libraries/js/tipTipv13/tipTip.css" />


<?php  ?>
<script src="<?php echo $this->basepath; ?>libraries/js/jquery_placeholder.js"></script>
<style>
		label.placeholder {
		cursor: text;

		padding: 8px 4px 4px 14px;
		font-size: 12px;
		font-weight: normal;

		color: #999999;
	}

	input:placeholder, textarea:placeholder {
		color: #999999;
	}
	input::-webkit-input-placeholder, textarea::-webkit-input-placeholder {
		color: #999999;
	}

</style>
<script language="javascript">
	$(document).ready(function(){
		$('input[placeholder],textarea[placeholder]').placeholder();
		
		$('.signup_data').tipTip({defaultPosition: 'right'});
		
	});
</script>
<?php  ?>

<script language="javascript">
	$(document).ready(function(){

		function signMeUp(){
			$('.loading_div').show();
			var new_email = $("#new_email").val();
			var new_avatarname = $("#new_avatarname").val();
			var new_password = $("#new_password").val();
			var new_fullname = $("#new_fullname").val();
			var new_confirm_password = $("#new_confirm_password").val();
			
			var new_username = new_email;
			
			var new_handphone = $("#new_handphone").val();
			var new_twitter = $("#new_twitter").val();


			var new_sex = $("#new_sex").val();
			var new_birthday = $("#new_birthday").val();
			var new_location = $("#new_location").val();
			
			
			var recaptcha_response_field = $("#recaptcha_response_field").val();
			var recaptcha_challenge_field = $("#recaptcha_challenge_field").val();
			
			// alert("rrf: " + recaptcha_response_field + ", rcf: " + recaptcha_challenge_field);
			
			if($.trim(new_email) == ""){
				alert("Email should not empty.");
				$("#new_email").focus();
				$('.loading_div').hide();
				return;
			}
			
			if($.trim(new_password) == ""){
				alert("Password should not empty.");
				$("#new_password").focus();
				$('.loading_div').hide();
				return;
			}
			
			if(new_password.length < 6){
				alert("Use at least six characters for your password.");
				$("#new_password").focus();
				$('.loading_div').hide();
				return;
			}
			
			if(new_password != new_confirm_password){
				alert("Your Confirm Password doesn't match with your Password!");
				$("#new_confirm_password").focus();
				$('.loading_div').hide();
				return;
			}
			
			// new_fullname
			if($.trim(new_fullname) == ""){
				alert("Full Name should not empty.");
				$("#new_fullname").focus();
				$('.loading_div').hide();
				return;
			}

			if($.trim(new_avatarname) == ""){
				alert("Avatar name should not empty.");
				$("#new_avatarname").focus();
				$('.loading_div').hide();
				return;
			}
			
			if(new_avatarname.length > 24){
				alert("Use up to 24 characters for your avatarname.");
				$("#new_avatarname").focus();
				$('.loading_div').hide();
				return;
			}

			if($.trim(new_sex) == ""){
				alert("Select your gender.");
				$("#new_sex").focus();
				$('.loading_div').hide();
				return;
			}

			if($.trim(new_birthday) == ""){
				alert("Enter your birthday.");
				$("#new_birthday").focus();
				$('.loading_div').hide();
				return;
			}

			if($.trim(new_location) == ""){
				alert("Enter your location.");
				$("#new_location").focus();
				$('.loading_div').hide();
				return;
			}


			if($.trim(new_handphone) == ""){
				alert("Enter your handphone number.");
				$("#new_handphone").focus();
				$('.loading_div').hide();
				return;
			}


			if($.trim(new_twitter) == ""){
				alert("Enter your Twitter.");
				$("#new_twitter").focus();
				$('.loading_div').hide();
				return;
			}

			if($.trim(recaptcha_response_field) == ''){
				alert('Please enter the Captcha');
				$("#recaptcha_response_field").focus();
				$('.loading_div').hide();
				return;
			}

			
			var email_used = redundancy_check('email', new_email);
			var avatarname_used = redundancy_check('avatarname', new_avatarname);

			// kirim ke server
			$.post(	"<?php echo $this->basepath; ?>user/guest/add_user", 
						 	{	'username':new_username,'fullname':new_fullname,'password':new_password,
								'avatarname':new_avatarname,
								'email':new_email,'automate_login':'1',
								'handphone':new_handphone,'twitter':new_twitter,
								'sex':new_sex,'birthday':new_birthday,'location':new_location,
								'recaptcha_response_field':recaptcha_response_field, 'recaptcha_challenge_field':recaptcha_challenge_field}, 
						 	function(data){
								if($.trim(data) == "OK"){
									$('.loading_div').hide();
									window.location.replace("<?php echo $this->basepath; ?>");
								} else {
									alert("Server: " + data);
									Recaptcha.reload();
									$('.loading_div').hide();
									return;
								}
							}
			);
		}
    
    $('#sign_up_btn').live('click', function(){
      signMeUp();
    });
    
		$(".signup_data").live('keydown', function(e){
			if(e.keyCode == 13){
				signMeUp();
			}
		});

		$("#new_email").change(function(){
			var new_email = $("#new_email").val();
			var used = redundancy_check('email', new_email);
		});
		
		$("#new_avatarname").change(function(){
			var new_avatarname = $("#new_avatarname").val();
			var used = redundancy_check('avatarname', new_avatarname);
		});
		
		function redundancy_check(check, value){
			var used = 0;
			switch(check){
				case 'email':
					$.post("<?php print($this->basepath); ?>user/guest/redundancy_check/email/" + value, {}, function(data){
						if(data != "0"){
							alert("Email " + value + " sudah terdaftar. Gunakan email lain.");
							$("#new_email").val("");
							$("#new_email").focus();
						}
					});
					break;
				case 'avatarname':
					$.post("<?php print($this->basepath); ?>user/guest/property_redundancy_check/avatarname/" + value, {}, function(data){
						if(data != "0"){
							alert("Avatar name " + value + " sudah terdaftar. Gunakan avatar name lain.");
							$("#new_avatarname").val("");
							$("#new_avatarname").focus();
						}
					});
					break;
			}
		}



	});
</script>





<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=353789864649141";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>


<script type="text/javascript">
	var RecaptchaOptions = {
		theme : 'custom',
		custom_theme_widget: 'recaptcha_widget'
	};
</script>

<style type="text/css">
.new_input{
  width:370px; 
  padding-left:15px; 
  margin-bottom:10px; 
  height: 22px; 
  color:#666;
  height:28px;
  font-size:14px;
  border-radius: 4px 4px 4px 4px;
}

.new_input:focus{
	-moz-box-shadow:    0 0 15px #eee;
	-webkit-box-shadow: 0 0 15px #eee;
	box-shadow:         0 0 15px #eee;
}

</style>

<div class="container_12" style="min-height:580px; padding-top:60px">
  <div class="grid_3">
    &nbsp;
  </div>
  <div class="grid_6" style="text-align:center;">
    <div style="text-align:left; color:#fff; font-size:24px; margin-bottom:10px; margin-left:40px;">Join Popbloop</div>
    <div class="clear"></div>
		
    <input type="text" title="Email is required" name="new_email" id="new_email" placeholder="Email" value="<?php echo $this->signup_email; ?>" class="new_input signup_data" />
    <div class="clear"></div>
    <input type="password" title="Password must be at least 6 characters" name="new_password" id="new_password" placeholder="Password" value="<?php echo $this->signup_password; ?>" class="new_input signup_data" />
    <div class="clear"></div>
    
    
    <input type="password" title="Please confirm your password" name="new_confirm_password" id="new_confirm_password" placeholder="Confirm Password" value="" class="new_input signup_data" />
    <div class="clear"></div>
		
    <input type="text" title="Name is required" name="new_fullname" id="new_fullname" placeholder="Full Name" value="<?php echo $this->signup_fullname; ?>" class="new_input signup_data" />
    <div class="clear"></div>
    
    <input type="text" title="Avatar name will be shown in your avatar." name="new_avatarname" id="new_avatarname" placeholder="Avatar Name" value="" class="new_input signup_data" />
    <div class="clear"></div>
		
		
		
		<select title="Select your gender" name="new_sex" id="new_sex" class="new_input signup_data" style="border-radius:5px; width: 386px; height: 32px; font-size: 15px; padding-left: 10px; padding-top: 4px;">
			<option value="">Gender</option>
			<option value="male">Male</option>
			<option value="female">Female</option>
		</select>
    <div class="clear"></div>
		
		
		<script type="text/javascript" src="<?php echo $this->basepath; ?>libraries/js/jquery.ui.popbloop.dark/js/jquery-ui-1.8.17.custom.min.js"></script>
		<link type="text/css" rel="stylesheet" href="<?php echo $this->basepath; ?>libraries/js/jquery.ui.popbloop.dark/css/custom-theme/jquery-ui-1.8.17.custom.css" />
		<script type="text/javascript">
			$(document).ready(function(){
				//alert('sdfsdf');
				$('#new_birthday').datepicker({dateFormat: 'dd-mm-yy'});
			});
		</script>
    <input type="text" title="Input your birthday in dd-mm-yy format." name="new_birthday" id="new_birthday" placeholder="Birthday" value="" class="new_input signup_data" />
    <div class="clear"></div>
		
		
    <input type="text" title="Input your location" name="new_location" id="new_location" placeholder="Location" value="" class="new_input signup_data" />
    <div class="clear"></div>
		
		
    <div style="height: 10px;"></div>
    <div class="clear"></div>
		
    <div style="height: 156px; margin-right: 12px; background: url(<?php echo $this->basepath; ?>images/event.jakcloth/jakcloth.sign.up.png) top center no-repeat;"></div>
    <div class="clear"></div>
		
    <input type="text" title="We'll contact your number if you win!" name="new_handphone" id="new_handphone" placeholder="Handphone Number" value="" class="new_input signup_data" />
    <div class="clear"></div>
    <input type="text" title="If you win, you'll be mentioned by @JakCloth!" name="new_twitter" id="new_twitter" placeholder="Twitter Account" value="" class="new_input signup_data" />
    <div class="clear"></div>
		
    <div style="height: 36px;"></div>
    <div class="clear"></div>
		
		
		
		
    <div>
      <div id="recaptcha_widget" style="display:none">
      
        <div id="recaptcha_image" style="width:382px; margin-left:40px; padding:10px 42px 10px 42px; border-radius:4px; background-color:#fff; text-align:center;"></div>
        <div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect please try again</div>
        <div style="width:42%; float:left; padding-top:5px;" class="recaptcha_only_if_image sign_content">Enter the words</div>
        <div style="width:42%; float:left; padding-top:5px;" class="recaptcha_only_if_audio sign_content">Enter the numbers</div>
        
        <div style="width:58%; float:left; padding-top:5px;" class="sign_content"><input type="text" id="recaptcha_response_field" name="recaptcha_response_field" style="width:180px;" /></div>
        
        <div title="Get another CAPTCHA" style="width:50%; float:left"><a style="text-decoration:none; color:#FFF; cursor:pointer;" href="javascript:Recaptcha.reload()">Reload</a></div>
        
        <!--[
        <div title="Get an audio CAPTCHA" class="recaptcha_only_if_image" style="width:35%; float:left"><a style="text-decoration:none; color:#FFF; cursor:pointer;" href="javascript:Recaptcha.switch_type('audio')">Audio</a></div>
        <div title="Get an image CAPTCHA" class="recaptcha_only_if_audio" style="width:35%; float:left"><a style="text-decoration:none; color:#FFF; cursor:pointer;" href="javascript:Recaptcha.switch_type('image')">Image</a></div>
        ]-->
        
        <div title="Help" style="width:50%; float:left"><a style="text-decoration:none; color:#FFF; cursor:pointer;" href="javascript:Recaptcha.showhelp()">Help</a></div>
        
      </div>
        
      <script type="text/javascript"
      src="http://www.google.com/recaptcha/api/challenge?k=<?php echo $publickey; ?>">
      </script>

      <noscript>
        <iframe src="http://www.google.com/recaptcha/api/noscript?k=<?php echo $publickey; ?>"
        height="200" width="250" frameborder="0"></iframe><br>
        <textarea name="recaptcha_challenge_field" id="recaptcha_challenge_field" rows="3" cols="40">
        </textarea>
        <input type="hidden" name="recaptcha_response_field"
        value="manual_challenge">
      </noscript>

    </div>
    <div class="clear"></div>
    
    <div style="float:left; width:25px; height:20px; text-align:left; margin-left:32px;">
      <input type="checkbox" id="remember_me" name="remember_me" style="border:0;">
    </div>
    <div style="float:left; height:20px; padding-top:3px;">
      <label for="remember_me" style="margin-bottom:6px;">Keep me logged-in on this computer</label>
    </div>
    <div class="clear"></div>
    <div style="text-align:left; color:#fff; background-color:#434343; font-size:16px; margin:5px 36px; padding:5px 15px; border-radius: 4px 4px 0 0; font-size:12px;">
      By clicking the button, you agree to the terms below:
    </div>
    <div class="clear"></div>
    
    
    
    
    <div style="text-align:left; color:#fff; background-color:#434343; font-size:16px; margin:0px 36px; padding:5px 15px; border-radius: 0 0 4px 4px; font-size:11px; max-height:100px; overflow-y:scroll;">
			<p>Halo Bloopers,</p>
			<p>Selamat datang di Pop Bloop. Harap baca Syarat dan Ketentuan berikut ini dengan seksama sebelum mengakses dan/atau menggunakan konten layanan yang tersedia di situs web Pop Bloop. Dengan mengakses dan/atau menggunakan layanan yang tersedia di situs web Pop Bloop maka kamu menyatakan menerima dan menyetujui untuk terikat dengan Syarat dan Ketentuan ini, baik kamu anggota terdaftar Pop Bloop atau bukan. Jika kamu tidak setuju dengan Syarat dan Ketentuan ini maka kamu tidak berhak untuk menggunakan layanan-layanan yang tersedia di situs Pop Bloop</p>
			
			<h2>Definisi</h2>
			<p>Pop Bloop adalah penyedia jasa layanan media interaksi sosial dunia maya secara online berbasis permainan (game) melalui web dengan alamat URL www.popbloop.com. Untuk produk demo ini Pop Bloop diakses melalui www.jakclothku.com </p>
			<p>Konten adalah termasuk namun tidak terbatas pada setiap teks, perangkat lunak, database, format,  design grafis yang dikembangkan oleh dan atau atas nama Pop Bloop yang disediakan di situs ini dan merupakan bagian tidak terpisahkan dari situs web ini.</p>
			<p>Hak Atas Kekayaan Intelektual adalah hak cipta, paten, hak database dan hak-hak merek, desain, ilmu pengetahuan dan informasi  lainnya yang sesuai dengan peraturan perundang-undangan yang berlaku.</p>
			<p>Kita atau Kami adalah pengembang dan pengelola Pop Bloop dan mitra usaha, baik berupa perusahaan maupun individu yang menurut hukum berwenang dan sah bertindak untuk dan atas nama Pop Bloop.</p>
			<p>Kamu atau Anda adalah seluruh pengguna layanan di situs web ini, baik sendiri-sendiri maupun secara bersama-sama secara keseluruhan.</p>
			
			<h2>Persetujuan Pengguna</h2>
			<p>Dengan mengakses dan menggunakan layanan yang disediakan di Pop Bloop maka kamu dengan ini menyetujui hal - hal berikut :</p>
			
			<p>Pop Bloop menyediakan jasa layanan media interaksi sosial dunia maya secara online berbasis permainan (game) melalui web yang dapat dimainkan melalui jaringan internet dan seluruh informasi mengenai layanan tersebut dapat dilihat di alamat situs web Pop Bloop </p>
			
			<p>Pop Bloop memegang secara penuh hak untuk mengubah alamat URL sewaktu-waktu secara sepihak apabila diperlukan sesuai dengan kebutuhan guna mendukung seluruh layanan yang  kami sediakan
			Untuk dapat menggunakan layanan yang kami sediakan, kamu harus menginstall software tambahan yaitu “unity player” yang disediakan oleh Pop Bloop di situs web Pop Bloop, yang mana software tambahan dimaksud dapat diubah sewaktu-waktu secara sepihak oleh kami tanpa perlu pemberitahuan terlebih dahulu</p>
			<p>Pop Bloop tidak menyediakan jasa akses internet, dan oleh karena itu kamu bertanggung jawab penuh atas semua biaya koneksi internet yang diperlukan dalam bermain dan mengakses situs web Pop Bloop</p>
			<p>Kamu tidak boleh menggunakan website dan layanan yang disediakan untuk mempromosikan dan/atau sebagai media iklan dan/atau melakukan kegiatan lainnya yang bersifat komersial tanpa seijin Pop Bloop</p>
			<p>Untuk dapat memakai seluruh layanan yang disediakan secara maksimal, kamu harus melakukan pendaftaran/registrasi tanda pengenal (ID) di Pop Bloop</p>
			<p>Sebelum melakukan registrasi atau pembuatan ID kamu wajib membaca terlebih dahulu Syarat dan Ketentuan pengguna ini secara seksama, jika kamu sudah menyetujuinya dengan menekan “setuju” maka kami anggap kamu sudah membaca dengan baik dan seksama syarat dan ketentuan ini.</p>
			
			<p>Kamu setuju untuk memasukkan/memberikan data yang benar dan akurat tentang dirimu yang sebenarnya serta melakukan perubahan pada profilmu jika ada perubahan. Untuk dan oleh karena itu kamu secara pribadi bertanggung jawab penuh atas data yang kamu masukkan/berikan serta akibat-akibat yang mungkin akan timbul.</p>
			<p>Pop Bloop secara sepihak berhak untuk menolak dan menghapus nama ID, nama karakter, nama kelompok, nama komunitas yang dipakai apabila dianggap mengandung unsur kekerasan, pornografi, SARA, melanggar peraturan perundang-undangan dan norma-norma yang berlaku.</p>
			
			<p>Kamu bertanggung jawab secara penuh untuk menjaga kerahasiaan ID, kata sandi (password) dan kode pribadi lainnya, untuk dan oleh karena itu kamu juga bertanggung jawab atas segala kegiatan yang dilakukan dari ID tersebut. Jika ID dan Password kamu diketahui oleh pihak lain baik secara sengaja maupun tidak dari dan oleh pihak manapun yang mengakibatkan kerugian dan kehilangan data maupun item permainan yang ada didalamnya, maka proses penyelesaiannya disesuaikan dengan peraturan perundang-undangan yang berlaku, dan membebasan pihak Pop Bloop dari segala gugatan,tuntutan, denda, ganti kerugian dari pihak manapun atas segala akibat-akibat hukum yang mungkin timbul.</p>
			
			<p>Pop Bloop memiliki hak penuh untuk mengubah, mengaudit dan menghapus semua isi di situs web Pop Bloop maupun kontent-konten di dalam game yang menurut pertimbangan melanggar aturan dalam Syarat dan Ketentuan Pengguna ini yang melanggar hukum dan norma sosial, termasuk namun tidak terbatas pada konten yang mendorong kekerasan, kebencian, ancaman  fisik, penggunaan obat terlarang, pelecehan, penghinaan, dan menyinggung suku, agama, ras, dan antar golongan atau melanggar norma-norma yang berlaku, konten yang berisi cara, instruksi, maupun metode melakukan tindakan melanggar hukum, terorisme, hacking, cracking, serta konten yang bersifat komersial, promosi dan iklan.</p>
			
			<p>Kamu wajib bertanggung jawab penuh atas perbuatan dan tingkah laku kepada pengguna/pemain lain, dan senantiasa menjaga tingkah laku yang sopan dan norma-norma sosial kepada para pemain lain.</p>
			
			<p>Kamu wajib mengambil tindakan pencegahan (preventif) dan menjaga kestabilan sistem operasi dan keamanan data komputer yang kamu gunakan agar tidak terjadi kerusakan maupun kerugian yang dapat saja disebabkan oleh virus atau komponen berbahaya lainnya, maupun hal-hal yang disebabkan dari pihak lain diluar Pop Bloop. Segala kerusakan dan kerugian yang mungkin timbul karena hal-hal tersebut merupakan diluar tanggungjawab Pop Bloop.</p>
			
			<h2>Kebijakan Privasi Atas Informasi Pribadi</h2>
			
			<p>Kamu mengetahui dan mengakui bahwa kami dapat mengetahui dan memproses data pribadi yang kamu berikan jika kamu mengakses/menggunakan layanan di situs web ini.
			Semua informasi dan data pribadi yang diberikan kepada Pop Bloop akan dijaga kerahasiaannya dan Pop Bloop tidak akan memberikan informasi tersebut kepada pihak lain kecuali jika diwajibkan oleh Undang-Undang.</p>
			<p>Kami dapat menjalin kerjasama dengan mitra usaha perusahaan periklanan dari pihak ketiga untuk mengiklankan produk atau layanan pada situs web kami. Perusahaan-perusahaan tersebut dapat memberlakukan cookies dan action tags untuk mengukur keefektifan pengiklanan.</p>
			
			<p>Kamu mungkin suatu ketika melihat tautan penghubung kepada situs lain di Pop Bloop, baik melalui iklan atau promosi pihak ketiga lainnya atau tautan yang dikirim oleh anggota Pop Bloop lainnya. Pop Bloop tidak bertanggungjawab terhadap praktek pengaturan privasi situs lainnya tersebut yang mungkin anda kunjungi melalui tautan penghubung yang anda temukan di Pop Bloop. Jika kamu memberikan informasi secara langsung kepada pihak lain tersebut selain Pop Bloop, mungkin kebijakan privasi yang berlaku berbeda dalam penggunaan data dan informasi pribadi oleh pihak-pihak tersebut.</p>
			
			<p>Pop Bloop dan pengguna, baik secara masing-masing dan bersama-sama akan mematuhi peraturan perundang-undangan perlindungan konsumen, Informasi dan Transaksi Elektronik, serta peraturan lainya mengenai pengunaan data dan informasi pribadi yang berlaku dalam melaksanakan hak dan kewajibannya.</p>
			
			<h2>Hak Atas Kekayaan Intelektual</h2>
			
			<p>Kamu tidak diijinkan mengirim, mendistribusikan, atau membuat salinan ulang dalam bentuk apa pun baik sebagian maupun seluruhnya dari semua materi yang memiliki merk dagang, hak intelektual dan paten atau materi yang merupakan hak milik/cipta yang mungkin ditemukan dalam situs web maupun game dan segala aksesorisnya pada Pop Bloop tanpa ijin tertulis dari Pop Bloop.
			Materi, Konten, Ide, Metode, Perangkat lunak (software), situs web dan fasilitas layanan lainnya yang disediakan oleh Pop Bloop adalah mutlak merupakan hak milik Pop Bloop sebagai pengembang/pengelola/ publisher dan mitra usahanya.<p>
			
			<p>Kami atau mitra atau pihak ketiga (yang telah memberikan kami ijin untuk menampilkan materi mereka sebagai konten di situs ini) memiliki semua Hak atas Kekayaan Intelektual dalam Konten pada situs web Pop Bloop ini. Jika kamu memerlukan software pihak ketiga untuk dapat mengakses dan menggunakan fasilitas dan layanan di situs web ini, kamu harus memperoleh lisensi software tersebut dengan biaya sendiri.</p>
			
			<h2>Pernyataan Atas Jaminan</h2>
			
			<p>Jasa, layanan, fasilitas yang diberikan tidaklah bebas dari kemungkinan kesalahan sistem seutuhnya. Kesalahan sistem bisa saja terjadi sewaktu-waktu baik itu karena termasuk namun tidak terbatas pada masalah koneksi internet, listrik, perangkat lunak, perangkat keras, force majeur maupun masalah dari program utama layanan itu sendiri.
			Kami tidak dapat menjamin bahwa situs web Pop Bloop ini akan selalu beroperasi sesuai dengan keinginan dan harapan kamu atau akan bebas dari kesalahan. Oleh karena itu Kami tidak memiliki kewajiban kepada pengguna untuk memperbaiki, memperbarui situs web ini, tetapi kita akan berusaha untuk dapat melakukannya dari waktu ke waktu dan kami berhak secara sepihak untuk mengubah, membatasi akses atau menutup situs ini kapan saja.</p>
			
			<p>Sesuai dengan Perkembangan dan Kebutuhannya Pop Bloop berhak secara sepihak untuk menyesuaikan, memperbaiki dan mengubah syarat dan ketentuan dalam hal penggunaan layanan ini sewaktu-waktu tanpa pemberitahuan terlebih dahulu. Oleh karena itu kamu wajib memeriksa perkembangan (update) yang terjadi dan disediakan oleh Pop Bloop untuk melihat dan mengerti isi dari syarat dan ketentuan penggunaan ini terutama jika ada perubahan.</p>
    </div>
    <div class="clear"></div>
		
		<div style="color: #fff; font-size: 11px; text-align: center; padding: 12px 33px;">Please make sure all required fields are filled out correctly before you click  "Create my account" button!</div>
		
    <div id="sign_up_btn" style="text-align:center; color:#fff; background:url('<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/bg/blue.btn.bg.png') repeat-x; font-size:16px; margin:5px 36px; padding:8px; cursor:pointer; border-radius: 4px; font-size:15px;">Create my account</div>
    
		
		<div style="height: 40px">&nbsp;</div>
		
  </div>
  <div class="grid_3">
    &nbsp;
  </div>
</div>


</div><!--[ end withjs ]-->
