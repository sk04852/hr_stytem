<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class Day implements Rule
{
    protected $year = null;

    protected $month = null;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($year = null, $month = null)
    {
        $this->month = $month;
        $this->year = $year;
    }


    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        dd($attribute, $value, $this->{$attribute});
        if(is_null($this->year) && is_null($this->month)){
            return false;
        }

        // Carbon::createFromDate($this->year, $this->month, $value); 
        try {
            Carbon::parse("$this->year-$this->month-$value");
            return true;
        } catch (\Exception $e) {
            return false;
        }
        
        
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be valid day.';
    }
}
