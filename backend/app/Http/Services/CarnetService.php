<?php

namespace App\Http\Services;

use App\Models\InfoCarnet;
use Exception;

class CarnetService
{
    public function getAll()
    {
        return InfoCarnet::all();
    }

    public function create(array $data)
    {
        return InfoCarnet::create($data);
    }

    public function findById($id)
    {
        return InfoCarnet::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $infoCarnet = $this->findById($id);
        $infoCarnet->update($data);
        return $infoCarnet;
    }

    public function delete($id)
    {
        $infoCarnet = $this->findById($id);
        return $infoCarnet->delete();
    }
}
