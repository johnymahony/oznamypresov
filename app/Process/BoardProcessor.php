<?php


namespace App\Process;


use App\Board;
use App\Log;

class BoardProcessor
{
    /**
     * @var string[]
     */
    private $processors;

    public function __construct($processors = null)
    {
        $this->processors = $processors;
        if (empty($this->processors)) {
            $this->processors = [
                resolve(StreetInTitle::class)
            ];
        }
    }

    public function process(Board $board)
    {
        $done = false;
        foreach ($this->processors as $processor) {
            $result = $processor->process($board);
            Log::debug(get_class($processor) . "({$board->id}): $result");
            if ($result) {
                $done = true;
                $board->update([
                    'status' => Board::STATUS_DONE
                ]);
            }
        }
        if (!$done && $board->parcels()->count() === 0) {
            $board->setWholeCity();
        }
    }
}