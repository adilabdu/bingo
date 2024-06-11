<?php

namespace App\Http\Requests;

use App\Models\Game;
use App\Models\GamePlayer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CallBingoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'draw_numbers_cut_off_index' => 'required|integer|min:4',
            'selected_numbers' => 'required|array',
        ];
    }

    /**
     * After validation checks
     */
    protected function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->ensureGameIsActive();
            $this->ensureCartelaBelongsToAuthorizedPlayer();
        });
    }

    private function ensureGameIsActive(): void
    {
        $gameStatus = $this->route('game')->status;
        if ($gameStatus !== Game::STATUS_ACTIVE) {
            $this->validator->errors()->add('game', 'This game is not active');
        }
    }

    private function ensureCartelaBelongsToAuthorizedPlayer(): void
    {
        $game = $this->route('game');
        $cartela = $this->route('cartela');
        $player = Auth::user()->load('player')->player->id;
        $gamePlayer = GamePlayer::where([
            'game_id' => $game->id,
            'player_id' => $player,
        ])->first();

        if ($gamePlayer->cartela_id !== $cartela->id) {
            $this->validator->errors()->add('cartela', 'This cartela does not belong to you in this game');
        }
    }
}
