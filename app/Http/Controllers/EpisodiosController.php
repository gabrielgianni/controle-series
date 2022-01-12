<?php

namespace App\Http\Controllers;

use App\Episodio;
use App\Temporada;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class EpisodiosController extends Controller
{
    public function index(Temporada $temporada, Request $request)
    {
        $episodios = $temporada->episodios;
        $temporadaId = $temporada->id;
        $mensagem = $request->session()->get('mensagem');

        return view('episodios.index', compact('episodios', 'temporadaId', 'mensagem'));
        // Caso não queiramos usar compact()
        // return view('episodios.index', [
        //     'episodios' => $temporada->episodios,
        //     'temporadaId' => $temporadaId = $temporada->id
        // ]);
    }

    public function assistir(Temporada $temporada, Request $request)
    {
        $episodiosAssistidos = $request->episodios; // array com id de eps. assistidos
        $temporada->episodios->each(function (Episodio $episodio) use ($episodiosAssistidos) {
            $episodio->assistido = in_array($episodio->id, $episodiosAssistidos);
        });
        $temporada->push();

        $request->session()->flash('mensagem', 'Episódios marcados como assistidos.');

        return redirect()->back();
    }
}
