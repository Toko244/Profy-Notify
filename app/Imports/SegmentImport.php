<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class SegmentImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $userProfyIds = [];
        foreach ($rows as $row) {
            $userProfyIds[] = $row[0];
        }
        return $userProfyIds;
    }
}
