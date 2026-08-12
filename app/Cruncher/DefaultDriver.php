<?php

namespace App\Cruncher;

use App\Attachment;
use App\Http\Client;
use App\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DefaultDriver implements Cruncher
{
    /** @var \App\Http\Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->client->configure([
            'base_uri' => config('app.cruncher.url'),
            'auth' => [
                config('app.cruncher.username'),
                config('app.cruncher.password'),
            ],
        ]);
    }

    public function count(): int
    {
        return $this->client->getJson('count?env=' . config('app.cruncher.env'));
    }

    public function get(string $id)
    {
        return $this->client->getJson("get/{$id}?env=" . config('app.cruncher.env'));
    }

    public function search(string $phrase, $fuzzy = false): array
    {
        $params = [
            'highlight' => 'true',
            'env' => config('app.cruncher.env'),
//            'limit' => 100,
//            'skip' => 0
        ];
        if ($fuzzy) {
            $params['fuzzy'] = 'true';
        }

        return $this->client->getJson("search/" . ($phrase) . "?" . http_build_query($params));
    }

    public function all(): array
    {
        return $this->client->getJson('listEnvs?env=' . config('app.cruncher.env'));
    }

    public function clear(): void
    {
        $this->client->post('clear?env=' . config('app.cruncher.env'));
    }

    public function setOCRForEnv()
    {
        return $this->client->post('setOCRForEnv/a40f6827-fec4-4a25-a474-ab58cf6f3421?ocrLanguage=slk');
    }

    public function add(Attachment $file)
    {
        $params = [
            'env' => config('app.cruncher.env'),
//            'name' => Str::random().'.pdf',
            'name' => 'doc',
            'filename' => $file->id . '-' . Str::random() . '.pdf',
        ];
        Log::debug("CruncherUpload " . $file->board_id);
//        dd($params);
        $params['contents'] = Storage::get($file->board->attachment_path);

        return $this->client->upload('add?env=' . config('app.cruncher.env'), $params);
    }

    public function createEnv($name)
    {
        return $this->client->post("registerEnv?name=$name&ocrLanguage=slk");
    }

    public function listEnvs()
    {
        return $this->client->getJson("listEnvs");
    }
}
