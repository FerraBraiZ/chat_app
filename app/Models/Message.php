<?php

namespace App\Models;

use App\Core\Model;

class Message extends Model
{

    protected $fillable = ['id', 'message', 'session', 'rooms_id'];
    
    /**
     * Message constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function buscaHistorico(string $session): array
    {
        $select = "
            SELECT 
                M.*
            FROM
                roomssessions RS
                    INNER JOIN
                messages M ON RS.rooms_id = M.rooms_id
            WHERE
                RS.session = ?
            ORDER BY M.id ASC;";

        $sql = $this->db->prepare($select);
        $sql->bindParam(1, $session);
        $sql->execute();

        // $sql->debugDumpParams();

        return $sql->fetchAll();
    }
}
