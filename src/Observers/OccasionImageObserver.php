<?php

namespace RoyScheepens\HexonExport\Observers;

use RoyScheepens\HexonExport\Models\OccasionImage;

use Storage;

class OccasionImageObserver
{
    /**
     * Deletes occasion image from disk before deleting the resource
     *
     * @param  OccasionImage  $image
     * @return void
     */
    public function deleting(OccasionImage $image)
    {
        // todo: this does not seem to work, check it
        if(Storage::disk('public')->exists($image->path))
        {
            Storage::disk('public')->delete($image->path);
        }
    }
}