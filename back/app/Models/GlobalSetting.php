<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GlobalSetting extends Model
{
    use HasFactory;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'global_settings';

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'status',
        'daily_limit',
        'description',
    ];

    /**
     * Les attributs qui doivent être castés en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'status' => 'string',
        'daily_limit' => 'integer',
    ];

    /**
     * Vérifie si le télétravail est autorisé pour cette date.
     *
     * @return bool
     */
    /**
     * Relation avec les demandes de télétravail (optionnelle).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teletravailRequests()
    {
        return $this->hasMany(TeletravailRequest::class, 'date', 'date');
    }

        public function isLimitReached()
    {
        if ($this->status !== 'limited' || is_null($this->daily_limit)) {
            return false;
        }

        // Calculer le nombre total d'employés et managers
        $totalEmployees = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['employee', 'manager']);
        })->count();

        // Calculer la limite absolue
        $absoluteLimit = ceil($totalEmployees * ($this->daily_limit / 100));

        // Compter les demandes approuvées pour cette date
        $approvedRequests = TeletravailRequest::where('date', $this->date)
            ->where('status', 'approved')
            ->count();

        return $approvedRequests >= $absoluteLimit;
    }
}