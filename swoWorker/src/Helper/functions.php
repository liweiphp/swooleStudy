<?php
/**
 * Created by PhpStorm.
 * User: weili
 * Date: 2021-01-23
 * Time: 15:08
 */
function p($message, $description = null, bool $type = NULL)
{
    echo "=======> ".$description." start<=====\n";
    if ($type){
        var_dump($message);
    } elseif (\is_array($message)) {
        echo \var_export($message, true);
    } else if (\is_string($message)) {
        echo $message."\n";
    } else {
        var_dump($message);
    }
    echo "=======> ".$description." end <=======\n";
}