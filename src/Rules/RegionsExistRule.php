<?php

namespace RedJasmine\Region\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use RedJasmine\Region\Domain\Models\Region;
use Tymon\JWTAuth\JWT;

class RegionsExistRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail) : void
    {
        $value = $this->analysis($value);
        // 数量需要相等
        $count = Region::whereIn('id', $value)->count();
        if ($count !== count($value)) {
            $fail('区域错误');
        }
    }

    protected function analysis(mixed $value) : array
    {
        $regions = [];
        if (is_array($value)) {
            $regions = array_keys($value);
        }

        if (is_string($value)) {
            $regions = explode(',', $value);
        }

        return array_unique($regions);
    }
}
