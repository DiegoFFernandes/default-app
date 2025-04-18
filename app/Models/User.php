<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'empresa',
        'phone',
        'cd_tipopessoa'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function storeData($input)
    {
        $user = new User();

        return $user::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'empresa' => $input['empresa'],
            'phone' => $input['phone'],
            'password' => $input['password'],
            'cd_tipopessoa' => $input['tipopessoa']
        ]);
    }

    public function updateData($input)
    {
        return User::find($input['id'])->update([
            'name' => $input['name'],
            'email' => $input['email'],
            'empresa' => $input['empresa'],
            'phone' => $input['phone'],
            'password' => $input['password'],
            'cd_tipopessoa' => $input['tipopessoa']
        ]);
    }

    public function getData()
    {
        return static::where('id', '!=', '1')
            // ->whereIn('empresa', $cd_empresa)
            ->orderBy('name', 'asc')->get();
    }
    public function userExists($id)
    {
        return static::where('id', $id)->exists();
    }
    public function listUserRole()
    {
        return User::select('users.id', 'users.name', 'users.email', 'users.empresa', 'users.created_at')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->where('users.email', '<>', 'ti.campina@ivorecap.com.br')
            ->where('users.id', '<>', '1')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.empresa', 'users.created_at')
            ->orderBy('id')->get();
    }
}
