<?php

namespace App\Repositories;

use App\Models\GlobalSetting;

class GlobalSettingRepository implements GlobalSettingRepositoryInterface
{
    public function all()
    {
        return GlobalSetting::all();
    }

    public function find($id)
    {
        return GlobalSetting::find($id);
    }

    public function create(array $data)
    {
        return GlobalSetting::create($data);
    }

    public function update($id, array $data)
    {
        $setting = $this->find($id);
        if ($setting) {
            $setting->update($data);
            return $setting;
        }
        return null;
    }

    public function delete($id)
    {
        $setting = $this->find($id);
        if ($setting) {
            $setting->delete();
            return true;
        }
        return false;
    }
}