<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiseaseTranslation extends Model
{
    protected $fillable=[
'disease_id',
'language_id',
'name',
'description'
];
public function disease()
{
    return $this->belongsTo(Disease::class);
}

public function language()
{
    return $this->belongsTo(Language::class);
}
}
