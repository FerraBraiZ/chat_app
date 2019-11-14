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
}
