<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'patient_name'  => $this->patient_name,
            'test_code'     => $this->test_code,
            'priority'      => $this->priority,
            'status'        => $this->status,
            'scheduled_at'  => optional($this->scheduled_at)->toIso8601String(),
            'completed_at'  => optional($this->completed_at)->toIso8601String(),
        ];
    }
}
