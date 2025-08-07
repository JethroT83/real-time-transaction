<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewTransaction implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ?array $transaction = null;

    /**
     * Create a new event instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = [
            'id' => $transaction->id,
            'user' => $transaction->user->name,
            'amount' => number_format($transaction->amount, 2),
            'description' => $transaction->description,
            'accountType' => $transaction->accountType->value,
            'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new Channel('transactions');

//        return [
//            //new PrivateChannel('channel-name'),
//            new Channel('transactions.' . $this->transaction->id),
//        ];
    }
}
