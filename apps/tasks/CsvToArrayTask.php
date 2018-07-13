<?php

$master_record_list = array(
    0 => array(
        'chara_id' => 1,
        'name' => 'キャラ2',
    ),
    1 => array(
        'chara_id' => 2,
        'name' => 'キャラ3',
    ),
);

$string = 'array(' . PHP_EOL;
foreach($master_record_list as $index => $record){
    $string .= $record['chara_id'] . ' => array(' . PHP_EOL;
    foreach($record as $key => $val){
        $string .= '"' . $key . '" => ' . '"' . $val . '"' . ',' . PHP_EOL;
    }
    $string .= '),' . PHP_EOL;
}
$string .= ');';

echo $string;exit;