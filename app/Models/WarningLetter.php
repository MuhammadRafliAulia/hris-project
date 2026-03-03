<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarningLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'nik',
        'jabatan',
        'departemen',
        'alasan',
        'paragraf_kedua',
        'sp_level',
        'nomor_surat',
        'tanggal_surat',
        'status',
        'approved_by',
        'approved_at',
        'signer_name_1',
        'signer_jabatan_1',
        'signature_1',
        'signer_name_2',
        'signer_jabatan_2',
        'signature_2',
        'signer_name_3',
        'signer_jabatan_3',
        'signature_3',
        'signer_name_4',
        'signer_jabatan_4',
        'signature_4',
        'signer_name_5',
        'signer_jabatan_5',
        'signature_5',
        'tempat',
        'proses',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isPendingHr()
    {
        return $this->status === 'pending_hr';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Get status badge label
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu Tanda Tangan',
            'pending_hr' => 'Menunggu Tanda Tangan HR',
            'approved' => 'Sudah Ditandatangani',
        ];
        return isset($labels[$this->status]) ? $labels[$this->status] : ucfirst($this->status);
    }

    /**
     * Get SP label text
     *
     * @return string
     */
    public function getSpLabelAttribute()
    {
        $labels = [1 => 'SP-1', 2 => 'SP-2', 3 => 'SP-3'];
        return isset($labels[$this->sp_level]) ? $labels[$this->sp_level] : 'SP-' . $this->sp_level;
    }
}
