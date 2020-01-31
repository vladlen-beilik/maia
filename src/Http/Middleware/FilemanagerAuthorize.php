<?php

namespace SpaceCode\Maia\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use SpaceCode\Maia\FilemanagerTool;
use Symfony\Component\HttpFoundation\Response;

class FilemanagerAuthorize
{
    /**
     * @param Request $request
     * @param Closure $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return app(FilemanagerTool::class)->authorize($request) ? $next($request) : abort(403);
    }
}
