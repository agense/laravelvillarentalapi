<?php
namespace App\Services;
use Carbon\Carbon;

class DateService{
     
    /**
     * Return the current date as date string
     * @return String
     */
    public static function defaultPeriodStartDate():string
    {
        return Carbon::now()->toDateString();
    }

    /**
     * Return the last day of the following month as date string
     * @return String
     */
    public static function defaultPeriodEndDate():string
    {
        return Carbon::now()->endOfMonth()->toDateString();
    }
    
    /**
     * Generates an array of all dates of the curent year as date string
     * @return Array
     */
    public function generate_current_year():array
    {
        $now = Carbon::now();
        $current_year = $now->year;
    
        $start = $now->format('Y-m-d');
        $end = "{$current_year}-12-31"; 
        return self::generate_dates($start, $end, true);
    }
    
    /**
     * Generates an array of all dates of the following year as date string
     * @return Array
     */
    public function generate_following_year():array
    {
        $now = Carbon::now();
        $nextYear = $now->year++;
        return self::generate_year($nextYear);
    }
    
    /**
     * Generates an array of all dates of the specified year as date string
     * @param Int $year
     * @return Array
     */
    public function generate_year(Int $year):array
    {
        $start = "{$year}-01-01";
        $end = "{$year}-12-31"; 
        return self::generate_dates($start, $end, true);
    }
    
    /**
     * Generates an array of all dates in the specified period as date string
     * @param String $start
     * @param String $end
     * @return Array
     */
    public function generateDates(String $start, String $end):array
    {
        $arr = [];
        $period = Carbon::parse($start)->daysUntil($end); 

        foreach ($period as $date) {
            array_push($arr, $date->format('Y-m-d'));
        }
       return $arr;
    }
}