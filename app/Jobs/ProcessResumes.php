<?php

namespace App\Jobs;

use App\Services\GeminiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessResumes implements ShouldQueue
{
    use Queueable;

    protected $resumeData;

    protected $user_id;

    /**
     * Create a new job instance.
     */
    public function __construct($resumeData, $user_id)
    {
        $this->resumeData = $resumeData;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): array
    {
        $gemini = new GeminiService;

        $file = $this->resumeData['file'];

        $startTime = microtime(true);

        $resume = $gemini->analyzeResume(
            file_get_contents($file->getRealPath()),
            $file->getMimeType(),
            $this->resumeData['category'] ?? null
        );

        $processingTime = round((microtime(true) - $startTime) * 1000);

        $resume['user_id'] = $this->user_id;
        $resume['filename'] = $file->getClientOriginalName();
        $resume['processing_time_ms'] = $processingTime;

        return $resume;
    }
}
