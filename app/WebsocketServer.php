<?php

namespace App;

use App\Core\Db;
use App\Models\Message;
use App\Models\Session;
use App\Models\Room;
use App\Models\RoomsSession;
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
            
            ###################################################################
            // salva sessao
            $session->fd = $frame->fd;
            $session->type = $data->user;
            $session->save();
            ###################################################################
            // verifica se tem room
            // salva room
            if ($data->user == "guest") {
                $room = new Room();
                $room->create_by = $data->session;

                if (!count($room->find())) {
                    $room->save();
                }
            }
            ###################################################################
            // retorna msg para a session corrente
            $return_msg = [];
            $return_msg['msg']      = $data->message;
            $return_msg['session']  = $data->session;
            $server->push($frame->fd, json_encode($return_msg));

            ###################################################################
            // salva mensagens
            if ($data->user == "guest") {
                // busca room corrente
                $result  = $room->find();
                $room_id = $result[0]['id'];
            } else {
                // suport manda msg com id da room
                # TODO: dependencia de interface
                $room_id = 1;
            }

            $msg = new Message();
            $msg->session   = $data->session;
            $msg->message   = $frame->data;
            $msg->rooms_id  = $room_id;
            $msg->save();


            // vincula session a room
            $rooms_session = new RoomsSession();
            $rooms_session->session = $data->session;
            $rooms_session->rooms_id  = $room_id;

            if (!count($rooms_session->find())) {
                $rooms_session->save();
            }

            // naum enviar a session corrente para todos
            unset($return_msg['session']);

            // enviar msg para as session da room corrente
            foreach ($rooms_session->buscaFdCorrente(1) as $result) {
                if ($frame->fd != $result['fd']) {
                    $server->push($result['fd'], json_encode($return_msg));
                }
            }
            ###################################################################
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
        // busca sessions de suport
        $session = new Session();
        $session->type = 'suport';
        $sessions = $session->find();

        if (count($sessions)) {
            $rooms = [];
            foreach ((new Room())->find() as $room) {
                unset($room[0], $room[1]);
                $rooms[] = $room;
            }

            $return_msg = [];
            $return_msg['rooms'] = $rooms;
            
            foreach ($session->find() as $result) {
                $server->push($result['fd'], json_encode($return_msg));
            }
        }
    }
}
