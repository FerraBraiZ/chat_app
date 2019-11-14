<?php

namespace App\Models;

use App\Core\Model;

class Message extends Model
{

    protected $fillable = ['id', 'message', 'session'];
    
    /**
     * Message constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }
}
