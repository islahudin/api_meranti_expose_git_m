<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class GuestEntry extends  Model
{

    protected $table = "tbl_guest_entry";

    protected $fillable = ["full_name", "email", "comment"];
}
