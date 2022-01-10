<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    // protected $table = 'series'; // não precisa disso pois o laravel reconhece a table pelo nome da class, pois ele pega o nome da classe Serie e coloca como minúsculo com s no final, sendo então 'series'.
    public $timestamps = false;

    public $fillable = ['nome'];

    public function temporadas()
    {
        return $this->hasMany(Temporada::class);
    }
}
