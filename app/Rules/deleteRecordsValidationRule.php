<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
        $this->relatedModel = $relatedModel::where('status','A');
        $this->relatedColumn = $relatedColumn;
        $this->relationship = $relationship;
        $this->title = $title;
        $this->relatedTitle = $relatedTitle;
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if($this->relationship){
            $records = $this->relatedModel->whereHas($this->relationship, function($query) use ($value){
                $query->where($this->relatedColumn, $value);

            });
        }else{

            $records = $this->relatedModel->where($this->relatedColumn, $value);
        }
        // dd(User::where('role_id',32)->where('status','A')->get()->isNotEmpty());
        $records = $records->select('id')->get() ?? collect();

        if ($records->isNotEmpty()){

            $fail($this->title . ' #' . $value . ' presenta relacionados con los siguientes registros de '.$this->relatedTitle.': ' . $records->pluck('id')->implode('-'));
        }

    }
}
