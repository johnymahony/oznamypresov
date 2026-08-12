<?php

function setUser($user)
{
    \App\Helpers\Session::$data['user'] = $user;
}

function user()
{
    if (isset(\App\Helpers\Session::$data['user'])) {
        return \App\Helpers\Session::$data['user'];
    } else {
        if (empty(auth()->user())) {
            if (php_sapi_name() === 'cli') {
                return __('Cron');
            } else {
                return __('Cityzen');
            }
        }

        return auth()->user()->name;
    }
}

function dateTimeNow($strtotime = null, $format = 'Y-m-d H:i:s')
{
    if ($strtotime) {
        return date($format, strtotime($strtotime));
    }

    return date($format);
}

function dateNow($strtotime = null)
{
    if ($strtotime) {
        return date('Y-m-d', strtotime($strtotime));
    }

    return date('Y-m-d');
}

//Works only with hh:mm:ss format
function getMinutesFromString($time)
{
    $parts = explode(':', $time);

    return intval($parts[0]) * 60 + intval($parts[1]) + intval($parts[2]) / 60;
}

function dispatchJob($job, $synch = true)
{
    if ($synch) {
        return $job->handle();
    }

//    event(new \App\Broadcast\JobStarted(get_class($job)));
    return dispatch($job);
}

function twoDecimals($number, $dec_point = '.')
{
    if (empty($number)) {
        $number = 0;
    }

    return number_format(round($number, 2), 2, $dec_point, '');
}

function getModel($name, $id)
{
    $class = '\App\\' . str_replace('_', '\\', $name);

    return $class::findOrFail($id);
}

function skDate($date)
{
    return date('d.m.Y', strtotime($date));
}

function handleSkDateTime($string)
{
    if ( ! preg_match("/\d\d\d\d\-\d\d\-\d\d/", $string)) {
        return $string;
    }
    if (preg_match("/ /", $string)) {
        return skDateTime($string);
    }

    return skDate($string);
}

function skDateTime($date)
{
    if (empty($date)) {
        return '';
    }

    return date('d.m.Y H:i:s', strtotime($date));
}

function skTime($date)
{
    if (empty($date)) {
        return '';
    }

    return date('H:i:s', strtotime($date));
}

function getModelName($object)
{
    return @array_pop(explode('\\', get_class($object)));
}

function onlyNumbers($text)
{
    return preg_replace("/[^0-9]/", '', $text);
}

function onlyLetters($text)
{
    return preg_replace("/[^a-zA-Z]/", '', $text);
}

function onlyLettersNumbers($text)
{
    return preg_replace("/[^a-zA-Z0-9]/", '', $text);
}

function onlyText($text)
{
    return preg_replace("/[^0-9a-zA-Z]/", '', $text);
}

function removeSpaces($string)
{
    return str_replace(' ', '', $string);
}

if (!function_exists('mb_str_pad')) {
    function mb_str_pad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT)
    {
        $diff = strlen($input) - mb_strlen($input);

        return str_pad($input, $pad_length + $diff, $pad_string, $pad_type);
    }
}

function readIban($iban)
{
    $r = '';
    foreach (str_split($iban) as $i => $letter) {
        if ($i % 4 == 0) {
            $r .= ' ';
        }
        $r .= $letter;
    }

    return trim($r);
}

function generateRandomString($length = 10)
{
    $characters = 'ABCDEFGHJKMNPQRSTUVXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

function env2($key, $default = null)
{
    $env = env($key, $default);
    if ($env === $default && isset($_ENV[$key])) {
        return $_ENV[$key];
    }

    return $env;
}

function parseIban($iban)
{
    $iban = str_replace(' ', '', $iban);
    if ( ! preg_match("/^.*(\d{4})(\d{6})(\d{10})$/", $iban, $matches)) {
        throw new \Exception("Unable to parse iban: $iban");
    }

    return [
        $matches[2],//predcislie
        $matches[3],//cislo
        $matches[1]//banka
    ];
}

function number2words($number, $units = null)
{

    $hyphen = ''; // in english "-", v slovencine ziadny
    $conjunction = ' '; // in english ' and ' v slovencine nepouzivame
    $separator = ' ';
    $negative = 'mínus ';
    $decimal = ' celé ';
    $dictionary = array(
        0 => 'nula',
        1 => 'jeden',
        2 => 'dva', // dvojtvar dve, dve - napr. 22000 - dvadsatDVA tisic, 200 = DVE sto
        3 => 'tri',
        4 => 'štyri',
        5 => 'päť',
        6 => 'šesť',
        7 => 'sedem',
        8 => 'osem',
        9 => 'deväť',
        10 => 'desať',
        11 => 'jedenásť',
        12 => 'dvanásť',
        13 => 'trinásť',
        14 => 'štrnásť',
        15 => 'pätnásť',
        16 => 'šestnásť',
        17 => 'sedemnásť',
        18 => 'osemnásť',
        19 => 'devätnásť',
        20 => 'dvadsať',
        30 => 'tridsať',
        40 => 'štyridsať',
        50 => 'päťdesiat',
        60 => 'šesťdesiat',
        70 => 'sedemdesiat',
        80 => 'osemdesiat',
        90 => 'deväťdesiat',
        100 => 'sto',
        1000 => 'tisíc',
        1000000 => 'milón',
        1000000000 => 'bilión',
        1000000000000 => 'trilión',
        1000000000000000 => 'quadrilión',
        1000000000000000000 => 'quintilión'
    );

    if ( ! is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
        // overflow
        throw new CHttpException(500,
            'Invalid number range - value must be between ' . PHP_INT_MAX . ' and ' . PHP_INT_MAX . '.');
    }

    if ($number < 0) {
        return $negative . number2words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $dict = $dictionary[$number];
            if ($units) {
                if ($number == 1) {
                    $dict = ''; // nie jedentisic, jedensto
                } elseif ($number == 2 && $units == 1000) {
                    $dict = 'dve'; // dvetisic, nie dvatisic
                }
            }
            $string = $dict;
            break;
        case $number < 100:
            $tens = ((int)($number / 10)) * 10;
            $units = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds = floor($number / 100);
            $remainder = $number % 100;
            if ($hundreds == 1) {
                $dict = ''; // nie styritisic jedenstoosemdesiat, jedenstopatnast
            } elseif ($hundreds == 2) {
                $dict = 'dve'; // dvesto, nie dvasto
            } else {
                $dict = $dictionary[$hundreds];
            }
            $string = $dict . $dictionary[100];
            if ($remainder) {
                $string .= number2words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int)($number / $baseUnit);
            $remainder = $number % $baseUnit;
            // SK declination
            $append = '';
            if ($baseUnit > 1000) {
                $bigNumSep = ' ';
                // nesklonujeme tisicky, len milion a vyssie
                if ($numBaseUnits >= 2 && $numBaseUnits <= 4) {
                    $append = 'y'; // 2, 3, 4 miliony, biliony, triliony, ..
                } elseif ($numBaseUnits >= 5) {
                    $append = 'ov'; // 5,6 ... 99 milionov
                }
            } else {
                $bigNumSep = '';
            }
            $string = number2words($numBaseUnits, $baseUnit) . $bigNumSep . $dictionary[$baseUnit] . $append;
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= number2words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string)$fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

function toSlug($text)
{

    $slug = diakritika($text);
    $slug = str_replace([' ', '_', '/', '(', ')'], '-', $slug);
    $slug = str_replace(['.', ','], '', $slug);
    $slug = strtolower($slug);
    $slug = preg_replace("/\-+/", "-", $slug);
    $slug = trim($slug);
    $slug = trim($slug, "-");

    $slug = preg_replace("/[^a-zA-Z0-9\-]/", '', $slug);
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);

    return $slug;
}

function normalizeNumber($number)
{
    return preg_replace("/[^0-9\.\-]/", '', str_replace([',', ' '], ['.', ''], $number));
}

function similarity($str1, $str2)
{
    $str1 = strtolower($str1);
    $str2 = strtolower($str2);
    $len1 = strlen($str1);
    $len2 = strlen($str2);

    $max = max($len1, $len2);
    $similarity = $i = $j = 0;

    while (($i < $len1) && isset($str2[$j])) {
        if (@$str1[$i] == @$str2[$j]) {
            $similarity++;
            $i++;
            $j++;
        } elseif ($len1 < $len2) {
            $len1++;
            $j++;
        } elseif ($len1 > $len2) {
            $i++;
            $len1--;
        } else {
            $i++;
            $j++;
        }
    }

    return round($similarity / $max, 2);
}

function diakritika($text)
{
    $s = [
        'á',
        'ä',
        'č',
        'ď',
        'é',
        'í',
        'ĺ',
        'ľ',
        'ň',
        'ó',
        'ô',
        'ŕ',
        'ř',
        'š',
        'ť',
        'ú',
        'ý',
        'ž',
        'Á',
        'Č',
        'Ď',
        'É',
        'Í',
        'Ĺ',
        'Ľ',
        'Ň',
        'Ó',
        'Ŕ',
        'Š',
        'Ť',
        'Ú',
        'Ý',
        'Ž'
    ];
    $r = [
        'a',
        'a',
        'c',
        'd',
        'e',
        'i',
        'l',
        'l',
        'n',
        'o',
        'o',
        'r',
        'r',
        's',
        't',
        'u',
        'y',
        'z',
        'A',
        'C',
        'D',
        'E',
        'I',
        'L',
        'L',
        'N',
        'O',
        'R',
        'S',
        'T',
        'U',
        'Y',
        'Z'
    ];

    return str_replace($s, $r, $text);
}

function percent($amount, $percent)
{
    return $amount / 100 * $percent;
}

function calculateAge($dob)
{
    return floor((time() - strtotime($dob)) / 31556926);
}

function getSelectChoices($table, $key, $value, $addEmpty = true)
{
    $all = DB::select("select $key, $value from $table order by $value");
    $return = [];
    if ($addEmpty) {
        $return[''] = '[choose]';
    }
    foreach ($all as $bank) {
        $return[$bank[$key]] = $bank[$value];
    }

    return $return;
}

function deleteDirectory($dir)
{
    if ( ! file_exists($dir)) {
        return true;
    }

    if ( ! is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if ( ! deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }

    }

    return rmdir($dir);
}

function string($string, $label = '')
{
    if (trim($string) == '') {
        throw new \Exception("String is empty $label");
    }

    return $string;
}

function notEmptyNumber($number, $label = '')
{
    if (empty($number)) {
        throw new \Exception("Number is empty $label:" . print_r($number, true));
    }
    if ($number == 0.00) {
        throw new \Exception("Number is empty $label:" . print_r($number, true));
    }
    if ( ! is_numeric($number)) {
        throw new \Exception("Number is not numeric $label:" . print_r($number, true));
    }

    return $number;
}

function notEmptyDate($date, $label = '')
{
    if (empty($date) || preg_match("/0000/", $date)) {
        throw new \Exception("Date is empty: $date, $label");
    }

    return $date;
}

function notEmptyString($value, $label = '')
{
    if (empty($value)) {
        throw new \Exception("string is empty: $value, $label");
    }

    return $value;
}

function notEmptyConfig($key)
{
    $value = config($key);
    if ($value == '') {
        throw new \Exception("Empty config: $key");
    }

    return $value;
}

function vincentyGreatCircleDistance(
    $latitudeFrom,
    $longitudeFrom,
    $latitudeTo,
    $longitudeTo,
    $earthRadius = 6371000
) {
    // convert from degrees to radians
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);

    $lonDelta = $lonTo - $lonFrom;
    $a = pow(cos($latTo) * sin($lonDelta), 2) +
        pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
    $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

    $angle = atan2(sqrt($a), $b);

    return $angle * $earthRadius;
}

function distance($lat1, $lon1, $lat2, $lon2)
{

    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;

    return ($miles * 1.609344) * 1000;
}

function addToLatLng($dx, $dy, $lat, $lng)
{
    //Earth’s radius, sphere
    $R = 6378137;

    //offsets in meters
    $Pi = pi();
    //Coordinate offsets in radians
    $dLat = $dy / $R;
    $dLon = $dx / ($R * cos($Pi * $lat / 180));
    //OffsetPosition, decimal degrees
    $latO = $lat + $dLat * 180 / $Pi;
    $lonO = $lng + $dLon * 180 / $Pi;

    return [$latO, $lonO];
}

;

function convert102067ToLatLng($x, $y)
{
//    proj4.defs('EPSG:4326', '+proj=longlat +datum=WGS84 +no_defs');
//    proj4.defs('EPSG:3857',
//        '+proj=merc +a=6378137 +b=6378137 +lat_ts=0.0 +lon_0=0.0 +x_0=0.0 +y_0=0 +k=1.0 +units=m +nadgrids=@null +wktext  +no_defs');
//    proj4.defs('EPSG:5514',
//        '+proj=krovak +lat_0=49.5 +lon_0=24.83333333333333 +alpha=0 +k=0.9999 +x_0=0 +y_0=0 +ellps=bessel +towgs84=570.8,85.7,462.8,4.998,1.587,5.261,3.56 +units=m +no_defs');
    $proj4 = new proj4php\Proj4php();

// Create two different projections.
    $proj = new proj4php\Proj('EPSG:5514', $proj4);
    $projWGS84 = new proj4php\Proj('EPSG:4326', $proj4);

// Create a point.
    $pointSrc = new proj4php\Point($x, $y, $proj);

// Transform the point between datums.
    $pointDest = $proj4->transform($projWGS84, $pointSrc);
    $array = $pointDest->toArray();

    return [$array[1], $array[0]];
}

function convert102100ToLatLng($x, $y)
{
    $proj4 = new proj4php\Proj4php();

// Create two different projections.
    $proj = new proj4php\Proj('EPSG:3857', $proj4);
    $projWGS84 = new proj4php\Proj('EPSG:4326', $proj4);

// Create a point.
    $pointSrc = new proj4php\Point($x, $y, $proj);

// Transform the point between datums.
    $pointDest = $proj4->transform($projWGS84, $pointSrc);
    $array = $pointDest->toArray();

    return [$array[1], $array[0]];
}

/**
 * @return \App\Cities\City
 */
function city()
{
    $city = config('app.city');
    $className = "\\App\\Cities\\$city\\$city";

    return resolve($className);
}

function cities()
{
    return [
        \App\Cities\Banskabystrica\Banskabystrica::class,
        \App\Cities\Hlohovec\Hlohovec::class,
        \App\Cities\Poprad\Poprad::class,
        \App\Cities\Presov\Presov::class,
//        \App\Cities\Trencin\Trencin::class,
        \App\Cities\Zilina\Zilina::class
    ];
}

/**
 * @return \App\Cities\Presov\Presov
 */
function presov()
{
    return resolve(\App\Cities\Presov\Presov::class);
}

/**
 * @return \App\Cities\Poprad\Poprad
 */
function poprad()
{
    return resolve(\App\Cities\Poprad\Poprad::class);
}

function isPresov()
{
    return city() instanceof \App\Cities\Presov\Presov;
}

function isPoprad()
{
    return city() instanceof \App\Cities\Poprad\Poprad;
}

function cadasters(): \Illuminate\Support\Collection
{
    /** @var \Illuminate\Support\Collection $list */
    $list = \App\CadasterNumber::orderBy('city')->get()->pluck('city', 'id');
    $list->prepend('[neznáme]', '');

    return $list;
}

function cookieId()
{
    if (request()->cookie_id) {
        return request()->cookie_id;
    }
    if (request()->hasCookie('cookie_id')) {
        return request()->cookie('cookie_id');
    }
}