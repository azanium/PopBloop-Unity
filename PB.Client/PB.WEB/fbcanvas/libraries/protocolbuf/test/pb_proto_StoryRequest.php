<?php
class StoryRequest extends PBMessage
{
  var $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
  public function __construct($reader=null)
  {
    parent::__construct($reader);
    $this->fields["1"] = "PBString";
    $this->values["1"] = "";
    $this->fields["2"] = "PBString";
    $this->values["2"] = "";
    $this->fields["3"] = "PBString";
    $this->values["3"] = "";
  }
  function character1()
  {
    return $this->_get_value("1");
  }
  function set_character1($value)
  {
    return $this->_set_value("1", $value);
  }
  function character2()
  {
    return $this->_get_value("2");
  }
  function set_character2($value)
  {
    return $this->_set_value("2", $value);
  }
  function secret_weapon()
  {
    return $this->_get_value("3");
  }
  function set_secret_weapon($value)
  {
    return $this->_set_value("3", $value);
  }
}
?>