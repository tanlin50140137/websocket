<?php
class WebsocketTest {
    public $server;
    public $request_id;
    public function __construct() {
        $this->server = new Swoole\WebSocket\Server("0.0.0.0", 9501);
        $this->server->on('open', function (swoole_websocket_server $server, $request) {
            //echo "server: handshake success with fd{$request->fd}\n";
            $this->request_id[] = $request->fd;
        });
        $this->server->on('message', function (Swoole\WebSocket\Server $server, $frame) {
            //echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";           
            foreach( $this->request_id as $v )
            {
            	if( $this->Server->isEstablished($v) )
            	{
            		$server->push($v, $frame->data);
            	}
            }
        });
        $this->server->on('close', function ($ser, $fd) {
            echo "client {$fd} closed\n";
        });
        $this->server->on('request', function ($request, $response) {
            // 接收http请求从get获取message参数的值，给用户推送
            // $this->server->connections 遍历所有websocket连接用户的fd，给所有用户推送
            foreach ($this->server->connections as $fd) {
                $this->server->push($fd, $request->get['message']);
            }
        });
        $this->server->start();
    }
}
new WebsocketTest();