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
class Status extends PBEnum
{
  const INACTIVE  = 0;
  const ACTIVE  = 1;
  const DONE  = 2;
}
class PlayerQuest extends PBMessage
{
  var $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
  public function __construct($reader=null)
  {
    parent::__construct($reader);
    $this->fields["1"] = "PBInt";
    $this->values["1"] = "";
    $this->fields["2"] = "PBInt";
    $this->values["2"] = "";
    $this->fields["3"] = "Status";
    $this->values["3"] = "";
    $this->values["3"] = new Status();
    $this->values["3"]->value = Status::ACTIVE;
    $this->fields["4"] = "PBInt";
    $this->values["4"] = "";
  }
  function id()
  {
    return $this->_get_value("1");
  }
  function set_id($value)
  {
    return $this->_set_value("1", $value);
  }
  function quest()
  {
    return $this->_get_value("2");
  }
  function set_quest($value)
  {
    return $this->_set_value("2", $value);
  }
  function status()
  {
    return $this->_get_value("3");
  }
  function set_status($value)
  {
    return $this->_set_value("3", $value);
  }
  function currentActivity()
  {
    return $this->_get_value("4");
  }
  function set_currentActivity($value)
  {
    return $this->_set_value("4", $value);
  }
}
class QuestResult extends PBMessage
{
  var $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
  public function __construct($reader=null)
  {
    parent::__construct($reader);
    $this->fields["1"] = "PBString";
    $this->values["1"] = "";
    $this->fields["2"] = "PBString";
    $this->values["2"] = "";
  }
  function key()
  {
    return $this->_get_value("1");
  }
  function set_key($value)
  {
    return $this->_set_value("1", $value);
  }
  function val()
  {
    return $this->_get_value("2");
  }
  function set_val($value)
  {
    return $this->_set_value("2", $value);
  }
}
class PlayerQuestList extends PBMessage
{
  var $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
  public function __construct($reader=null)
  {
    parent::__construct($reader);
    $this->fields["1"] = "PlayerQuest";
    $this->values["1"] = array();
  }
  function playerQuests($offset)
  {
    return $this->_get_arr_value("1", $offset);
  }
  function add_playerQuests()
  {
    return $this->_add_arr_value("1");
  }
  function set_playerQuests($index, $value)
  {
    $this->_set_arr_value("1", $index, $value);
  }
  function remove_last_playerQuests()
  {
    $this->_remove_last_arr_value("1");
  }
  function playerQuests_size()
  {
    return $this->_get_arr_size("1");
  }
}
class ObjectiveType extends PBEnum
{
  const NPC  = 1;
  const AREA  = 2;
  const ITEM  = 2;
}
class Action extends PBEnum
{
  const DISPLAYDIALOG  = 1;
}
class Activity extends PBMessage
{
  var $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
  public function __construct($reader=null)
  {
    parent::__construct($reader);
    $this->fields["1"] = "PBInt";
    $this->values["1"] = "";
    $this->fields["2"] = "PBString";
    $this->values["2"] = "";
    $this->fields["3"] = "PBInt";
    $this->values["3"] = array();
    $this->fields["4"] = "QuestResult";
    $this->values["4"] = array();
    $this->fields["5"] = "PBInt";
    $this->values["5"] = "";
    $this->fields["6"] = "ObjectiveType";
    $this->values["6"] = "";
    $this->fields["7"] = "Action";
    $this->values["7"] = "";
    $this->fields["8"] = "DialogTree";
    $this->values["8"] = "";
  }
  function id()
  {
    return $this->_get_value("1");
  }
  function set_id($value)
  {
    return $this->_set_value("1", $value);
  }
  function activityName()
  {
    return $this->_get_value("2");
  }
  function set_activityName($value)
  {
    return $this->_set_value("2", $value);
  }
  function requirement($offset)
  {
    $v = $this->_get_arr_value("3", $offset);
    return $v->get_value();
  }
  function append_requirement($value)
  {
    $v = $this->_add_arr_value("3");
    $v->set_value($value);
  }
  function set_requirement($index, $value)
  {
    $v = new $this->fields["3"]();
    $v->set_value($value);
    $this->_set_arr_value("3", $index, $v);
  }
  function remove_last_requirement()
  {
    $this->_remove_last_arr_value("3");
  }
  function requirement_size()
  {
    return $this->_get_arr_size("3");
  }
  function result($offset)
  {
    return $this->_get_arr_value("4", $offset);
  }
  function add_result()
  {
    return $this->_add_arr_value("4");
  }
  function set_result($index, $value)
  {
    $this->_set_arr_value("4", $index, $value);
  }
  function remove_last_result()
  {
    $this->_remove_last_arr_value("4");
  }
  function result_size()
  {
    return $this->_get_arr_size("4");
  }
  function nextActivity()
  {
    return $this->_get_value("5");
  }
  function set_nextActivity($value)
  {
    return $this->_set_value("5", $value);
  }
  function objectiveType()
  {
    return $this->_get_value("6");
  }
  function set_objectiveType($value)
  {
    return $this->_set_value("6", $value);
  }
  function action()
  {
    return $this->_get_value("7");
  }
  function set_action($value)
  {
    return $this->_set_value("7", $value);
  }
  function dialog()
  {
    return $this->_get_value("8");
  }
  function set_dialog($value)
  {
    return $this->_set_value("8", $value);
  }
}
class Quest extends PBMessage
{
  var $wired_type = PBMessage::WIRED_LENGTH_DELIMITED;
  public function __construct($reader=null)
  {
    parent::__construct($reader);
    $this->fields["1"] = "PBInt";
    $this->values["1"] = "";
    $this->fields["2"] = "PBString";
    $this->values["2"] = "";
    $this->fields["3"] = "PBInt";
    $this->values["3"] = array();
    $this->fields["4"] = "PBInt";
    $this->values["4"] = "";
    $this->fields["5"] = "PBInt";
    $this->values["5"] = "";
    $this->fields["6"] = "PBString";
    $this->values["6"] = "";
    $this->fields["7"] = "Activity";
    $this->values["7"] = array();
  }
  function id()
  {
    return $this->_get_value("1");
  }
  function set_id($value)
  {
    return $this->_set_value("1", $value);
  }
  function name()
  {
    return $this->_get_value("2");
  }
  function set_name($value)
  {
    return $this->_set_value("2", $value);
  }
  function requirementQuest($offset)
  {
    $v = $this->_get_arr_value("3", $offset);
    return $v->get_value();
  }
  function append_requirementQuest($value)
  {
    $v = $this->_add_arr_value("3");
    $v->set_value($value);
  }
  function set_requirementQuest($index, $value)
  {
    $v = new $this->fields["3"]();
    $v->set_value($value);
    $this->_set_arr_value("3", $index, $v);
  }
  function remove_last_requirementQuest()
  {
    $this->_remove_last_arr_value("3");
  }
  function requirementQuest_size()
  {
    return $this->_get_arr_size("3");
  }
  function requirementDoku()
  {
    return $this->_get_value("4");
  }
  function set_requirementDoku($value)
  {
    return $this->_set_value("4", $value);
  }
  function requirementFame()
  {
    return $this->_get_value("5");
  }
  function set_requirementFame($value)
  {
    return $this->_set_value("5", $value);
  }
  function description()
  {
    return $this->_get_value("6");
  }
  function set_description($value)
  {
    return $this->_set_value("6", $value);
  }
  function activities($offset)
  {
    return $this->_get_arr_value("7", $offset);
  }
  function add_activities()
  {
    return $this->_add_arr_value("7");
  }
  function set_activities($index, $value)
  {
    $this->_set_arr_value("7", $index, $value);
  }
  function remove_last_activities()
  {
    $this->_remove_last_arr_value("7");
  }
  function activities_size()
  {
    return $this->_get_arr_size("7");
  }
}
?>