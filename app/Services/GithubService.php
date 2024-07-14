<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GithubService {
    private const BASE_URI = 'https://api.github.com/';
    private const ACCEPT_HEADER = 'application/vnd.github.v3+json';

    private Client $client;

    public function __construct() {
        $this->client = new Client([
            'base_uri' => self::BASE_URI,
            'headers' => [
                'Authorization' => 'token ' . config('services.github.token'),
                'Accept' => self::ACCEPT_HEADER,
            ],
        ]);
    }

    public function getUserFollowing(string $username): array {
        try {
            $userData = $this->getUserData($username);
            $followingData = $this->getFollowingData($username);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() == 404) {
                return ['error' => 'User not found'];
            }
        }

        return [
            'user' => $userData ?? null,
            'following' => $followingData ?? null,
        ];
    }

    private function getUserData(string $username): array {
        $response = $this->client->get("users/{$username}");
        return json_decode($response->getBody()->getContents(), true);
    }

    private function getFollowingData(string $username): array {
        $response = $this->client->get("users/{$username}/following");
        return json_decode($response->getBody()->getContents(), true);
    }
}