<?php
define("DIALOG", 1);

require_once('libraries/protocolbuf/message/pb_message.php');

require_once('modules/002_asset_management/protocol_buffer/pb_proto_DialogTree.php');

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
$dialogData01->set_DialogType(DIALOG);
$dialogData01->set_text("Anda siapa?");
$dialogData01->set_opt(0, $dialogOption01);
$dialogData01->set_opt(1, $dialogOption02);

$dialogData02 = new DialogData();
$dialogData02->set_id(2);
$dialogData02->set_DialogType(DIALOG);
$dialogData02->set_text("Kambing Guling!!!");
$dialogData02->set_opt(0, $dialogOption03);

$dialogData03 = new DialogData();
$dialogData03->set_id(3);
$dialogData03->set_DialogType(DIALOG);
$dialogData03->set_text("Ayam kampu** ?");
$dialogData03->set_opt(0, $dialogOption04);

$dialogTree01 = new DialogTree();
$dialogTree01->set_dialogs(0, $dialogData01);
$dialogTree01->set_dialogs(1, $dialogData02);
$dialogTree01->set_dialogs(2, $dialogData03);
$dialogTree01->set_currentDialog(1);

$serialized_string = $dialogTree01->SerializeToString();
$text = print_r($serialized_string, true);

print($text);

/*
$filename = 'test.txt';
$somecontent = $text;

if (is_writable($filename)) {
    if (!$handle = fopen($filename, 'a')) {
         echo "Cannot open file ($filename)";
         exit;
    }

    if (fwrite($handle, $somecontent) === FALSE) {
        echo "Cannot write to file ($filename)";
        exit;
    }

    echo "Success, wrote ($somecontent) to file ($filename)";

    fclose($handle);

} else {
    echo "The file $filename is not writable";
}
*/
?>