<?php

namespace Marvel\Traits;

use Illuminate\Support\Str;

trait MediaManager
{
    public function uploadImages($request, $model, $collectionName, $disk)
    {
        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $file) {

                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $model->addMedia($file)
                    ->usingFileName($fileName)
                    ->toMediaCollection($collectionName, $disk);
            }
        }
        return true;
    }


    public function updateImages($request, $model, $collectionName, $disk)
    {
        $model->clearMediaCollection($collectionName);
        $this->uploadImages($request, $model, $collectionName, $disk);
    }

    public function deleteFile($request, $model, $collectionName)
    {
        $media = $model->getMedia($collectionName)->find($request->id);
        $media->delete();
        return true;
    }
}
