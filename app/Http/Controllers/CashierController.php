<?php

namespace App\Http\Controllers;

use App\Events\StartGameEvent;
use App\Models\Cartela;
use App\Models\Cashier;
use App\Models\Game;
use App\Models\GameCategory;
use App\Models\GamePlayer;
use App\Services\DrawGameService;
use App\Services\SettleGameService;
use App\Services\StartGameService;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CashierController extends Controller
{
    public function index()
    {
        return Inertia::render('Game/Cashier/Initiate', [
            'gameCategories' => GameCategory::all()
        ]);
    }

    public function finance()
    {
        return Inertia::render('Cashier/Finance');
    }

    public function createGame(Request $request)
    {
        $gameCategory = GameCategory::find($request->gameCategoryId);

       // Check if there is a pending game where the category is the same and is a tv game
        $game = Game::where('game_category_id', $gameCategory->id)
            ->where('is_tv_game', true)
            ->where('cashier_id', auth()->user()->cashier->id)
            ->whereIn('status', [Game::STATUS_PENDING, Game::STATUS_ACTIVE])
            ->first();

        if (!$game) {
            $game = Game::create([
                'game_category_id' => $gameCategory->id,
                'status' => Game::STATUS_PENDING,
                'is_tv_game' => true,
                'cashier_id' => auth()->user()->cashier->id,
            ]);
        }


        return Inertia::render('Game/Cashier/Add', [
            'gameCategory' => $gameCategory,
            'game' => $game,
            'gamePlayersCount' => $game->players()->count(),
            'selectedCartelas' => $game->cartelas()->get(),
        ]);
    }

    public function addPlayers(Request $request)
    {
        $request->validate([
            'gameId' => 'required|exists:games,id',
            'cartelaName' => 'required|string',
        ]);

        $game = Game::find($request->gameId);
        $cartela = Cartela::where('name', $request->cartelaName)->first();

        // Check if the cartela is already in use
        $cartelaInUse = GamePlayer::where('cartela_id', $cartela->id)
            ->whereRelation('game', function ($query) use ($game) {
                $query->where('id', $game->id);
            })->exists();

        if ($cartelaInUse) {
            return redirect()->back()->withErrors(['cartelaName' => 'Cartela already in use']);
        }
        GamePlayer::create([
            'game_id' => $game->id,
            'cartela_id' => $cartela->id,
            'gamePlayersCount' => $game->players()->count()
        ]);

        $cashier = Cashier::where('user_id', auth()->user()->id)->first();
        $cashier->update(['balance' => $cashier->balance + $game->gameCategory->amount]);

        return redirect()->back()->with('success', 'Player added successfully');
    }

    public function startGame(Request $request, $cartelaName = null, $gameId = null, $gameCategoryId = null)
    {
        $request->validate([
            'game_id' => 'nullable|exists:games,id',
            'game_category_id' => 'nullable|exists:game_categories,id',
        ]);

        $game = Game::whereIn('status', [Game::STATUS_PENDING, Game::STATUS_ACTIVE])
            ->where('is_tv_game', true)
            ->where('cashier_id', auth()->user()->cashier->id)
            ->where('game_category_id', $request->input('game_category_id', $gameCategoryId ))
            ->first();


        $game->load(['players', 'gameCategory']);

        if ($game->status === Game::STATUS_PENDING) {

            $totalPlayers = $game->players()->count();

            // Todo: Change the percentage value to .env variable
            $game->update(['winner_net_amount' => $totalPlayers * (int)$game->gameCategory->amount * config('WINNER_RETAIN_PERCENTAGE', 0.85)]);
            $game->update(['status' => Game::STATUS_ACTIVE]);

            if (!$game->draw_numbers) {
                DrawGameService::drawGame($game->id);
            }
        }

        $batchIndex = $request->input('batch_index', 0);

        return Inertia::render('Game/Cashier/Play', [
            'game' => $game,
            'gamePlayersCount' => $game->players()->count(),
            'nextBatchIndex' => $batchIndex + 1,
            'totalPlayers' => $game->players->count(),
            'drawnNumbers' => $game->draw_numbers,
            'cartela' => Inertia::lazy(function () use ($cartelaName, $gameId) {
                if (!$cartelaName)
                    return null;

                // Return cartela if the cartela is in the game
                $cartela = Cartela::where('name', $cartelaName)->first();
                // Check if is in the game
                $cartelaInGame = GamePlayer::where('cartela_id', $cartela->id)
                    ->where('game_id', $gameId)
                    ->first();

                if (!$cartelaInGame)
                    return null;

                return $cartela;
            })
        ]);
    }

    public function finish(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
        ]);

        $game = Game::find($request->game_id);

        if (!$game->is_tv_game || $game->status !== Game::STATUS_ACTIVE) {
            return redirect()->route('cashier.game.initiate')->with('error', 'Invalid game');
        }

        $game->update(['status' => Game::STATUS_COMPLETED]);

        $cashier = Cashier::where('user_id', auth()->user()->id)->first();
        $cashier->update(['balance' => $cashier->balance - $game->winner_net_amount]);

        return redirect()->route('cashier.game.initiate')->with('success', 'Game completed successfully');
    }


    public function store(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'password' => Hash::make('password'),
            'type' => 'cashier',
        ]);

        $cashier = Cashier::create([
            'user_id' => $user->id,
            'branch_id' => $request->branch_id,
            'balance' => 0,
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($cashier)
            ->log('added a cashier to a branch');

        return redirect()->route('cashier.store')->with('success', 'Cashier created successfully.');

    }
}
