<?php
class Template {
	public function render($script){
		ob_start();
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