<?php

namespace SpaceCode\Maia\Controllers;

use SpaceCode\Maia\Requests\ActionRequest;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class ActionController extends Controller {

    /**
     * @param NovaRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(NovaRequest $request) {
        return response()->json([
            'actions' => $request->newResource()->availableActions($request),
            'pivotActions' => [
                'name' => $request->pivotName(),
                'actions' => $request->newResource()->availablePivotActions($request),
            ],
        ]);
    }

    /**
     * @param ActionRequest $request
     * @return mixed
     * @throws \Laravel\Nova\Exceptions\MissingActionHandlerException
     */
    public function store(ActionRequest $request) {

        $request->validateFields();

        return $request->action()->handleRequest($request);
    }
}