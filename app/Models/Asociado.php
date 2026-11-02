<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asociado extends Model
{
    use HasFactory;

    // le digo a laravel que columnas se pueden llenar desde un formulario
    protected $fillable = [
        'nombre',
        'dpi',
        'correo',
        'telefono',
    ];
}
