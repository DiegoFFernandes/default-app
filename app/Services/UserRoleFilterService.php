<?php

namespace App\Services;

use Illuminate\Support\Facades\Redirect;

class UserRoleFilterService
{
    protected $user;
    protected $area;
    protected $supervisorComercial;
    protected $gerenteUnidade;

    public function __construct($user, $area, $supervisorComercial, $gerenteUnidade)
    {
        $this->user = $user;
        $this->area = $area;
        $this->supervisorComercial = $supervisorComercial;
        $this->gerenteUnidade = $gerenteUnidade;
    }

    public function getFiltros()
    {
        if ($this->user->hasRole('admin')) {
            return [
                'cd_regiao' => '',
                'cd_empresa' => 0,
            ];
        }

        if ($this->user->hasRole('gerente comercial')) {
            $cd_regiao = $this->area->findGerenteSupervisor($this->user->id)
                ->pluck('CD_AREACOMERCIAL')
                ->implode(',');

            if (empty($cd_regiao)) {
                if (empty($cd_regiao)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Usuário com permissão de gerente, mas sem vínculo com região. Fale com o administrador!',
                        'redirect' => route('home'),
                    ], 403); // código HTTP 403 Forbidden
                }
            }

            return [
                'cd_regiao' => $cd_regiao,
                'cd_empresa' => 0,
            ];
        }

        if ($this->user->hasRole('supervisor')) {
            $cd_regiao = $this->supervisorComercial->findSupervisorUser($this->user->id)
                ->pluck('CD_SUPERVISORCOMERCIAL')
                ->implode(',');

            if (empty($cd_regiao)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuário com permissão de supervisor, mas sem vínculo com vendedor. Fale com o administrador!',
                    'redirect' => route('home'),
                ], 403);
            }

            return [
                'cd_regiao' => $cd_regiao,
                'cd_empresa' => 0,
            ];
        }

        if ($this->user->hasRole('gerente unidade')) {
            $cd_empresa = $this->gerenteUnidade->findEmpresaGerenteUnidade($this->user->id)
                ->pluck('cd_empresa')
                ->implode(',');

            return [
                'cd_regiao' => '',
                'cd_empresa' => $cd_empresa,
            ];
        }

        // Caso o usuário não tenha nenhum dos papéis esperados, retorna valores padrão
        return [
            'cd_regiao' => '',
            'cd_empresa' => 0,
        ];
    }
}
