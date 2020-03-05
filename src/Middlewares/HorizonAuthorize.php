<?php

namespace SpaceCode\Maia\Middlewares;

use SpaceCode\Maia\Tools\NovaHorizonTool;
use Laravel\Nova\Nova;

class HorizonAuthorize
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
//        if ($request->user()->roles->count() > 0) {
//            foreach ($request->user()->roles as $role) {
//                if($role->name === 'developer') {
//                    return true;
//                }
//            }
//        }
        $tool = collect(Nova::registeredTools())->first([$this, 'matchesTool']);
        return optional($tool)->authorize($request) ? $next($request) : abort(403);
    }

    /**
     * Determine whether this tool belongs to the package.
     *
     * @param \Laravel\Nova\Tool $tool
     * @return bool
     */
    public function matchesTool($tool)
    {
        return $tool instanceof NovaHorizonTool;
    }
}
