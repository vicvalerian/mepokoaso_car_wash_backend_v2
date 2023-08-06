<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiPencucian extends Model
{
    use SoftDeletes;

    protected $guarded = [
        "id",
        "created_at",
        "updated_at",
    ];

    public function kendaraan(){
        return $this->belongsTo(Kendaraan::class);
    }

    public function karyawan(){
        return $this->belongsTo(Karyawan::class);
    }

    public function mobil_pelanggan(){
        return $this->belongsTo(MobilPelanggan::class);
    }

    public function karyawan_pencucis(){
        return $this->belongsToMany(Karyawan::class, 'detail_transaksi_pencucis', 'transaksi_pencucian_id', 'karyawan_id',)->withPivot('upah_pencuci')->whereNull('detail_transaksi_pencucis.deleted_at');
    }

    public function detail_transaksi_pencucis(){
        return $this->hasMany(DetailTransaksiPencuci::class);
    }

    public static function filters(){
        $instance = new static();
        return $instance->getConnection()->getSchemaBuilder()->getColumnListing($instance->getTable());
    }

    public function getCreatedAtAttribute(){
        if(!is_null($this->attributes['created_at'])){
            return Carbon::parse($this->attributes['created_at'])->format('Y:m:d H:i:s');
        }
    }

    public function getUpdatedAtAttribute(){
        if(!is_null($this->attributes['updated_at'])){
            return Carbon::parse($this->attributes['updated_at'])->format('Y:m:d H:i:s');
        }
    }
}
