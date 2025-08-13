<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ComplaintStatusUpdated extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $complaint;

    public function __construct($complaint)
    {
        $this->complaint = $complaint;
    }

    public function via($notifiable)
    {
        return ['broadcast'];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'complaint_id' => $this->complaint->complaint_id,
            'status' => $this->complaint->status,
            'message' => "Complaint #{$this->complaint->complaint_id} updated to {$this->complaint->status}"
        ]);
    }
}