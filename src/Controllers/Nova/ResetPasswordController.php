<?php

namespace SpaceCode\Maia\Controllers\Nova;

use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Laravel\Nova\Nova;

class ResetPasswordController extends Controller
{
    use ValidatesRequests;
    use ResetsPasswords;

    /**
     * ResetPasswordController constructor.
     */
    public function __construct()
    {
        $this->middleware('nova.guest:'.config('nova.guard'));
    }

    /**
     * @param Request $request
     * @param null $token
     * @return Factory|View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('maia::auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * @return string
     */
    public function redirectPath()
    {
        return Nova::path();
    }

    /**
     * @return mixed
     */
    public function broker()
    {
        return Password::broker(config('nova.passwords'));
    }

    /**
     * @return mixed
     */
    protected function guard()
    {
        return Auth::guard(config('nova.guard'));
    }
}
