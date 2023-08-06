<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiKedai extends Model
{
    use SoftDeletes;

    protected $guarded = [
        "id",
        "created_at",
        "updated_at",
    ];

    public function karyawan(){
        return $this->belongsTo(Karyawan::class);
    }

    public function detail_transaksi_kedais(){
        return $this->hasMany(DetailTransaksiKedai::class);
    }

    public function menu_kedai(){
        return $this->belongsToMany(MenuKedai::class, 'detail_transaksi_kedais', 'transaksi_kedai_id', 'menu_kedai_id')->withPivot('kuantitas', 'sub_total')->whereNull('detail_transaksi_kedais.deleted_at');
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
