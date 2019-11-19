<?php

namespace App\Models;

use App\Core\Model;

class Session extends Model
{

    protected $fillable = ['session', 'fd', 'type'];
    
    /**
     * Message constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    
    
    public function update(): array
    {
        $select = "
        UPDATE sessions 
        SET 
            fd = ?
        WHERE
            session = ?;
        ";

        $sql = $this->db->prepare($select);
        $sql->bindParam(1, $this->fd);
        $sql->bindParam(2, $this->session);
        $sql->execute();

        // $sql->debugDumpParams();

        return $sql->fetchAll();
    }
}
