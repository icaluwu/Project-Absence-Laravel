<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','content','posted_by','visible_from','visible_to',
    ];

    protected $casts = [
        'visible_from' => 'date',
        'visible_to' => 'date',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
