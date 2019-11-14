<?php

namespace App;

use App\Core\Db;
use App\Models\Message;
use App\Models\Session;
use App\Models\Room;
use Swoole\Http\Request;
use Phalcon\Security\Random;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * Class WebsocketServer
 * @package App
 */
class WebsocketServer
{
    /**
     * @var Server
     */
    private $ws;

    /**
     * @var Db
     */
    private $db;

    /**
     * WebsocketServer constructor.
     */
    public function __construct()
    {
        
        // Iniciando conexao com o banco
        $this->db = new Db();

        // Iniciando servidor websocket
        $this->ws = new Server("127.0.0.1", 9503);
        
        $this->ws->on('open', function ($ws, Request $request) : void {
            $this->onOpen($request);
        });
        
        $this->ws->on('message', function ($ws, Frame $frame) : void {
            $this->onMessage($ws, $frame);
            $this->sendRooms($ws, $frame);
        });
        
        $this->ws->start();
    }

    /**
     * Client connected
     * @param $server
     * @param Request $request
     */
    private function onOpen(Request $request): void
    {
        echo "server: handshake success with fd {$request->fd}\n";
    }

    /**
     * @param $server
     * @param $frame
     */
    private function onMessage($server, Frame $frame): void
    {
        $data = json_decode($frame->data);

        if (empty($data->handshake_session)) {
            $session = new Session();
            $session->session = $data->session;
            
            if (empty($data->session) || !count($session->find())) {
                $data->session = (new Random())->uuid();
                $session->session = $data->session;
            }
            
            ###########################################################################
            # SALVAR SESSAO
            $session->fd = $frame->fd;
            $session->type = $data->user;
            $session->save();
            ###########################################################################
            # VERIFICAR SE TEM ROMM
            # SALVAR ROOM

            $room = new Room();
            $room->create_by = $data->session;

            if (!count($room->find())) {
                $room->save();
            }
            ###########################################################################

            $start_fd = 0;
            while (true) {
                $conn_list = $server->connection_list($start_fd, 10);

                
                if ($conn_list === false or count($conn_list) === 0) {
                    break;
                }

                $start_fd = end($conn_list);

                foreach ($conn_list as $fd) {
                    $return_msg = [];
                    $return_msg['msg']      = $data->message;
                    
                    if ($frame->fd != $fd) {
                        $server->push($fd, json_encode($return_msg));
                    } else {
                        $return_msg['session']  = $data->session;
                        $server->push($fd, json_encode($return_msg));
                    }
                }
            }
            ###########################################################################
            # SALVAR MSG's
            if ($data->user == "guest") {
                $msg = new Message();
                $msg->session = $data->session;
                $msg->message = $frame->data;
                $msg->save();
            }
            ###########################################################################
        }
    }
    /**
     * Atualiza sala para os atendentes
     *
     * @return void
     */
    /**
     * @param $server
     * @param $frame
     */
    private function sendRooms($server, Frame $frame): void
    {
    }
}
