<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Storage;

class Serie extends Model
{
    // protected $table = 'series'; // não precisa disso pois o laravel reconhece a table pelo nome da class, pois ele pega o nome da classe Serie e coloca como minúsculo com s no final, sendo então 'series'.
    public $timestamps = false;

    public $fillable = ['nome', 'capa'];

    public function getCapaUrlAttribute()
    {
        if($this->capa) {
            return Storage::url($this->capa);
        }
        
        return Storage::url('serie/sem-imagem.jpg');
    }

    public function temporadas()
    {
        return $this->hasMany(Temporada::class);
    }
}
