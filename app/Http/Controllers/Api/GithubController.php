<?php

namespace App\Http\Controllers\Api;

use App\Services\GitHubService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class GithubController extends Controller {
    protected GitHubService $githubService;

    public function __construct(GitHubService $githubService) {
        $this->githubService = $githubService;
    }

    public function getUserFollowing(string $username): JsonResponse {
        $githubResponse = $this->githubService->getUserFollowing($username);

        if ($this->hasError($githubResponse)) {
            return $this->handleErrorResponse($username, $githubResponse['error']);
        }

        $followingData = $githubResponse['following'] ?? [];
        $followingLogins = array_map(static function (array $followingData) {
            return $followingData['login'];
        }, $followingData);

        if (!isset($githubResponse['user']['login'])) {
            return $this->handleUserLoginNotFoundResponse($username);
        }

        $this->logUserFollowing($githubResponse['user']['login'], $followingLogins);

        return response()->json($githubResponse);
    }

    protected function hasError(array $response): bool {
        return $response['error'] ?? false;
    }

    protected function handleErrorResponse(string $username, string $error): JsonResponse {
        Log::error("Error fetching user following for {$username}: {$error}");
        return response()->json(['error' => $error], 400);
    }

    protected function handleUserLoginNotFoundResponse(string $username): JsonResponse {
        Log::error("User login not found in GitHub response for {$username}");
        return response()->json(['error' => 'User login not found'], 400);
    }

    protected function logUserFollowing(string $login, array $followingLogins): void {
        Log::info("User {$login} is following: " . implode(', ', $followingLogins));
    }
}
