<?php

namespace App\Helpers;

class HtmlToPdf
{
    /**
     * @param $html
     *
     * @return bool|string
     * @throws \Exception
     */
    public static function htmlToPdf($html)
    {
        $sign = password_hash(env('ASYNC_BACKEND_PASS') . 'HtmlToPdf', PASSWORD_BCRYPT, [
            'cost' => 8
        ]);

        $req     = new HttpRequest();
        $json    = $req->call("https://asyncwebapi.com/api/Common/Html2Pdf?s=" . $sign, json_encode([
            'html' => $html,
        ]));
        $decoded = json_decode($json);
        if ($decoded === null) {
            throw new \Exception("Invalid JSON in html2pdf:" . $json);
        }

        return base64_decode($decoded->result);
    }
}