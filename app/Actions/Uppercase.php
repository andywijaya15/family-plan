<?php

namespace App\Actions;

class Uppercase
{
    public static function execute(array|object $data): array|object
    {
        $isObject = is_object($data);

        $arrayData = $isObject ? get_object_vars($data) : $data;

        foreach ($arrayData as $key => $value) {
            $lowerKey = strtolower($key);

            if (
                $lowerKey === 'id' ||
                str_ends_with($lowerKey, '_id') ||
                $lowerKey === 'password' ||
                $lowerKey === 'email' ||
                $lowerKey === 'remember_token'
            ) {
                continue;
            }

            if (is_string($value)) {
                $arrayData[$key] = strtoupper($value);
            }
        }

        if ($isObject) {
            foreach ($arrayData as $key => $val) {
                $data->{$key} = $val;
            }

            return $data;
        }

        return $arrayData;
    }
}
