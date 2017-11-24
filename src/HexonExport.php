<?php

namespace RoyScheepens\HexonExport;

use RoyScheepens\HexonExport\Models\Occasion;
use RoyScheepens\HexonExport\Models\OccasionImage;
use RoyScheepens\HexonExport\Models\OccasionOption;

use Storage;

use Illuminate\Support\Str;
use Carbon\Carbon;

class HexonExport {

    /**
     * The Hexon Id of the resource
     * @var Number
     */
    protected $resourceId;

    /**
     * The local resource, based on the Hexon Id
     * @var Occasion
     */
    protected $resource;

    /**
     * Class Constructor
     */
    function __construct()
    {
        // todo: add option to set image disk on storage
    }


    public function handle(\SimpleXmlElement $xml)
    {
        // todo: validate XML

        $this->resourceId = $xml->voertuignr_hexon;

        $this->resource = Occasion::where('hexon_id', $this->resourceId)->firstOrNew();

        $this->a

        switch ($xml->attributes()->actie)
        {
            // Creates or updates the existing record
            case 'add':
            case 'change':

                // $this->storeOptions($xml->opties); // ??

                $this->storeImages($xml->afbeeldingen);

                break;

            // Deletes the resource and all associated data
            case 'delete':
                $this->resource->delete();
                break;

            // Nothing to do here...
            default:
                break;
        }

        // Store the XML to disk
        $this->storeXml($xml);
    }

    /**
     * Stores the images to disk
     * @param  Array $images An array of images
     * @return void
     */
    private function storeImages($images)
    {
        foreach ($images as $imageId => $imageUrl)
        {
            if( $contents = file_get_contents($imageUrl) )
            {
                $imageResource = $this->resource->images->where('resource_id', $imageId)->firstOrNew();

                $imageResource->resource_id = $imageId;

                $imageResource->filename = implode('_', [
                    $this->resourceId,
                    $imageId
                ]).'jpg';

                Storage::disk('public')->put($imageResource->path, $contents);

                $imageResource->save();

            } else {
                // todo: handle exception
            }
        }
    }

    /**
     * Stores the XML to disk
     * @param  SimpleXmlElement $xml The XML data to write to disk
     * @return void
     */
    private function storeXml($xml)
    {
        // todo: add hexon id to filename
        $filename = Carbon::format('Y-m-d H:i:s').'xml';
        $path = config('hexon-export.xml_storage_path');

        Storage::put($path.$filename, $xml);
    }

    // function handle
    // function handleImage
}