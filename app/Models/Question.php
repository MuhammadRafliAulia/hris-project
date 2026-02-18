<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['bank_id', 'sub_test_id', 'question', 'option_a', 'option_a_image', 'option_b', 'option_b_image', 'option_c', 'option_c_image', 'option_d', 'option_d_image', 'option_e', 'option_count', 'correct_answer', 'correct_answer_text', 'order', 'type', 'image', 'image_data', 'image_mime', 'audio', 'is_example'];

    protected $casts = [
        'is_example' => 'boolean',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function subTest()
    {
        return $this->belongsTo(SubTest::class);
    }
}
