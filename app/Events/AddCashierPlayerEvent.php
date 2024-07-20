<?php

namespace App\Events;

use App\Models\Cartela;
use App\Models\Game;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AddCashierPlayerEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Game $game;
    public $selectedCartelas;

    public function __construct($game, $selectedCartelas)
    {
        $this->game = $game;
        $this->selectedCartelas = $selectedCartelas;

        Log::info("Event Constructed");
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('cashier-players');
    }

    public function broadcastAs(): string
    {
        return 'cashier-players.' . $this->game->id;
    }
}
