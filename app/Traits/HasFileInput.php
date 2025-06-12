<?php

namespace App\Traits;

trait HasFileInput
{

    function fileInput($data)
    {
        if (!$data) {
            return ['path' => "", 'full_path' => ""];
        }
        return ['path' => "", 'full_path' => asset('storage/' . $data)];
    }

    function fileInputs($data)
    {
        if (!$data) {
            return [['path' => "", 'full_path' => ""]];
        }
        return array_map(function ($item) {
            return ['path' => $item, 'full_path' => asset('storage/' . $item)];
        }, $data);
    }
}