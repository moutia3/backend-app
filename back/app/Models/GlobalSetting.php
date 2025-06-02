<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GlobalSetting extends Model
{
    use HasFactory;

    /**
     * 
     *
     * @var string
     */
    protected $table = 'global_settings';

    /**
     * 
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
     *
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'status' => 'string',
        'daily_limit' => 'integer',
    ];

    /**
     *
     *
     * @return bool
     */
    /**
     * 
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

        $totalEmployees = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['employee', 'manager']);
        })->count();

        $absoluteLimit = ceil($totalEmployees * ($this->daily_limit / 100));

        $approvedRequests = TeletravailRequest::where('date', $this->date)
            ->where('status', 'approved')
            ->count();

        return $approvedRequests >= $absoluteLimit;
    }
}