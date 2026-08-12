<?php

namespace App\Adapter;

use App\Category;
use App\Log;
use App\Problem;
use SimpleXMLElement;

class PopradBoardAdapter
{
    public function process()
    {
        $content = file_get_contents('https://egov.poprad.sk/default.aspx?NavigationState=610:0:');
        $content = str_replace("\r\n", '', $content);
        $content = str_replace("\n\r", '', $content);
        $content = str_replace("\n", '', $content);
        $content = str_replace("\t", '', $content);
        $content = str_replace("<br>", '', $content);
        $content = preg_replace("/\s+/", ' ', $content);
        $content = preg_replace("/class=\".*?\"/", ' ', $content);
        $content = substr_replace($content, '', 0, strpos($content, '<tr rId'));
        $content = substr($content, 0, strpos($content, '</tbody'));

        $board_data = [];

        try {
            $xml = new SimpleXmlElement("<table>$content</table>");
        } catch (\Exception $e) {
            Problem::make("Invalid XML in scrapper", null, $content);
            return $board_data;
        }

        foreach ($xml->tr as $row) {
            $poId = (string)$row['rId'];
            $row = $row->td;

            $data = [];
            $data['category_label'] = trim($row[5]);
            $data['description'] = (string)$row[1];
            $data['whole_city'] = false;
            $category = Category::firstOrCreate(['name' => $data['category_label']]);

            $date = date('Y-m-d', strtotime($row[2]));
            $data['category_id'] = $category->id;
            $data['title'] = (string)$row[0];
            $data['published_at'] = $date;
            $data['po_id'] = $poId;
            $data['published_uet_until'] = date('Y-m-d', strtotime($row[3]));
            $data['link'] = 'https://egov.poprad.sk/' . (string)$row[6]->a['href'];
            $data['slug'] = toSlug($data['description']);

            $board_data[] = $data;
        }

        return $board_data;
    }
}
