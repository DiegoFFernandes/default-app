<?php

namespace App\Services;
use App\Repositories\SupervisorRepository;
use Illuminate\Support\Facades\Auth;

class SupervisorAuthService
{
    protected $supervisorRepo;


    public function __construct(SupervisorRepository $repo)
    {
        $this->supervisorRepo = $repo;
    }

    public function getCdSupervisor()
    {
        $user = Auth::user();        

        if ($user && $user->hasRole('supervisor')) {            
            $supervisor = $this->supervisorRepo->searchSupervisor($user->id);
            return $supervisor->cd_supervisorcomercial ?? null;
        }

        return null;
    }
   
}