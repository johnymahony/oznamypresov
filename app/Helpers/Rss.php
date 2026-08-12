<?php
/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 25. 4. 2021
 * Time: 14:25
 */

namespace App\Helpers;


use App\Category;
use App\Console\Commands\Scrapper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Rss
{
    public function createData($categoryLabel, $title, $text, $date)
    {
        $category = Category::firstOrCreate(['name' => $categoryLabel]);
        $exists = Scrapper::exists($category->id, $title, $title, $date);
        if ($exists) {
            return false;
        }

        $link = $this->textToPdf($text, $title);
        $data = [
            'category_id' => $category->id,
            'title' => $title,
            'description' => $title,
            'published_at' => dateNow($date),
            'link' => $link,
            'category_label' => $categoryLabel
        ];

        $data['slug'] = toSlug($data['description']);
        $data['po_id'] = md5(implode('', $data));

        return $data;
    }

    private function textToPdf($text, $title)
    {
        $html = view('rss_pdf', [
            'text' => $text,
            'title' => $title
        ])->render();
        $filename = Str::random() . time() . '.pdf';
        $pdfContent = HtmlToPdf::htmlToPdf($html);
        Storage::disk('public')->put($filename, $pdfContent);

        return url(Storage::url($filename));
    }
}