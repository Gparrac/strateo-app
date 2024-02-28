<?php

namespace App\Http\Controllers\CRUD\Interfaces;

interface RecordOperations
{
    public function singleRecord($id);
    public function allRecords($ids = null, $pagination=5, $sorters = [], $keyword =null, $typeKeyword = null, $format = null);
}
