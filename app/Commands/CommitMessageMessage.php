<?php

namespace App\Commands;

use Illuminate\Support\Facades\Process;
use LaravelZero\Framework\Commands\Command;
use OpenAI\Client;

class CommitMessageMessage extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'commit-msg';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Generate commit message from diff';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Client $client)
    {
        $diffCommand = Process::run('git diff --staged');
        if ($diffCommand->failed()) {
            $this->error($diffCommand->errorOutput());

            return self::FAILURE;
        }
        $diff = $diffCommand->output();
        $prompt = <<<PROMPT
Given the following git patch file:
    $diff

    ###
    Generate a one-sentence long git commit message.
    Return only the commit message without comments or other text.
PROMPT;
        $response = $client->completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $prompt,
            'temperature' => 0,
            'max_tokens' => 128,
        ]);
        if (! empty($response->choices)) {
            $this->output->writeln(trim($response->choices[0]->text));
        }

        return self::SUCCESS;
    }
}
