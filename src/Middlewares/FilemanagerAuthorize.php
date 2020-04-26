<?php

namespace SpaceCode\Maia\Middlewares;

use Closure;
use Illuminate\Http\Request;
use SpaceCode\Maia\Tools\FilemanagerTool;
use Symfony\Component\HttpFoundation\Response;

class FilemanagerAuthorize
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return Response|void
     */
    public function handle(Request $request, Closure $next)
    {
        return app(FilemanagerTool::class)->authorize($request) ? $next($request) : abort(403);
    }
}
