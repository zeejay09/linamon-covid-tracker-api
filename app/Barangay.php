<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barangay extends Model
{
    use SoftDeletes;

    public $table = 'barangays';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'brgy_name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function user() {
        return $this->hasMany(User::class);
    }

    public function pui() {
        return $this->hasMany(Pui::class);
    }

    public function pum() {
        return $this->hasMany(Pum::class);
    }

    public function covidcase() {
        return $this->hasMany(LaCovidCase::class);
    }
}
