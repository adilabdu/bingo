<?php

namespace App\Events;

use App\Models\Cartela;
use App\Models\Game;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GameResultEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Game $game;

    public User $winner;

    public array $winningNumbers;

    public Cartela $cartela;

    /**
     * Create a new event instance.
     */
    public function __construct($game, $winner, $winningNumbers, $cartela)
    {
        $this->game = $game;
        $this->winner = $winner;
        $this->winningNumbers = $winningNumbers;
        $this->cartela = $cartela;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('game-result');
    }

    public function broadcastAs(): string
    {
        return 'game-result.' .$this->game->id;
    }
}
