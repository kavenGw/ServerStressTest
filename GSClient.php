<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class GSClient
{
    private $client;

    private $mPid;

    private $buffer = array();

    private $mData;

    private $token;

    public function __construct($pid) {

        echo "创建用户{$pid} ]\n";

        if ($pid < 10){
            $this->mPid = "000" . $pid;
        }elseif($pid < 100){
            $this->mPid = "00" . $pid;
        }elseif($pid < 1000){
            $this->mPid = "0" . $pid;
        }
        $this->mPid = "Test" . $this->mPid ;
        $this->mPid = "Test0004";

        $this->client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

        $this->client->on("connect", array($this, 'onConnect'));
        $this->client->on("receive", array($this, 'onReceive'));
        $this->client->on("error", array($this, 'onError'));
        $this->client->on("close", array($this, 'onClose'));

        $this->isGameServer = false;

	      $this->client->connect(IP, PORT , 1);

    }

    public function onConnect()
    {

        if ($this->isGameServer){
            echo "用户{$this->mPid} 连接游戏服务器成功\n";
            // $msg = num2UInt32Str(28) . num2UInt32Str(31) . num2UInt16Str(8) . "test0001" . num2UInt16Str(8) . "12345678";
            // $this->sendMessage($msg);
            $this->sendMessage(32,writeStr($this->token));
        }else{
            echo "用户{$this->mPid} 连接登陆服务器成功\n";
            // $msg = num2UInt32Str(28) . num2UInt32Str(31) . num2UInt16Str(8) . "test0001" . num2UInt16Str(8) . "12345678";
            $this->sendMessage(31,writeStr($this->mPid) . writeStr("12345678"));
        }
        //请求登陆
        // $data = array();
        // $data['pid'] = $this->mPid;
        // $data['pwd'] = $this->mPid;
        // $data['ver'] = "1.0.1";
        // $this->sendMessage(1001, $data);
        // $acc = "test000" . $this->mPid;
        // $this->sendMessage("\0\0\0\28\0\0\0\31\0\8test0001\0\00812345678");
    }

    public function onReceive($cli, $data)
    {

        $tmp = unpack('C*', $data);

        foreach($tmp as $key => $val)
        {
            $this->buffer[] = $val;
        }

        $this->handleBuffer($this->buffer);
    }

    public function handleBuffer(&$data)
    {
        echo "用户{$this->mPid} 收到消息总长度" . count($data) . "\n";
        $headLen = 4;
        if(count($data) > $headLen)
        {
            $len = readInt($data);
            if(count($data) >= $len - 4)
            {
                $this->onData(array_splice($data, 0,$len - 4));
                $this->handleBuffer($data);
            }
        }
    }

    public function onData(&$data)
    {
        $cmd = readInt($data);
        $this->dealCmd($cmd, $data);
    }

    public function dealCmd($cmdId,$data)
    {
        echo "用户{$this->mPid}收到消息{$cmdId}\n";
        if($cmdId == 2)
        {
            //登陆游戏
            $serverIp = readStr($data);
            $serverPort = readShort($data);
            echo "用户{$this->mPid} 连接游戏服务器{$serverIp} {$serverPort} \n";
            $this->token = readStr($data);
            var_dump($this->token);

            $this->isGameServer = true;
            $this->client->connect($serverIp,intval($serverPort));
        }elseif ($cmdId == 3){
            //创建角色
            $this->sendMessage(33,num2UInt16Str(2).writeStr($this->mPid));
        }elseif($cmdId == 5){
            //逻辑接口
            $resqId = readShort($data);
            echo "收到逻辑消息{$resqId}\n";
            if($resqId == 2){
                $result = readByte($data);
                echo "登陆游戏返回{$result}\n";
            }
        }
    }

    public function onError()
    {
        echo "用户{$this->mPid} 错误 {$this->client->errCode} \n";
    }

    public function onClose()
    {
        echo "用户{$this->mPid} 断开\n";
    }

    public function sendMessage($cmd,$msg)
    {
        $len = num2UInt32Str(strlen($msg)+8);//+cmd
        $data = $len . num2UInt32Str(intval($cmd)) . $msg;
        $this->client->send($data);

        echo "用户{$this->mPid} 发送消息{$cmd} {$data}成功\n";
    }
}

define("ROOT", __DIR__);
require_once(ROOT."/util.php");
require_once(ROOT."/GSConfig.php");

for($i = 4;$i<=4;$i++)
{
    $client = new GSClient($i + RoleStartIndex);
    unset($client);
}
