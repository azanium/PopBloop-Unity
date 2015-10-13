<?php
require_once('../message/pb_message.php');

require_once('./pb_proto_Quest.php');

$dialogOption01 = new DialogOption();
$dialogOption01->set_nextDialog(2);
$dialogOption01->set_text('Kambing');

$dialogOption02 = new DialogOption();
$dialogOption02->set_nextDialog(3);
$dialogOption02->set_text('Ayam');

$dialogOption03 = new DialogOption();
$dialogOption03->set_nextDialog(0);
$dialogOption03->set_text('OK. Guling2 aja semau lo...');

$dialogOption04 = new DialogOption();
$dialogOption04->set_nextDialog(0);
$dialogOption04->set_text('OK deh, ayam jg ga apa2...');

$dialogData01 = new DialogData();
$dialogData01->set_id(1);
$dialogData01->set_tipe(DIALOG);
$dialogData01->set_text("Anda siapa?");
$dialogData01->set_opt(0, $dialogOption01);
$dialogData01->set_opt(1, $dialogOption02);

$dialogData02 = new DialogData();
$dialogData02->set_id(2);
$dialogData02->set_tipe(DIALOG);
$dialogData02->set_text("Kambing Guling!!!");
$dialogData02->set_opt(0, $dialogOption03);

$dialogData03 = new DialogData();
$dialogData03->set_id(3);
$dialogData03->set_tipe(DIALOG);
$dialogData03->set_text("Ayam kampu** ?");
$dialogData03->set_opt(0, $dialogOption04);

$dialogTree01 = new DialogTree();
$dialogTree01->set_dialogs(0, $dialogData01);
$dialogTree01->set_dialogs(1, $dialogData02);
$dialogTree01->set_dialogs(2, $dialogData03);
$dialogTree01->set_currentDialog(1);

/////////////////////////////////////////////////////////////////////////////////////////
$dialogOption05 = new DialogOption();
$dialogOption05->set_text("Soto");
$dialogOption05->set_nextDialog(5);

$dialogOption06 = new DialogOption();
$dialogOption06->set_text("Sate");
$dialogOption06->set_nextDialog(6);

$dialogOption07 = new DialogOption();
$dialogOption07->set_text("Ga mau makan, udah kenyang");
$dialogOption07->set_nextDialog(0);

$dialogOption08 = new DialogOption();
$dialogOption08->set_text("Betawi");
$dialogOption08->set_nextDialog(7);

$dialogOption09 = new DialogOption();
$dialogOption09->set_text("Madura");
$dialogOption09->set_nextDialog(7);

$dialogOption10 = new DialogOption();
$dialogOption10->set_text("Ga jadi soto. Ganti menu!");
$dialogOption10->set_nextDialog(4);

$dialogOption11 = new DialogOption();
$dialogOption11->set_text("Kambing");
$dialogOption11->set_nextDialog(8);

$dialogOption12 = new DialogOption();
$dialogOption12->set_text("Ayam");
$dialogOption12->set_nextDialog(8);

$dialogOption13 = new DialogOption();
$dialogOption13->set_text("Sapi");
$dialogOption13->set_nextDialog(8);

$dialogOption14 = new DialogOption();
$dialogOption14->set_text("Ga jadi sate. Ganti menu!");
$dialogOption14->set_nextDialog(4);

$dialogOption15 = new DialogOption();
$dialogOption15->set_text("OK");
$dialogOption15->set_nextDialog(0);

$dialogOption16 = new DialogOption();
$dialogOption16->set_text("OK");
$dialogOption16->set_nextDialog(0);


$dialogData04 = new DialogData();
$dialogData04->set_id(4);
$dialogData04->set_tipe(0);
$dialogData04->set_text("Selamat Datang di Dialog Tree Test 001. Makanan apa yang Anda pilih?");
$dialogData04->set_opt(0, $dialogOption05);
$dialogData04->set_opt(1, $dialogOption06);
$dialogData04->set_opt(2, $dialogOption07);


$dialogData05 = new DialogData();
$dialogData05->set_id(5);
$dialogData05->set_tipe(0);
$dialogData05->set_text("Anda memilih Soto. Soto dari daerah mana yang Anda sukai?");
$dialogData05->set_opt(0, $dialogOption08);
$dialogData05->set_opt(1, $dialogOption09);
$dialogData05->set_opt(2, $dialogOption10);

$dialogData06 = new DialogData();
$dialogData06->set_id(6);
$dialogData06->set_tipe(0);
$dialogData06->set_text("Anda memilih Sate. Sate apa yang Anda sukai?");
$dialogData06->set_opt(0, $dialogOption11);
$dialogData06->set_opt(1, $dialogOption12);
$dialogData06->set_opt(2, $dialogOption13);
$dialogData06->set_opt(3, $dialogOption14);

$dialogData07 = new DialogData();
$dialogData07->set_id(7);
$dialogData07->set_tipe(0);
$dialogData07->set_text("Soto Anda telah terhidang. Selamat menikmati.");
$dialogData07->set_opt(0, $dialogOption15);

$dialogData08 = new DialogData();
$dialogData08->set_id(8);
$dialogData08->set_tipe(0);
$dialogData08->set_text("Sate Anda telah terhidang. Selamat menikmati.");
$dialogData08->set_opt(0, $dialogOption16);


$dialogTree02 = new DialogTree();
//$dialogTree02->set_dialogs(array($dialogData04, $dialogData05, $dialogData06, $dialogData07, $dialogData08));
$dialogTree02->set_dialogs(0, $dialogData04);
$dialogTree02->set_dialogs(1, $dialogData05);
$dialogTree02->set_dialogs(2, $dialogData06);
$dialogTree02->set_dialogs(3, $dialogData07);
$dialogTree02->set_dialogs(4, $dialogData08);
$dialogTree02->set_currentDialog(0);


// QUEST





$questResult01 = new QuestResult();
$questResult01->set_key('item');
$questResult01->set_val('0');

$questResult02 = new QuestResult();
$questResult02->set_key('doku');
$questResult02->set_val('300');

$Activity01 = new Activity();
$Activity01->set_id(1);
$Activity01->set_activityName("Test Psikologi Calon Pendekar");
//$Activity01->set_requirement(null);
$Activity01->set_result(0, $QuestResult01);
$Activity01->set_result(1, $QuestResult02);
$Activity01->set_nextActivity(2);
$Activity01->set_dialog($dialogTree01);
$Activity01->set_objectiveType(1);
$Activity01->set_action(1);


$QuestResult03 = new QuestResult;
$QuestResult03->set_key("fame_point");
$QuestResult03->set_val("20");

$QuestResult04 = new QuestResult;
$QuestResult04->set_key("doku");
$QuestResult04->set_val("100");

$Activity02 = new Activity();
$Activity02->set_id(2);
$Activity02->set_activityName("Mencari Makanan Favorit Para Pendekar");
//$Activity02->set_requirement(null);
$Activity02->set_result(0, $QuestResult03);
$Activity02->set_result(1, $QuestResult04);
$Activity02->set_nextActivity(0);
$Activity02->set_dialog($dialogTree02);
$Activity02->set_objectiveType(1);
$Activity02->set_action(1);


$Quest01 = new Quest();
$Quest01->set_id(1);
$Quest01->set_name("Petualangan Sang Pendekar");
//$Quest01->set_requirementQuest(null);
$Quest01->set_requirementDoku(0);
$Quest01->set_requirementFame(0);
$Quest01->set_description("Petualangan menjelajahi dunia LILO");

$Quest01->set_activities(0, $Activity01);
$Quest01->set_activities(1, $Activity02);

$playerQuest01 = new PlayerQuest();
$playerQuest01->set_id(18);
$playerQuest01->set_quest(1);
$playerQuest01->set_status(ACTIVE);
$playerQuest01->set_currentActivity(1);

$playerQuestList01 = new PlayerQuestList();
$playerQuestList01->set_playerQuests(0, $playerQuest01);


$serialized_string = $playerQuestList01->SerializeToString();
$text = print_r($serialized_string, true);

print($text);
?>