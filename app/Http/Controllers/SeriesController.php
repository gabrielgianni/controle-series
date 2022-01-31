<?php

namespace App\Http\Controllers;

use App\Episodio;
use App\Events\NovaSerie as EventsNovaSerie;
use App\Http\Requests\SeriesFormRequest;
use App\Mail\NovaSerie;
use App\Serie;
use App\Services\CriadorDeSerie;
use App\Services\RemovedorDeSerie;
use App\Temporada;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SeriesController extends Controller
{   
    public function index(Request $request) {
        $series = Serie::query()->orderBy('nome')->get();
        $mensagem = $request->session()->get('mensagem');
        $mensagem = $request->session()->remove('mensagem');

        return view('series.index', compact('series', 'mensagem'));
    }

    public function create()
    {
        return view('series.create');
    }

    public function store(SeriesFormRequest $request, CriadorDeSerie $criadorDeSerie)
    {
        $capa = null;
        if($request->hasFile('capa')) {
            $capa = $request->file('capa')->store('serie');
        }

        $serie = $criadorDeSerie->criarSerie(
            $request->nome,
            $request->qtd_temporadas,
            $request->ep_por_temporada,
            $capa
        );

        $eventoNovaSerie = new EventsNovaSerie($request->nome, $request->qtd_temporadas, $request->ep_por_temporada);

        event($eventoNovaSerie);

        $request->session()->flash('mensagem', "Série {$serie->id} e suas temporadas e episódios adicionados com sucesso {$serie->nome}");

        // return redirect('/series');
        return redirect()->route('listar_series');
    }

    public function destroy(Request $request, RemovedorDeSerie $removedorDeSerie)
    {
        $nomeSerie = $removedorDeSerie->removerSerie($request->id);
        $request->session()->flash('mensagem', "Série $nomeSerie removida com sucesso!");

        return redirect()->route('listar_series');
    }

    public function editaNome(int $id, Request $request)
    {
        $novoNome = $request->nome;
        $serie = Serie::find($id);
        $serie->nome = $novoNome;
        $serie->save();
    }

    public function redirect()
    {
        return redirect()->route('listar_series');
    }
}
