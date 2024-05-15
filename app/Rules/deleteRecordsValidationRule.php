<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;


class deleteRecordsValidationRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    protected $relatedModel;
    protected $relatedColumn;
    protected $relationship;
    protected $title;
    protected $relatedTitle;

    public function __construct(Model $relatedModel, $relatedColumn, $relationship = null, $title, $relatedTitle){
        $this->relatedModel = $relatedModel;
        $this->relatedColumn = $relatedColumn;
        $this->relationship = $relationship;
        $this->title = $title;
        $this->relatedTitle = $relatedTitle;
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if($this->relationship){
            $records = $this->relatedModel::whereHas($this->relationship, function($query) use ($value){
                $query->where($this->relatedColumn, $value);
                $query->where('status', 'A');
            });
        }else{
            $records = $this->relatedModel::where($this->relatedColumn, $value)->where('status','A');
        }
        $records = $records->select('id')->get();

        if(count($records) == 0){
            $fail($this->title . ' #' . $value . ' presenta relacionados con los siguientes registros de '.$this->relatedTitle.': ' . $records->pluck('id')->implode('-'));
        }
    }
}
