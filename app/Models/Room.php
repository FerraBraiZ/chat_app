<?php

namespace App\Models;

use App\Core\Model;

class Room extends Model
{

    protected $fillable = ['id', 'create_by'];
    
    /**
     * Message constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }
}
