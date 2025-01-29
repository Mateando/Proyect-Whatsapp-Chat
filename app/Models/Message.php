<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'body',
        'is_read',
        'user_id',
        'chat_id'
    ];
    
    //Relacion uno a muchos inversa
    public function user(){
        return $this->belongsTo(User::class);
    }

    //Relacion uno a muchos inversa
    public function chat(){
        return $this->belongsTo(Chat::class);
    }
}
