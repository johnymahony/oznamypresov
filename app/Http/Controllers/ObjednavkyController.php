<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ObjednavkyController extends Controller
{
    public function objednavky()
    {
        $json = file_get_contents(storage_path() . '/app/Zoznam_Objednavky.json');
        $data = json_decode($json);
        $ico = [];
        $organizacie = [];
        foreach ($data as $row) {
            $row->{'IČO'} = trim($row->{'IČO'});
            if (empty($row->{'IČO'})) {
                $row->{'IČO'} = 'nezname_ico';
            }
            $year = date('Y', strtotime($row->{'Dátum_vystavenia'}));
            if (empty($row->{'Organizačná_štruktúra_úradu'})) {
                $row->{'Organizačná_štruktúra_úradu'} = 'nezadané';
            }
            $organizacie[$row->{'Organizačná_štruktúra_úradu'}] = $row->{'Organizačná_štruktúra_úradu'};
            if (request()->get('rok') && $year != request()->get('rok')) {
                continue;
            }
            if (request()->get('stav') && $row->{"Stav_vybavenia"} != request()->get('stav')) {
                continue;
            }
            if (request()->get('organizacia') && $row->{"Organizačná_štruktúra_úradu"} != request()->get('organizacia')) {
                continue;
            }
            @$ico[$row->{'IČO'}.'-'.$row->{'Dodávateľ'}]['name'] = $row->{'Dodávateľ'};
            @$ico[$row->{'IČO'}.'-'.$row->{'Dodávateľ'}]['ico'] = $row->{'IČO'};
            @$ico[$row->{'IČO'}.'-'.$row->{'Dodávateľ'}]['sum'] += floatval($row->{"Hodnota_objednávky"});
        }

        uasort($ico, function ($a, $b) {
            return $b['sum'] - $a['sum'];
        });

        asort($organizacie);
        return view('objednavky', [
            'data' => $ico,
            'organizacie' => $organizacie,
        ]);
    }

    public function detail($icoInput, $yearInput = null, $statusInput = null)
    {
        $icoInput = urldecode($icoInput);
        $json = file_get_contents(storage_path() . '/app/Zoznam_Objednavky.json');
        $data = json_decode($json);
        $ico = [];
        $organizacie = [];
        foreach ($data as $row) {
            $row->{'IČO'} = trim($row->{'IČO'});
            if (empty($row->{'IČO'})) {
                $row->{'IČO'} = 'nezname_ico';
            }
            $row->{'IČO'} = $row->{'IČO'}.'-'.$row->{'Dodávateľ'};
            if ($row->{'IČO'} != $icoInput) {
                continue;
            }
            $year = date('Y', strtotime($row->{'Dátum_vystavenia'}));
            if (empty($row->{'Organizačná_štruktúra_úradu'})) {
                $row->{'Organizačná_štruktúra_úradu'} = 'nezadané';
            }
            $organizacie[$row->{'Organizačná_štruktúra_úradu'}] = $row->{'Organizačná_štruktúra_úradu'};
            if ($yearInput && $year != $yearInput) {
                continue;
            }
            if ($statusInput && $row->{"Stav_vybavenia"} != $statusInput) {
                continue;
            }
            $ico[] = $row;
        }

//        arsort($ico);
//        asort($organizacie);
        return view('objednavky_detail', [
            'data' => $ico,
            'icoInput' => $icoInput,
            'organizacie' => $organizacie,
        ]);
    }
}
