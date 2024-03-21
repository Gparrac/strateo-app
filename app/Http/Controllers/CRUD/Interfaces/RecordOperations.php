<?php

namespace App\Http\Controllers\CRUD\Interfaces;

interface RecordOperations
{
    public function singleRecord($id);
    public function allRecords($ids = null, $pagination=5, $sorters = [], $filters=[], $format = null);
}
