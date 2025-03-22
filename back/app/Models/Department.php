<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    // Relation avec les demandes de télétravail
    public function teletravailRequests()
    {
        return $this->hasMany(TeletravailRequest::class);
    }
}