<?php

function debug($data){
    if(GSDebug){
        echo $data;
    }
}

function gslog($data){
    $time = date("m-d H:i:s");
    echo $time . " " . $data ;
}
