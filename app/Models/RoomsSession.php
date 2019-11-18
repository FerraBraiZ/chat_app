<?php

namespace App\Models;

use App\Core\Model;

class RoomsSession extends Model
{

    protected $fillable = ['session', 'rooms_id'];
    
    /**
     * Message constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    
    /**
     * Retorna os Fd's da room (conexoes vinculadas)
     *
     * @param integer $room_id
     * @return array
     */
    public function buscaFdCorrente(int $room_id): array
    {
        $select = "
            SELECT
                S.fd
            FROM
                roomssessions RS
                    INNER JOIN
                sessions S ON RS.session = S.session
            WHERE
                RS.rooms_id = ?;";

        $sql = $this->db->prepare($select);
        $sql->bindParam(1, $room_id);
        $sql->execute();

        // $sql->debugDumpParams();

        return $sql->fetchAll();
    }
}
