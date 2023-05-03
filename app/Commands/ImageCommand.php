<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
use OpenAI\Client;

class ImageCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'image {prompt*} {--filename=}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Generate an image';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Client $client)
    {
        $prompt = implode(' ', $this->argument('prompt'));
        $response = $client->images()->create([
            'prompt' => $prompt,
            'n' => 1,
            'size' => '256x256',
            'response_format' => 'url',
        ]);
        foreach ($response->data as $data) {
            $url = $data->url;
            $data = file_get_contents($url);
            $filename = $this->option('filename') ?: sprintf('%s.png', Str::slug($prompt));
            File::put(getcwd()."/$filename", $data);
            $this->info($filename);
        }

        return self::SUCCESS;
    }
}
