<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROL_DUENO = 'Dueño';
    public const ROL_ADMINISTRADOR = 'Administrador';
    public const ROL_EMPLEADO = 'Empleado de local';

    /** @var list<string> */
    public const ROLES = [
        self::ROL_DUENO,
        self::ROL_ADMINISTRADOR,
        self::ROL_EMPLEADO,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Indica si el usuario posee alguno de los roles indicados.
     */
    public function tieneRol(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function esAdministrador(): bool
    {
        return $this->tieneRol(self::ROL_ADMINISTRADOR);
    }

    public function esDueno(): bool
    {
        return $this->tieneRol(self::ROL_DUENO);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
