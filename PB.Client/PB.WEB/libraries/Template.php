<?php
class Template {
	public function render($script){
		ob_start();
		
		$role = $_SESSION['url_role'];

//		if($role == 'admin'){
//			define("LILO_THEME_ACTIVE", '');
//		}
		
		// di variable $script, ubah '/templates/' menjadi '/templates_' . LILO_THEME_ACTIVE . '/'
		if(trim(LILO_THEME_ACTIVE) != '' && strtolower(trim(LILO_THEME_ACTIVE)) != 'default' && $role != 'admin'){
			$script_ = str_replace("/templates/", "/templates_" . LILO_THEME_ACTIVE . '/', $script);
			
			if(isset($_SESSION['brand_page_id'])){	// dipanggil dari brand's page tab
				$script__ = str_replace("/templates/", "/templates_fb/", $script);
				
				if(file_exists($script__)){
					$script_ = $script__;
				}
			}
			
			if(file_exists($script_)){
				$script = $script_;
			}
		}

		$this->_include($script);
		return ob_get_clean();
	}
	
	public function __get($key){
		return (isset($this -> $key) ? $this -> $key : null);
	}
	
	protected function _include(){
		include func_get_arg(0);
	}
}
?>