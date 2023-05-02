<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
use OpenAI\Client;

class Image extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'image {prompt*} {--size=256} {--print}';

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
            'size' => sprintf('%dx%d', $this->option('size'), $this->option('size')),
            'response_format' => 'url',
        ]);
        foreach ($response->data as $data) {
            $url = $data->url;
            $data = file_get_contents($url);
            $filename = sprintf('%s.png', Str::slug($prompt));
            File::put(getcwd()."/$filename", $data);
            if ($this->option('print')) {
                Process::run(['imgcat', '--width=24', '--height=24', $filename]);
            } else {
                $this->info($filename);
            }
        }
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
