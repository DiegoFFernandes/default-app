<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AgendaEnvio extends Model
{
    use HasFactory;
    protected $table = 'AGENDAPESSOA';
    protected $connection;

    public function __construct()
    {
        $this->connection = 'Sempre setar o banco firebird com SetConnet';
    }

    public function setConnet()
    {
        return $this->connection = Auth::user()->conexao;
    }
    public function searchSend($request)
    {
        //return $request->cd_number;
        $query = "select ae.nr_contexto,
                    ce.ds_contexto,
                    ae.ds_assunto,
                    ae.ds_mensagem,                     
                    ae.nr_agenda, 
                    ae.nr_envio, 
                    ae.cd_pessoa, 
                    ae.cd_pessoa||'-'||p.nm_pessoa nm_pessoa,
                    ae.bi_anexorelat, 
                    ae.dt_envio,
                    ae.st_envio
        from agendaenvio ae
        inner join pessoa p on (p.cd_pessoa = ae.cd_pessoa)
        inner join contextoemail ce on (ce.nr_contexto = ae.nr_contexto)                
                where ae.ds_mensagem like '%$request->cd_number%' 
                " . (($request->cd_pessoa != 0) ? "and ae.cd_pessoa = $request->cd_pessoa" : "") . "
                " . (($request->nm_pessoa != 0) ? "and p.nm_pessoa like '%$request->nm_pessoa%'" : "") . "
                " . (($request->cpf_cnpj != 0) ? "and p.nr_cnpjcpf = '$request->cpf_cnpj'" : "") . "
                " . (($request->inicio_data != 0) ? "and ae.dt_envio between '$request->inicio_data' and '$request->fim_data'" : "") . "
                " . (($request->ds_email != 0) ? "and ae.ds_emaildest like '%$request->ds_email%'" : "") . "    
                " . (($request->nr_contexto != 0) ? "and ae.nr_contexto in ($request->nr_contexto)" : "");

        $results = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($results);

        $key = "anexo_" . $request->cd_number . "cliente_1" . $request->cd_pessoa . "nr_contexto" . $request->nr_contexto;
        return Cache::remember($key, now()->addMinutes(60), function () use ($query) {
            return DB::connection('firebird')->select($query);
        });
    }
    public function contextoEmail()
    {
        $query = "select ce.nr_contexto, ce.ds_contexto, ce.st_ativo
        from contextoemail ce
        where ce.st_ativo = 'S'
            and ce.tp_envio = 'E'
            --and ce.nr_contexto in (1,4,5,6,7,3,2,10,11,12,13,14,33,32,37,41)
        order by ce.ds_contexto";
        $results =  DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($results);
    }
    public function verEmail($nr_envio, $nr_agenda, $nr_contexto)
    {
        $query = "
            SELECT
                CE.DS_CONTEXTO,
                AE.DS_ASSUNTO,
                REPLACE(AE.DS_MENSAGEM, '[#10]', '</br>') DS_MENSAGEM,
                AE.DS_EMAILREM,
                AE.DS_EMAILDEST,
                AE.DT_ENVIO,
                AE.NR_ENVIO,
                AE.NR_CONTEXTO,
                AE.NR_AGENDA
            FROM AGENDAENVIO AE
            INNER JOIN PESSOA P ON (P.CD_PESSOA = AE.CD_PESSOA)
            INNER JOIN CONTEXTOEMAIL CE ON (CE.NR_CONTEXTO = AE.NR_CONTEXTO)
            WHERE AE.NR_ENVIO = $nr_envio
                AND AE.NR_AGENDA = $nr_agenda
                AND AE.NR_CONTEXTO = $nr_contexto
            ";

        $agendaenvio = DB::connection('firebird')->select($query);
        $results = Helper::ConvertFormatText($agendaenvio);

        // dd('results', $results);

        $anexos = self::AnexoAgendaEnvio($nr_envio, $nr_agenda, $nr_contexto);

        // Criar ANEXOS caso não exista
        $results[0]->ANEXOS = [];

        foreach ($anexos as $anexo) {
            $exploder        = explode('\\', $anexo->DS_CAMINHOANEXO);
            $results[0]->ANEXOS[] = [
                'TITULO' => $anexo->DS_ANEXOMODELO,
                'CAMINHO' => $anexo->DS_CAMINHOANEXO,
                'NR_ANEXO' => $anexo->NR_ANEXO
            ];
        }
        return $results;
    }
    public function AnexoAgendaEnvio($nr_envio, $nr_agenda, $nr_contexto)
    {
        $query = "
            SELECT
                AGE.NR_CONTEXTO,
                AGE.NR_AGENDA,
                AGE.NR_ENVIO,
                AC.DS_ANEXOMODELO,
                AGE.NR_ANEXO,
                AGE.DS_CAMINHOANEXO
            FROM ANEXOAGENDAENVIO AGE
            LEFT JOIN ANEXOCONTEXTO AC ON (AC.CD_EMPRESA = AGE.CD_EMPRESA
                AND AC.NR_CONTEXTO = AGE.NR_CONTEXTO
                AND AC.NR_ANEXOCONTEXTO = AGE.NR_ANEXO)
            WHERE AGE.NR_CONTEXTO = $nr_contexto
                AND AGE.NR_AGENDA = $nr_agenda
                AND AGE.NR_ENVIO = $nr_envio";

        $results = DB::connection('firebird')->select($query);

        return Helper::ConvertFormatText($results);
    }


    public function reenviaFollow($nr_envio, $copia)
    {
        $email = Auth::user()->email;
        return DB::transaction(function () use ($nr_envio, $copia, $email) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $query = "update AGENDAENVIO AE 
                    SET AE.st_envio = 'A' 
                    " . (($copia == 1) ? ", ae.tp_emailcopia = 'N', AE.DS_EMAILCOPIA = '" . $email . "'" : "") . "
                    WHERE AE.nr_envio = $nr_envio";

            return DB::connection('firebird')->statement($query);
        });
    }
}
