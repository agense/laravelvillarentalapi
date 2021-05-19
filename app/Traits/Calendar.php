<?php
/**
 * This trait allows mass update or create for attributes based on dates, i.e. allows adding attribute values for each day
 * This trait should only be used by App\Models\Villa class
 * Each attribute that can be updated using this trait must have a belongsTo relation to a villa object
 * Villa object must have a hasMany relation to each attribute updatable via this trait
 */
namespace App\Traits;
use App\Services\DateService;
use App\Models\Villa;
use Illuminate\Support\Str;
use Carbon\Carbon;

trait Calendar 
{
    private $calendarAttributes = ['availability','price'];

    private $field;
    private $relation;
    private $fieldClass;
    private $startDate;
    private $endDate;
    private $requestPeriod;

    /**
     * Sets required class fields 
     * @param String $field - relation to be used on villa object (ex: availability, price)
     * @param String $start
     * @param String $end
     * @return Void
     */
    private function setFields(String $field, String $start, String $end)
    {
        $this->validateFields($field);
        $this->field = $field;
        $this->relation = Str::plural($field);
        $this->fieldClass = 'App\Models\\'.ucfirst($field);
        $this->startDate = $start;
        $this->endDate = $end;
        $this->requestPeriod = (new DateService)->generateDates($this->startDate, $this->endDate);
    }

    /**
     * Validates if provided $field attribute is a valid attribute for this class
     * @param String $field - attribute to be updated (ex: availability, price)
     * @return Void
     */
    private function validateFields(String $field)
    {
        if(! in_array($field,  $this->calendarAttributes)){
            abort(422, "[Period based updates are only available for attributes: ".implode(', ', $this->calendarAttributes) );
        }
    }

    /**
     * Loads existing data for $field attribute via relation vith Villa model
     * @return Void
     */
    private function loadExistingData()
    {
        $this->load([$this->relation => function ($query) {
            $query->whereBetween('date', [$this->startDate, $this->endDate]);
        }]);
    }

    /**
     * Retrieves existing dates in period from $start to $end for $field relation  
     * Fills dates from period that are not defined in db using null values
     * @param String $field - relation to be updated (ex: availability, price)
     * @param String $start - date as string in format 'Y-m-d'
     * @param String $end - date as string in format 'Y-m-d'
     * @return Array - a list of dates and atribute values in period requested
     */
    private function getPeriodData(String $field, String $start, String $end):array
    {
        $this->setFields($field, $start, $end);

        //Get availabilities within period
        $this->loadExistingData();

        //Generate missing data in period
        $missingDates = self::getMissingDates();
        $newData = self::generateDateEntries($missingDates);

        //Return full period
        $fullPeriod = array_merge($this->{$this->relation}->toArray(), $newData);
        return collect($fullPeriod)->sortBy('date')->toArray();
    }

    /**
     * Updates existing dates and adds new ones in period from $start to $end with value provided
     * @param String $field - relation to be updated (ex: availability, price)
     * @param String $start - date as string in format 'Y-m-d'
     * @param String $end - date as string in format 'Y-m-d'
     * @param Int $value
     * @return Array - a list of dates updated or created
     */
    private function updatePeriod(String $field, String $start, String $end, Int $value):array
    {
        $this->setFields($field, $start, $end);

        //Get existing and missing dates
        $this->loadExistingData();
        $updatableDates = self::getExistingDates();
        $missingDates = self::getMissingDates();

        // Update existing dates
        if(!empty($updatableDates)){
            self::massUpdatePeriod($updatableDates, $value);
        }
        // Create entries for dates that are not set yet in db table   
        if(!empty($missingDates)){
            $new = self::generateDateEntries($missingDates, $value);
            $this->{$this->relation}()->createMany($new);
        }
        // Return modified entries
        $this->refresh();
        return $this->{$this->relation}->whereIn('date', $this->requestPeriod)->sortBy('date')->toArray();
    }

    /**
     * Mass update of defined relation to villa object for specific dates with specific value
     * @param Array $dates (indexed array of dates as string in format 'Y-m-d')
     * @param Int $value
     * @return Void
     */
    private function massUpdatePeriod(Array $dates, Int $value)
    {
        $this->fieldClass::where('villa_id', $this->id)
        ->whereIn('date', $dates)
        ->update([$this->field => $value]);
    }

    /**
     * Generates an array of values for $field relation for a single date
     * @param String $date
     * @param Int $value (nullable)
     * @return Array
     */
    private function generateDateEntry(String $date, ?Int $value = null ):array
    {
        $arr = ["date"=> $date, "$this->field" => $value];
        return $arr;
    }

    /**
     * Generates an array of values for $field relation for multiple dates
     * @param Array $dates
     * @param Int $value (nullable)
     * @return Array
     */
    private function generateDateEntries(Array $dates, ?Int $value = null):array 
    {
        return collect($dates)->map(function($date, $index) use($value){
            return self::generateDateEntry($date, $value);
        })->toArray();
    }

    /**
     * Returns an array of dates in specified period existing in db for specific relation
     * @return Array
     */
    private function getExistingDates():array
    {
        return $this->{$this->relation}->pluck('date')->toArray();
    }

    /**
     * Returns an array of dates in specified period non-existing in db for specific relation
     * @return Array
     */
    private function getMissingDates():array
    {
        return array_diff($this->requestPeriod, self::getExistingDates());
    }

}
