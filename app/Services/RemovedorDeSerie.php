<?php

namespace App\Services;

use App\{Episodio, Serie, Temporada};
use Illuminate\Support\Facades\DB;
use Storage;

class RemovedorDeSerie
{
    public function removerSerie(int $serieId): string
    {
        $nomeSerie = '';
        // o código será executado somente se todo o código poder ser executado, então se o BD sair do ar enquanto o código está sendo executado, não terá o problema de sobrar episódios ou temporadas faltando pra ser excluido. 
        DB::transaction(function () use ($serieId, &$nomeSerie) {
            $serie = Serie::find($serieId);
            $nomeSerie = $serie->nome;
            
            $this->removerTemporadas($serie);
            $serie->delete();

            if($serie->capa) {
                Storage::delete($serie->capa);
            }
        });

        return $nomeSerie;
    }

    // as etapas da exclusão de uma série, suas temporadas e episódios foram dividos em 3, porque as funções deve, ter no máximo um nível de indentação.

    /**
     * @param $serie
     */
    private function removerTemporadas(Serie $serie): void
    {
        $serie->temporadas->each(function (Temporada $temporada) {
            $this->removerEpisodios($temporada);
            $temporada->delete();
        });
    }

    /**
     * @param Temporada $temporada
     */
    private function removerEpisodios($temporada)
    {
        $temporada->episodios()->each(function (Episodio $episodio) {
            $episodio->delete();
        });
    }
}