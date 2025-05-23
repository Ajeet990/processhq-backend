<?php
namespace App\Helper;

use App\Models\Module;

class CommonHelper
{
    public static function getPaginationData($data)
    {
        return [
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'per_page' => $data->perPage(),
            'total' => $data->total(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
        ];
    }
}