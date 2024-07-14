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

        if ($githubResponse['error'] ?? false) {
            $this->logError($username, $githubResponse['error']);
            return response()->json(['error' => $githubResponse['error']], 400);
        }
    
        $followingLogins = array_map(static function (array $following) {
            return $following['login'];
        }, $githubResponse['following']);
    
        $this->logUserFollowing($githubResponse['user']['login'], $followingLogins);
    
        return response()->json($githubResponse);
    }

    private function logError(string $username, string $error): void {
        Log::Log($username, $error);
    }
    
    private function logUserFollowing(string $username, array $followingLogins): void {
        Log::Log($username, $followingLogins);
    }
}
