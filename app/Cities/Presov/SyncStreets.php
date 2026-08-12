<?php

namespace App\Cities\Presov;


use App\Street;
use App\StreetName;
use Illuminate\Support\Facades\DB;

class SyncStreets
{

    public function handle($url)
    {
        $data = json_decode(file_get_contents($url), 1);
        foreach ($data['features'] as $i => $street) {

            DB::table('tagging_tags')->where('slug',
                \Conner\Tagging\Util::slug($street['properties']['N_GM_U']))->update(['name' => $street['properties']['N_GM_U']]);

            $exists = StreetName::where([
                'name' => $street['properties']['N_GM_U'],
            ])->first();
            if (empty($exists)) {
                echo "Added new street: " . $street['properties']['N_GM_U'] . "\n";
                StreetName::create([
                    'name' => $street['properties']['N_GM_U'],
                    'po_id' => $street['properties']['I_GM_U'],
                    'coords' => json_encode($street['geometry']['coordinates'])
                ]);
            } else {
                $exists->update([
                    'po_id' => $street['properties']['I_GM_U'],
                    'coords' => json_encode($street['geometry']['coordinates'])
                ]);
            }
        }


        $streets = json_decode(file_get_contents("https://egov.presov.sk/GeoDataKatalog/supisne_cisla.json"), 1);
        foreach ($streets['features'] as $streetData) {
            $street = $streetData['properties'];
            $latLng = convert102067ToLatLng($streetData['geometry']['coordinates'][0][0],
                $streetData['geometry']['coordinates'][0][1]);
            $exists = Street::where([
                'name' => $street['N_U'],
                'cs' => $street['CS'],
                'co' => $street['CO']
            ])->first();
            if (empty($exists)) {
                //echo "Added {$street['N_U']} {$street['CS']}/{$street['CO']}\n";
                \Illuminate\Support\Facades\DB::table('streets')->insert([
                    'name' => $street['N_U'],
                    'cs' => $street['CS'],
                    'co' => $street['CO'],
                    'point_id' => $street['I_GM_ADR'],
                    'city_part_id' => $street['I_CM'],
                    'city_part_name' => $street['N_CM'],
                    'street_id' => $street['I_U'],
                    'x' => $streetData['geometry']['coordinates'][0][0],
                    'y' => $streetData['geometry']['coordinates'][0][1],
                    'latitude' => $latLng[0],
                    'longitude' => $latLng[1],
                ]);
            }
        }
    }
}