<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaCovidCase extends Model
{
    use SoftDeletes;

    public $table = 'la_covid_cases';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'alias', 'barangay_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'barangay_id', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function barangay() {
        return $this->belongsTo(Barangay::class);
    }
}
