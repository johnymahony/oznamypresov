<?php

namespace App\Http\Ajax;

use App\Exceptions\UserException;
use App\Helpers\HttpRequest;
use App\Log;
use App\Parcel;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 19. 5. 2018
 * Time: 17:17
 */
class FindParcel
{
    /**
     * @param $params
     *
     * @return bool|string|array
     * @throws UserException
     */
    public function handle($params)
    {
        $parcel = Parcel::find($params['parcel_id']);
        Log::debug("Find Parcel: " . $parcel);
        Parcel::checkNumber($parcel->number);
        $uzemie = $parcel->board->cadaster_number;
        if (empty($uzemie)) {
            throw new UserException("Zadajte číslo katastrálneho územia");
        }

        $numberToSearch = preg_replace("/^(.*\s)/", '', $parcel->number);
        if (preg_match('/c/i', $parcel->number)) {
            $url = 'https://kataster.skgeodesy.sk/eskn/rest/services/VRM/parcels_c_view/MapServer/0/query?where=PARCEL_NUMBER%3D%27' . $numberToSearch . '%27+AND+CADASTRAL_UNIT_ID%3D' . $uzemie . '&text=&objectIds=&time=&geometry=&geometryType=esriGeometryEnvelope&inSR=&spatialRel=esriSpatialRelIntersects&relationParam=&outFields=&returnGeometry=true&returnTrueCurves=false&maxAllowableOffset=&geometryPrecision=&outSR=&returnIdsOnly=false&returnCountOnly=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&returnZ=false&returnM=false&gdbVersion=&returnDistinctValues=false&resultOffset=&resultRecordCount=&f=pjson';
        } else {
            $url = 'https://kataster.skgeodesy.sk/eskn/rest/services/VRM/parcels_e_view/MapServer/0/query?where=PARCEL_NUMBER%3D%27' . $numberToSearch . '%27+AND+CADASTRAL_UNIT_ID%3D' . $uzemie . '&text=&objectIds=&time=&geometry=&geometryType=esriGeometryEnvelope&inSR=&spatialRel=esriSpatialRelIntersects&relationParam=&outFields=&returnGeometry=true&returnTrueCurves=false&maxAllowableOffset=&geometryPrecision=&outSR=&returnIdsOnly=false&returnCountOnly=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&returnZ=false&returnM=false&gdbVersion=&returnDistinctValues=false&resultOffset=&resultRecordCount=&f=pjson';
        }
        Log::debug("Kataster request: " . $url);
        try {
            $result = (new HttpRequest())->call($url);
//            $result = file_get_contents($url);
        } catch (\Exception $e) {
            Log::error("Kataster request exception: " . $e->getMessage());
            $parcel->update(['points' => 'E']);
            return false;
        }
        Log::debug("Kataster result: " . $result);
        $decoded = json_decode($result, true);
        if ($decoded === null) {
            Log::debug("Invalid JSON response from kataster find: " . $result);
            $parcel->update(['points' => 'E']);

            return false;
        }
        if (empty($decoded['features']) || empty($decoded['features'][0]['geometry']['rings'])) {
            $parcel->update(['points' => 'NA']);

            return 'NA';
        }
        $coords          = $decoded['features'][0]['geometry']['rings'][0];
        $convertedCoords = [];
        foreach ($coords as $coord) {
            $convertedCoords[] = convert102100ToLatLng($coord[0], $coord[1]);
        }
        $parcel->points = json_encode($convertedCoords);
        $parcel->save();

        return $parcel;
    }
}