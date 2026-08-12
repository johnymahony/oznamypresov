<?php
namespace App;

use App\Mail\MailSender;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\MinorError;

class Log
{

    public static $logFile = 'debug';
    public static $disableQueryLog = false;
    private static $group = null;

    public static function minorError($text)
    {
        self::init();
        if (config('app.env') == 'local') {
            die($text);
        }
        if (config('app.env') == 'testing') {
            throw new \Exception($text);
        }
        $text = "M|" . self::$group . '|Url: ' . url()->current() . '|' . $text;
        Log::debug("MinorError: " . $text);
        MinorError::create([
            'text' => $text,
            'backtrace' => self::getTrace(),
        ]);
    }

    public static function init($force = false)
    {
        if (is_null(self::$group) || $force) {
            self::$group = date('YmdHis') . 'X' . rand(100, 999);
        }
    }

    public static function debug($text, $trace = false)
    {
        if (is_null(self::$group)) {
            self::init();
        }
        self::writelog(self::$logFile, "D|" . self::$group . '|' . $text . ($trace ? self::getTrace() : ''));
    }

    public static function writelog($logfile, $string)
    {
        $path = storage_path().'/logs';
//        if (!file_exists($path)) {
//            $path = config('app.log_path2');
//        }
        if (!file_exists($path)) {
            mkdir($path);
            chmod($path, 0755);
        }
        $path .= '/' . date('Ym');
        if (!file_exists($path)) {
            mkdir($path);
            chmod($path, 0755);
        }
        $path .= '/' . date('d');
        if (!file_exists($path)) {
            mkdir($path);
            chmod($path, 0755);
        }
        $file = $path . '/' . $logfile . '.log';

        $date = date('Y-m-d H:i:s');
        $parts = explode(" ", microtime());
        $micro = number_format(round(array_shift($parts), 3), 3);
        $string = strtr(trim($string), "\n\r\t", '   ');
        $string = preg_replace('/\s+/', ' ', $string);
        if (!$fp = fopen($file, 'a')) {
            trigger_error("Unable to open logfile for writing: " . $file);
            return;
        }
        chmod($file, 0777);
        fwrite($fp, "$date|$micro|$string\n");
        fclose($fp);
    }

    public static function getTrace()
    {
        $r = "BackTrace:\n";
        $t = debug_backtrace();
        array_shift($t);

        foreach ($t as $i) {
            $r .= "|s|" . @$i['file'] . ":" . @$i['line'] . ", func: " . @$i['function'] . ", args: " . preg_replace("/\s+/", ' ', str_replace("\n", '', ''/*print_r($i['args'], true)*/)) . "|e|\n";
        }
        return $r;
        // print_r();
    }

    public static function error($text)
    {
        self::init();

        $text = $text . "\n\n" . self::getTrace();
        $text = "E|" . self::$group . '|Url: ' . url()->current() . '|' . $text;
        self::writelog(self::$logFile, $text);

        if (config('app.env') == 'local') {
            die($text);
        }
        if (config('app.env') == 'testing') {
            throw new \Exception($text);
        }
        self::sendErrorEmail($text);
    }

    /**
     * @param $text
     */
    public static function sendErrorEmail($text)
    {
        $developers = config('app.developers');
        foreach ($developers as $address) {
            try {
                (new MailSender($address, 'error', null, ['text' => $text]))->send();
            } catch (Exception $e) {
                self::writelog(self::$logFile, "E|" . self::$group . '|unable to send error email for ' . $address . ': ' . $e);
            }
        }
    }
}