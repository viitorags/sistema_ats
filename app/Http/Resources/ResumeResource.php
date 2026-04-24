<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResumeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'filename' => $this->filename,
            'candidate_name' => $this->candidate_name,
            'candidate_email' => $this->candidate_email,
            'candidate_phone' => $this->candidate_phone,
            'score' => $this->score,
            'technical_score' => $this->technical_score,
            'match_score' => $this->match_score,
            'summary' => $this->summary,
            'skills' => $this->skills,
            'category' => $this->category,
            'processing_time_ms' => $this->processing_time_ms,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
