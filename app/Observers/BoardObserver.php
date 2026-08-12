<?php

namespace App\Observers;

use App\Board;
use App\Category;
use App\Geo\Geocoder;
use App\Log;
use App\StreetName;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 6. 8. 2018
 * Time: 14:31
 */
class BoardObserver
{
    /**
     * @var Geocoder
     */
    private $geocoder;

    public function __construct(Geocoder $geocoder)
    {
        $this->geocoder = $geocoder;
    }

    public function creating(Board $board)
    {
        $board->slug = toSlug($board->title);
        if (empty($board->status)) {
            $board->status = Board::STATUS_NEW;
        }
    }

    public function created(Board $board)
    {
        if ($board->category_id !== city()->podnetyCategoryId()) {
            return;
        }

        if ( ! $this->resolveGeo($board)) {
            $board->update([
                'status' => Board::STATUS_READY,
            ]);
        }
    }

    public function updatedCustom(Board $board, $new, $original)
    {
        if ($board->category_id !== city()->podnetyCategoryId()) {
            return;
        }
        if ($board->points()->count() === 0 || $board->tagged()->count() === 0) {
            $this->resolveGeo($board);
        }
        if ($new['solution_state'] !== $original['solution_state']) {
            $board->update([
                'ready_at' => dateTimeNow(),
                'sent_daily' => false,
            ]);
        }
    }

    /**
     * @param Board $board
     *
     */
    private function resolvePoint(Board $board): bool
    {
        try {
            return $this->geocoder->addressToPoint($board, $board->address);
        } catch (\Exception $e) {
            Log::debug("Problem in geocode: ({$board->id}):" . $e->getMessage());
        }

        return false;
    }

    private function setAsComplete(Board $board)
    {
        $board->update([
            'status' => Board::STATUS_DONE,
            'ready_at' => dateTimeNow()
        ]);
    }

    /**
     * @param Board $board
     */
    private function resolveStreetFromAddress(Board $board): bool
    {
        $address = str_replace([
            'ulica',
            'ul.',
            city()->humanName(),
            strtolower(city()->humanName()),
            city(),
            strtolower(city()),
        ], '', $board->address);
        $address = trim($address, ' ,.-');
        if (empty($address)) {
            return false;
        }
        $exists = StreetName::where('name', $address)->first();
        if (empty($exists)) {
            Log::debug("Unknown street " . $address);

            return false;
        }

        $board->retag([$exists->name]);

        return true;
    }

    private function cleanTextForAddress($text)
    {
        $text = str_replace([
            'Pekný deň',
            'Pekny den',
            'Dobrý deň',
            'Dobry den',
        ], '', $text);

        return $text;
    }

    public function resolveStreetFromText(Board $board)
    {
        $title = $this->cleanTextForAddress($board->title);
        $description = $this->cleanTextForAddress($board->description);
        $allStreets = StreetName::select('name')->pluck('name');
        $streetFound = [];
        foreach ($allStreets as $street) {
            $cut = 2;
            if (strlen($street) < 8) {
                $cut = 1;
            }
            $short = substr($street, 0, $cut * -1);
            if (preg_match("/$short/", $title)) {
                $streetFound[$street] = $street;
                echo "FOUND TITLE ($street) " . $title . "\n";
            } elseif (preg_match("/$short/", $description)) {
                echo "FOUND DESC ($street) " . $description . "\n";
                $streetFound[$street] = $street;
            }
        }
        if ($streetFound) {
            $board->retag(array_values($streetFound));

            return true;
        }

        return false;
    }

    /**
     * @param Board $board
     */
    private function resolveGeo(Board $board): bool
    {
        if (preg_match("/ [0-9]/", $board->address)) {
            if ($this->resolvePoint($board)) {
                $this->setAsComplete($board);

                return true;
            }
        }
        if ($this->resolveStreetFromAddress($board) || $this->resolveStreetFromText($board)) {
            $this->setAsComplete($board);

            return true;
        };

        return false;
    }
}