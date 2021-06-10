<?php
namespace App\Services;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Artisan;

class ApiRouteDisplayService {

    private $path;
    private $createPath;
    private $groupingLevel;
    
    public function __construct(){
        $this->path = "/data/routes.json";
        $this->filePath = storage_path()."/app".$this->path; 
        $this->groupingLevel = config('commands.route_display.grouping_level') ?? 0;
    }

    /**
     * Read and return data from json file storing api routes
     * If routes.json file does not exist, create one
     * @return Array - decoded file data
     */
    public function readData()
    {
        if(! Storage::exists($this->path)){
            Artisan::call("route:to-file");
        }
        return json_decode(file_get_contents($this->filePath), true);
    }

   /**
    * Display route data as html
    * @return String HTML
    */
    public function displayDataAsHtml()
    {
        $data = self::readData();
        $output = "";
        foreach($data as $key => $value){
            if(is_numeric($key)){
                $output .= self::tableLoop($data);
            }elseif(count($value) == 1 && !is_array($value)){
                $output .= "<h4>".ucfirst($key)."</h4>";
                $output .= self::tableLoop($value);
            }else{
                $output .= self::recursiveLoop($data);
                unset($data[$key]);
            }
        }
        return $output;
    }

    /**
     * Creates an HTML table for an array of routes
     * @param Array $data
     * @return String (HTML)
     */
    private function tableLoop(Array $data)
    {
        $output = '<table class="highlight"><thead><tr><th>Method</th><th>Uri</th>';
        if(config('commands.route_display.route_autodescription')): 
            $output .= '<th>Description</th>';
        endif;
        $output .= '</tr></thead><tbody>';
        foreach($data as $k => $v){
             $output .= '<tr>';
             if(is_array($v)){
                 foreach($v as $ke => $ve){
                     $output .= "<td>".$ve."</td>";
                 }
             }else{
                 $output .= "<td>".$v."</td>";
             }
             $output.= '</tr>';
         }
         $output .= '</tbody></table>';
         return $output;
    }

    /**
     * Creates HTML output for grouped routes using recusive looping
     * @param Array $data
     * @param String $output
     * @return String (HTML)
     */
    private function recursiveLoop(Array $data, $output = "") {
        foreach($data as $key => $value){
            if(is_array($value) && !is_numeric(array_key_first($value))){
                $output .= "<h4>".ucfirst($key)." Routes</h4>";
                return self::recursiveLoop($value, $output);
                
            }elseif(is_array($value) && is_numeric(array_key_first($value))) {
               $output .= !is_numeric($key) ? "<h5>".ucfirst($key)."</h5>" : "<h5>Other</h5>";
               $output .= self::tableLoop($value);

                //Unset looped through level
                unset($data[$key]);
                if(!empty($data) && $this->groupingLevel > 1){
                    return self::recursiveLoop($data, $output);
                }else{
                    return $output;
                }
            }
        }
    }

}