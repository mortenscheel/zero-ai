<?php

namespace App\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class CodenameLogoCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'codename-logo {codename}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Generate a logo from a codename';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $codename = $this->argument('codename');
        $prompt = <<<EOF
Create a stylized poster that represents "$codename" in the style of old Soviet propaganda posters.
EOF;
        $filename = Str::slug($codename).'.png';
        Artisan::call("image $prompt --filename=$filename", outputBuffer: $this->output);

        return self::SUCCESS;
    }
}
