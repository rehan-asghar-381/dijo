<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DataSetting extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'id' => 'integer',
        // 'status' => 'integer',
    ];

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }


    // public function getValueAttribute($value){
    //     if (count($this->translations) > 0) {
    //         foreach ($this->translations as $translation) {
    //             if ($translation['key'] == $this->key) {
    //                 return $translation['value'];
    //             }
    //         }
    //     }
    //     return $value;
    // }

    protected static function booted()
    {
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                return $query->where('locale', app()->getLocale());
            }]);
        });
    }

}
