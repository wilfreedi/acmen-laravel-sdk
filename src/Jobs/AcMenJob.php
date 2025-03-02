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

    public $method, $data, $type;

    /**
     * Create a new job instance.
     */
    public function __construct($method, $data, $type) {
        $this->method = $method;
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        $method = $this->method;
        $data = $this->data;
        $type = $this->type;

        AcMenService::request($method, $data, $type);
    }
}
