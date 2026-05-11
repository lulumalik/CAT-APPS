<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    protected $fillable = [
        'program_category',
        'title',
        'subtitle',
        'body_text',
        'sign_name',
        'sign_position',
        'theme_color',
    ];
}
