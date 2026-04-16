<?php

namespace Wilfreedi\AcMen\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Wilfreedi\AcMen\Services\AcMenService;

class AcMenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $method;
    public array $data;
    public string $type;
    public string $token;
    public ?string $url;

    /**
     * Create a new job instance.
     */
    public function __construct(string $method, array $data, string $type, string $token, ?string $url = null)
    {
        $this->method = $method;
        $this->data = $data;
        $this->type = $type;
        $this->token = $token;
        $this->url = $url;
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        $method = $this->method;
        $data = $this->data;
        $type = $this->type;
        $token = $this->token;
        $url = $this->url;

        $acMenService = new AcMenService();
        $acMenService->request($method, $data, $type, $token, $url);
    }
}
