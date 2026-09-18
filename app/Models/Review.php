<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'rating_reviews';
    protected $primaryKey = 'rating_id';
    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'client_id',
        'worker_id',
        'client_username',
        'worker_username',
        'rating_score',
        'review_text',
    ];
}
