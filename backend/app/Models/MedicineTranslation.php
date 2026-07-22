<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicineTranslation extends Model
{
    protected $fillable=[
'medicine_id',
'language_id',
'name',
'description',
'usage_instruction'
];
public function medicine()
{
    return $this->belongsTo(Medicine::class);
}

public function language()
{
    return $this->belongsTo(Language::class);
}
}
