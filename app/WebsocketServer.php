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
            $this->onOpen($ws, $request);
        });
        
        $this->ws->on('message', function ($ws, Frame $frame) : void {
            $this->onMessage($ws, $frame);
        });
        
        $this->ws->start();
    }

    /**
     * Cliente conectado
     *
     * @param [type] $server
     * @param Request $request
     * @return void
     */
    private function onOpen($server, Request $request): void
    {
        echo "server: handshake success with fd {$request->fd}\n";
    }

    /**
     * @param $server
     * @param $frame
     */

    /**
     * Gerencia troca de mensagens entre o servidor
     *
     * @param [type] $server
     * @param Frame $frame
     * @return void
     */
    private function onMessage($server, Frame $frame): void
    {
        $decodedData = json_decode($frame->data);

        switch ($decodedData->requestType) {
            case 'init':
                // var_dump($decodedData);
                $this->initSession($decodedData, $server, $frame);
                $this->sendRooms($server, $frame);
                break;
            
            case 'message':
                // var_dump($decodedData);
                $this->sendRooms($server, $frame);

                // atualiza fd da sessao
                $session = new Session();
                $session->session = $decodedData->handshakeSession;
                $session->fd = $frame->fd;
                $session->type = $decodedData->user;
                $session->update();



                if ($decodedData->user == "guest") {
                    $room = new Room();
                    $room->create_by = $decodedData->handshakeSession;

                    // busca sala corrente
                    $result  = $room->find();
                    $room_id = $result[0]['id'];
                } else {
                    // suport manda msg com id da sala
                    $room_id = $decodedData->current_room;
                }

                // salva mensagem
                $msg = new Message();
                $msg->session   = $decodedData->handshakeSession;
                # TODO: remover requestType e handshakeSession da 'message'
                $msg->message   = $frame->data;
                $msg->rooms_id  = $room_id;
                $msg->save();

                // vincula sessao a sala
                $rooms_session = new RoomsSession();
                $rooms_session->session = $decodedData->handshakeSession;
                $rooms_session->rooms_id  = $room_id;

                if (!count($rooms_session->find())) {
                    $rooms_session->save();
                }


                ###################################################################
                // enviar msg para as sessoes da sala corrente
                $return_msg = [];
                $return_msg['requestType']  = "message";
                $return_msg['msg']          = $decodedData->message;
                $return_msg['current_room'] = $room_id;
                
                foreach ($rooms_session->buscaFdCorrente($room_id) as $result) {
                    if ($frame->fd != $result['fd']) {
                        $server->push($result['fd'], json_encode($return_msg));
                    }
                }
                break;
            case 'viewRoom':
                // var_dump($decodedData);
                // envia historico de msg se tiver
                $return_msg['requestType']      = "historyView";
                
                $history_result = (new Message())->buscaHistoricoById($decodedData->room_id);
                $history = [];

                foreach ($history_result as $key => $msg) {
                    $history[] = $msg["message"];
                }

                // var_dump($history);

                if (count($history)) {
                    $return_msg['history']      = $history;
                    $server->push($frame->fd, json_encode($return_msg));
                }
                break;
        }
    }


    /**
     * Atualiza salas para os atendentes
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

            $return_msg                 = [];
            $return_msg['requestType']  = "rooms";
            $return_msg['rooms']        = $rooms;

            foreach ($session->find() as $result) {
                $server->push($result['fd'], json_encode($return_msg));
            }
        }
    }


    /**
     * Gerencia inicializacao da sessao
     *
     * @param [type] $data
     * @param [type] $server
     * @param Frame $frame
     * @return void
     */
    private function initSession($data, $server, Frame $frame)
    {
        $session = new Session();
        $session->session = $data->handshakeSession;
        
        if (empty($session->session) || !count($session->find())) {
            $session->session = (new Random())->uuid();
            // cria uma nova sessao
            $session->fd = $frame->fd;
            $session->type = $data->user;
            $session->save();
        } else {
            // atualiza sessao sessao
            $session->fd = $frame->fd;
            $session->type = $data->user;
            $session->update();
        }
        
        // verifica se tem room
        // salva room
        if ($data->user == "guest") {
            $room = new Room();
            $room->create_by = $session->session;

            if (!count($room->find())) {
                $room->save();
            }
        }


        // confirma session
        $return_msg = [];
        $return_msg['requestType']      = "init";
        $return_msg['user']             = $data->user;
        $return_msg['handshakeSession'] = $session->session;

        $server->push($frame->fd, json_encode($return_msg));

        // envia historico de msg se tiver
        $return_msg['requestType']      = "history";
        
        
        $history_result = (new Message())->buscaHistorico($session->session);
        $history = [];

        foreach ($history_result as $key => $msg) {
            $history[] = $msg["message"];
        }
        if (count($history)) {
            $return_msg['history']      = $history;
            $server->push($frame->fd, json_encode($return_msg));
        }
    }
}

# TODO: avaliar sessoes fechadas após restart do servidor
