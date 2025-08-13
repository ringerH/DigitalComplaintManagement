<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Complaint;

class ComplaintSubmitted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $complaint;

    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
    }

    public function broadcastOn()
    {
        return new Channel('admin-dashboard');
    }

    public function broadcastWith()
    {
        return [
            'complaint_id' => $this->complaint->complaint_id,
            'complainant_name' => $this->complaint->complainant_name,
            'college' => $this->complaint->college->name,
            'submitted_by' => $this->complaint->user ? $this->complaint->user->name : 'Admin',
            'submitted_at' => $this->complaint->submitted_at->format('Y-m-d'),
            'status' => $this->complaint->status,
        ];
    }
}