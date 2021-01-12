<?php

require_once "db.php";
class SwooleTaskServer
{
    protected $server;
    protected $config = [
        'worker_num' => 4,
        'task_worker_num' => 4,
        'task_ipc_mode' => 3,
        'message_queue_key' => ''
    ];
    protected $tableId;
    protected $db;
    protected $lock;
    public function __construct()
    {
        $this->server = new Swoole\WebSocket\Server('0.0.0.0', 9170);
        $this->lock = new Swoole\Lock(SWOOLE_MUTEX);
        $this->initEvent();
        $this->setConf();
        $this->setIdTable();
        $this->server->start();

    }

    public function open($server, $request)
    {
        $http_id = $request->get['http_id'];
        $this->tableId->set($http_id, ['fd' => $request->fd]);
    }
    public function message($server, $frame)
    {
        $data = json_decode($frame->data, true);
        //1.解析
        $fd = $this->tableId->get($data['http_id'])['fd'];
        $re['fid'] = $fd;
        $re['http_id'] = $data['http_id'];
        if ($data['method'] == 'sendSms') {
            $db = new Db();
            //2。获取任务总量
            $count = $db->query("select count(*) as size from mobile");
            $re['total'] = $total = (int)$count[0]['size'];
            //3。分块
            $re['avg'] = $total / $this->config['task_worker_num'];
            $re['offset'] = 0;
            //4。记录
            $this->tableId->set('smsOffset', ['fd' => 0]);
            $this->tableId->set('smsTotal', ['fd' => $total]);
            for ($i = 0; $i < $this->config['task_worker_num']; $i++) {
                //5。下发任务
                $server->task($re);
                $re['offset'] = $re['offset'] + $re['avg'];
            }
            $server->push($fd, json_encode(['msg' => "处理中"]));
        }
    }
    public function request($request, $response)
    {

    }
    public function task($server, $task_id, $work_id, $data)
    {
        $offset = $data['offset'];
        $db = new Db();
        $mobile = $db->query("select mobile from mobile limit ".$offset.",".$data['avg']);

        foreach ($mobile as $num) {
            $num;
        }

        $this->lock->lock();
        $offset = $this->tableId->get('smsOffset')['fd'];
        $sendSize = $offset+$data['avg'];
        echo "处理数据：".$sendSize."\n";
        $this->tableId->set('smsOffset', ['fd' => $sendSize]);
        $this->lock->unlock();
        $server->finish($data);
    }
    public function finish($server, $task_id, $data)
    {
        $offset = (int)$this->tableId->get('smsOffset')['fd'];
        $total = (int)$this->tableId->get('smsTotal')['fd'];

        if ($offset >= $total && $offset!=0) {
            $this->tableId->del('smsOffset');
            $this->tableId->del('smsTotal');
            echo "任务完成";
            $fd = $this->tableId->get($data['http_id'])['fd'];
            $server->push($fd, json_encode(['msg' => "任务完成"]));
        }
    }

    public function close($server, $fd)
    {

    }


    protected function setIdTable()
    {
        $this->tableId = new Swoole\Table(1 * 1024 * 1024);
        $this->tableId->column('fd', Swoole\Table::TYPE_INT);
        $this->tableId->create();

    }
    protected function initEvent(){
        $this->server->on('open', [$this, 'open']);
        $this->server->on('message', [$this, 'message']);
        $this->server->on('request', [$this, 'request']);
        $this->server->on('task', [$this, 'task']);
        $this->server->on('finish', [$this, 'finish']);
        $this->server->on('close', [$this, 'close']);
    }

    protected function setConf(){
        $msg_key = ftok(__DIR__, 'a');
        $this->config['message_queue_key'] = $msg_key;
        $this->server->set($this->config);
    }


}

new SwooleTaskServer();