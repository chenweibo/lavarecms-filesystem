<?php

namespace chenweibo\LaravelCmsFile;

use Illuminate\Http\Request;

class LaravelCmsFile
{
    // Build wonderful things

    public static function resolveFromRequest(Request $request, string $name = null,string $path)
    {
        $config = static::recursiveMergeConfig(
            config('laravelcmsfile.strategies.default', []),
            config("laravelcmsfile.strategies.{$name}", [])
        );

        return new Upload($config,$request,$path);
    }

    /**
     * Array merge recursive distinct.
     *
     * @param array &$array1
     * @param array &$array2
     *
     * @return array
     */
    protected static function recursiveMergeConfig(array $array1, array $array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = \forward_static_call(\sprintf('%s::%s', __CLASS__, __FUNCTION__), $merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}
