<?php

namespace App\Helpers;

class Stats
{
    public int $kill_participation = 0;

    public float $kda = 0;

    public int $game_won = 0;

    public float $win_percent = 0;

    public int $game_lose = 0;

    public int $game_played = 0;

    public function __construct(
        $matches,
    ) {
        $total_kda = 0;
        $total_kill_participation = 0;
        $matches->each(function ($match) use (&$total_kda, &$total_kill_participation) {
            $total_kill_participation += $match->kill_participation;
            $total_kda += $match->kda;
            $this->game_won += $match->won;
            $this->game_lose += ! $match->won;
            $this->game_played += 1;
        });

        if ($this->game_played) {
            $this->kda = round($total_kda / $this->game_played, 2);
            $this->win_percent = round($this->game_won / $this->game_played * 100);
            $this->kill_participation = round($total_kill_participation / $this->game_played * 100);
        }
    }
}
