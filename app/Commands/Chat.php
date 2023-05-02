<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use OpenAI\Client;

class Chat extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'chat';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Chat';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Client $client)
    {
        $messages = [];
        while (true) {
            $prompt = $this->ask('Prompt');
            if (! $prompt) {
                break;
            }
            $messages[] = ['role' => 'user', 'content' => $prompt];
            $stream = $client->chat()->createStreamed([
                'model' => 'gpt-3.5-turbo',
                'messages' => $messages,
            ]);
            /** @var \OpenAI\Responses\Chat\CreateStreamedResponse $response */
            foreach ($stream as $response) {
                foreach ($response->choices as $choice) {
                    $delta = $choice->delta->content;
                    if ($delta) {
                        $this->output->write($delta);
                    }
                }
            }
            $this->output->writeln('');
        }
    }
}
