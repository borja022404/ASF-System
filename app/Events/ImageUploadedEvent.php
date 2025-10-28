<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Report;

class ImageUploadedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $report; // The uploaded report instance

    /**
     * Create a new event instance.
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * The channel this event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        // Public channel so Admin & Vet can listen without login
        return new Channel('image-uploads');
    }

    /**
     * The event name when broadcasted.
     */
    public function broadcastAs(): string
    {
        return 'ImageUploaded';
    }

    /**
     * Format the data sent to the frontend.
     */
    public function broadcastWith(): array
    {
        return [
            'id'          => $this->report->id,
            'images'      => collect($this->report->images ?? [])->map(function ($img) {
                return asset('storage/' . $img);
            })->toArray(), // Multiple image URLs
            'description' => $this->report->description,
            'symptoms'    => $this->report->symptoms ?? [],
            'status'      => $this->report->status,
            'location'    => $this->report->location,
            'latitude'    => $this->report->latitude,
            'longitude'   => $this->report->longitude,
            'created_at'  => $this->report->created_at->toDateTimeString(),
        ];
    }
}
