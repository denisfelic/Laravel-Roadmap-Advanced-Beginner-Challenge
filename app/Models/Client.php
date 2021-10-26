<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    use HasFactory;

    public $fillable = [
        'company_name',
        'VAT',
        'address'
    ];


    /**
     * Get the project associated with the Client
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function project(): HasOne
    {
        return $this->hasOne(Project::class, 'assigned_client_id');
    }
}
