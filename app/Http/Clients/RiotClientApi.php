<?php

namespace App\Http\Clients;

use Illuminate\Support\Facades\Http;

class RiotClientApi
{
    public string $base_url;

    public array $headers;

    public bool $is_ok = false;

    public function __construct()
    {
        $lock_path = config('lol.lol_path').'\lockfile';
        if (! file_exists($lock_path)) {
            $this->is_ok = false;

            return;
        }
        $lock_file = fopen($lock_path, 'r');
        $lock_content = fread($lock_file, filesize($lock_path));
        fclose($lock_file);
        $lock_content = explode(':', $lock_content);
        $port = $lock_content[2];
        $protocol = $lock_content[4];
        $password = utf8_decode($lock_content[3]);
        $this->headers = [
            'Authorization' => 'Basic '.base64_encode("riot:$password"),
        ];
        $this->base_url = "$protocol://127.0.0.1:$port";
        try {
            $url = $this->base_url.'/lol-gameflow/v1/availability';
            $response = Http::withHeaders($this->headers)->withoutVerifying()->get($url);
            $this->is_ok = $response->status() == 200 && $response->json()['isAvailable'];
        } catch (\Exception $e) {
            $this->is_ok = false;
        }
    }

    public function get_conversations(): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        $url = $this->base_url.'/lol-chat/v1/conversations';

        return Http::withHeaders($this->headers)->withoutVerifying()->get($url);
    }

    public function post_champ_select_conversation(string $conversation_id, string $message): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        $url = $this->base_url."/lol-chat/v1/conversations/$conversation_id/messages";

        return Http::withHeaders($this->headers)->withoutVerifying()->post($url, [
            'body' => $message,
        ]);
    }

public function post_other_conversation(string $summonerName, string $message): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
{
    $message = str_replace('"', '\\"', $message);

    $url = $this->base_url."/lol-game-client-chat/v1/instant-messages?message=$message&summonerName=$summonerName";

    return Http::withHeaders($this->headers)->withoutVerifying()->post($url);
}

    public function get_friends(): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        $url = $this->base_url.'/lol-chat/v1/friends';

        return Http::withHeaders($this->headers)->withoutVerifying()->get($url);
    }
}
