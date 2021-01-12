<?php
/**
 * Created by PhpStorm.
 * User: weili
 * Date: 2021-01-21
 * Time: 18:10
 */

$server = new Swoole\Server('0.0.0.0', 9180);
$msg_key = ftok(__DIR__, 'a');

$server->set(array('task_worker_num' => 4,'message_queue_key' => $msg_key, 'task_ipc_mode'=>2));
$server->on('receive', function ($server, $fd, $reactor_id, $data) {
    $data = str_repeat('a', 10 * 1024 * 1024);
    $server->task($data);
    $server->send($fd, 'ok');
});



$server->on('task', function ($server, $task_id, $reactor_id, $data){
    sleep(5);
    echo $data;
    echo '111';
});

$server->start();