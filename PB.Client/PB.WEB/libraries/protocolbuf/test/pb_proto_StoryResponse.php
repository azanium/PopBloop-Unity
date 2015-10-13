<?php
class StoryResponse_ResponseType extends PBEnum
{
  const SUCCESS  = 0;
  const FAILED  = 1;
}
class StoryResponse extends PBMessage
{
  var $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
  public function __construct($reader=null)
  {
    parent::__construct($reader);
    $this->fields["1"] = "StoryResponse_ResponseType";
    $this->values["1"] = "";
    $this->fields["2"] = "PBString";
    $this->values["2"] = "";
  }
  function status()
  {
    return $this->_get_value("1");
  }
  function set_status($value)
  {
    return $this->_set_value("1", $value);
  }
  function story()
  {
    return $this->_get_value("2");
  }
  function set_story($value)
  {
    return $this->_set_value("2", $value);
  }
}
?>