<?php
use Illuminate\Database\Eloquent\Collection;

/**
 * Adds a specified key to each item in an indexed array
 */
function appendKeys(Array $arr, String $key):array
{
    return collect($arr)->map(function($item, $index) use($key){
        return [$key => $item];
    })->toArray();
}

/**
 * Returns items from initial collection that has an id matching the ids array provided as second argument
 */
function findMatches(Collection $initial, Array $ids)
{
    return $initial->filter(function($value, $key) use($ids) {
        return in_array($value->id, $ids);   
    });
}