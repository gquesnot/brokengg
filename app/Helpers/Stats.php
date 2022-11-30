<?php

namespace App\Helpers;

class Stats
{
    public int $kill_participation = 0;

    public float $kda = 0;

    public float $avg_kills = 0;

    public float $avg_deaths = 0;

    public float $avg_assists = 0;

    public int $game_won = 0;

    public float $win_percent = 0;

    public int $game_lose = 0;

    public int $game_played = 0;

    public function __construct(
        $matches,
    ) {
        $this->game_played = $matches->count();
        if ($this->game_played) {
            $this->game_won = $matches->where('won', true)->count();
            $this->game_lose = $matches->where('won', false)->count();
            $this->kill_participation = round($matches->avg('kill_participation') * 100);
            $this->avg_kills = round($matches->sum('kills') / $this->game_played, 1);
            $this->avg_deaths = round($matches->sum('deaths') / $this->game_played, 1);
            $this->avg_assists = round($matches->sum('assists') / $this->game_played, 1);
            $this->kda = round(($this->avg_kills + $this->avg_assists) / $this->avg_deaths, 1);
            $this->win_percent = $this->game_played > 0 ? round($this->game_won / $this->game_played * 100, 1) : 0;
        }
    }
}
