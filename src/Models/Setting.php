<?php

namespace Habib\Settings\Models;

use Habib\Settings\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Setting extends Model
{
    use UuidTrait;

    protected $fillable = ['name', 'type', 'value', 'locale' , 'group_by'];

    public static function validateCreate(array $validate = null)
    {

        return $validate ?? [
                'name' => ['required', 'max:255'],
                'value' => ['required'],
                'locale' => ['required', Rule::in(config('layout.locales', []))],
                'type' => ['required', Rule::in(['string', 'text', 'number', 'file'])],
            ];
    }

    public static function validateUpdate(array $validate = null)
    {

        return $validate ?? [
                'name' => ['sometimes:max:255'],
                'value' => ['sometimes'],
                'locale' => ['sometimes', Rule::in(config('layout.locales', []))],
                'type' => ['sometimes', Rule::in(['string', 'text', 'number', 'file'])],
            ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Model $model) {
            $model->locale = $model->locale ?? app()->getLocale();
            $model->type = $model->type ?? 'string';
        });
    }

    public function scopeSearch(Builder $query, $column, $like = false)
    {
        $value = request($column, null);
        return $query->when($value, function (Builder $builder) use ($value, $like, $column) {
            $mark = $like ? '%' : '';
            return $builder->where($column, $like ? 'LIKE' : '=', $mark . $value . $mark);
        });
    }

    public function getValueAttribute($value)
    {

        return $this->type == 'file' ? url($value) : $value;
    }
}
