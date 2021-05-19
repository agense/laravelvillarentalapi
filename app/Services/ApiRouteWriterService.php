<?php
/**
 * This is a helper class for CreateRoutesFile Artisan command. 
 * Allows route grouping and decription generation.
 */
namespace App\Services;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class ApiRouteWriterService {

    private $groupingLevel;

    public function __construct(){
        $this->groupingLevel = config('commands.route_display.grouping_level');
    }

    /**
     * Group routes by uri params
     * @param $inputData
     * @return Array (or initial data if conversion to collection fails)
     */
    public function groupRoutes($inputData)
    {
        $data = self::toCollection($inputData);
        if($data == null) return $inputData;

        if($this->groupingLevel){
            //Group routes by the first segment in the uri
            $data = $data->groupBy( function ($item, $key)  { 
                return self::getUriParam($item, 1);
            });

            //If all routes have a second segment in the uri, group each group to subgroups by the second segment in the uri
            if($this->groupingLevel > 1 ){
                $data = $data->map(function ($group) { 
                    if($this->subgroupingPossible($group, 2)){
                        return $group->groupBy(function ($value, $key) { 
                            return self::getUriParam($value, 2);
                        });  
                    }else{
                        return $group;   
                    }
                });
            }
        }
        return $data->toArray();
    }

    /**
     * Returns false if at least one route inside the group does not have a segment of number specified as level, true otherwise
     * @param Array/Object $group
     * @param Int $level
     * @return Bool
     */
    private function subgroupingPossible($group, Int $level){
        foreach($group as $route){
            if(self::getUriParam($route, $level) == null){
                return false;
            }
        }
        return true;
    }
   
    /**
     * Creates descriptions for routes based on uri params
     * @param $inputData
     * @return Array (or initial data if conversion to collection fails)
     */
    public function describeRoutes($inputData)
    {
        $data = self::toCollection($inputData);
        if($data == null) return $inputData;

        if(! in_array( array_key_first($data->first()), ["method", "uri"])){
            throw new \Exception('Route object inaccessible. This may happen if routes are grouped. Add descriptions before grouping routes. ');
        }
        $data = $data->map(function ($item, $key) use($data) { 
            $item['description'] = self::createAutoDescription($item);
            return $item;
        });
        return $data;
    }

    /**
     * Creates automatic description for each route based on route syntax
     * @param Array $item - route data
     * @return String 
     */
    private function createAutoDescription(Array $item):String
    {
        $output = "";
        // Describe Action
        if($item['method'] == "GET" || $item['method'] == "GET|HEAD"){
            $output .= "Get ";
        }elseif($item['method'] == "POST" || $item['method'] == "POST|HEAD"){
            $output .= "Create ";
        }elseif($item['method'] == "PUT" || $item['method'] == "PATCH" || $item['method'] == "PUT|PATCH"){
            $output .= "Update ";
        }elseif($item['method'] == "DELETE"){
            $output .= "Delete ";
        }
        //Describe Resource
        if(count(explode('/', $item['uri'])) <= 2){
            $resource = self::getUriParam($item, 1);
            $output.= $resource;
        }else{
            $resource = self::getUriParam($item, 3);
            if( $resource !== null && Str::startsWith($resource, '{')){
                $output.= Str::replaceFirst("}", "", Str::replaceFirst("{", "", $resource));
                
                $specific = self::getUriParam($item, 4);   
                $output.= ($specific !== null) ? " $specific" : "";
            }else{
                $resource = self::getUriParam($item, 2);
                $output.= ($item['method'] == "POST" || $item['method'] == "POST|HEAD" ) ? Str::singular($resource) : $resource;
            }
        }
        return $output;
    }

    /**
     * Transform json data or array to Illuminate\Support\Collection instance
     * @param $data
     * @return Collection or null
     */
    private function toCollection($data):?Collection
    {
        if($data instanceof Collection){
            return $data;
        }
        if(is_string($data)){
            $data= json_decode($data, true);
        }
        if(is_array($data)){
            return collect($data);
        }
        return null;
    }

    /**
     * Gets a param from route's uri specified by index defining its position in route's uri as array
     * @param $item - object or array instance
     * @param Int $index
     * @return String
     */
    private function getUriParam(Array $item, Int $index):?String
    {
        return explode('/', $item['uri'])[$index] ?? null;
    }
}
