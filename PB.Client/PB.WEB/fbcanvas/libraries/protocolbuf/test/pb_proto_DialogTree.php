<?php
class DialogOption extends PBMessage
{
  var $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
  public function __construct($reader=null)
  {
    parent::__construct($reader);
    $this->fields["1"] = "PBInt";
    $this->values["1"] = "";
    $this->fields["2"] = "PBString";
    $this->values["2"] = "";
  }
  function nextDialog()
  {
    return $this->_get_value("1");
  }
  function set_nextDialog($value)
  {
    return $this->_set_value("1", $value);
  }
  function text()
  {
    return $this->_get_value("2");
  }
  function set_text($value)
  {
    return $this->_set_value("2", $value);
  }
}
class DialogData_Tipe extends PBEnum
{
  const DIALOG  = 0;
}
class DialogData extends PBMessage
{
  var $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
  public function __construct($reader=null)
  {
    parent::__construct($reader);
    $this->fields["1"] = "PBInt";
    $this->values["1"] = "";
    $this->fields["2"] = "DialogData_Tipe";
    $this->values["2"] = "";
    $this->fields["3"] = "PBString";
    $this->values["3"] = "";
    $this->fields["4"] = "DialogOption";
    $this->values["4"] = array();
  }
  function id()
  {
    return $this->_get_value("1");
  }
  function set_id($value)
  {
    return $this->_set_value("1", $value);
  }
  function tipe()
  {
    return $this->_get_value("2");
  }
  function set_tipe($value)
  {
    return $this->_set_value("2", $value);
  }
  function text()
  {
    return $this->_get_value("3");
  }
  function set_text($value)
  {
    return $this->_set_value("3", $value);
  }
  function opt($offset)
  {
    return $this->_get_arr_value("4", $offset);
  }
  function add_opt()
  {
    return $this->_add_arr_value("4");
  }
  function set_opt($index, $value)
  {
    $this->_set_arr_value("4", $index, $value);
  }
  function remove_last_opt()
  {
    $this->_remove_last_arr_value("4");
  }
  function opt_size()
  {
    return $this->_get_arr_size("4");
  }
}
class DialogTree extends PBMessage
{
  var $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
  public function __construct($reader=null)
  {
    parent::__construct($reader);
    $this->fields["1"] = "DialogData";
    $this->values["1"] = array();
    $this->fields["2"] = "PBInt";
    $this->values["2"] = "";
  }
  function dialogs($offset)
  {
    return $this->_get_arr_value("1", $offset);
  }
  function add_dialogs()
  {
    return $this->_add_arr_value("1");
  }
  function set_dialogs($index, $value)
  {
    $this->_set_arr_value("1", $index, $value);
  }
  function remove_last_dialogs()
  {
    $this->_remove_last_arr_value("1");
  }
  function dialogs_size()
  {
    return $this->_get_arr_size("1");
  }
  function currentDialog()
  {
    return $this->_get_value("2");
  }
  function set_currentDialog($value)
  {
    return $this->_set_value("2", $value);
  }
}
?>