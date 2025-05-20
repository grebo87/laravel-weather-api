<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SearchHistoryResource;
use App\Services\SearchHistoryService;
use Illuminate\Http\Request;

class UserSearchHistoryController extends Controller
{
    protected SearchHistoryService $searchHistoryService;

    public function __construct(SearchHistoryService $searchHistoryService)
    {
        $this->searchHistoryService = $searchHistoryService;
    }

    /**
     * Display a listing of the user's search history.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $limit = $request->query('limit', 10);

        $history = $this->searchHistoryService->getRecentSearches($user, (int)$limit);

        return SearchHistoryResource::collection($history);
    }
}
