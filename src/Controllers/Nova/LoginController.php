<?php

namespace SpaceCode\Maia\Controllers\Nova;

use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Nova\Nova;

class LoginController extends Controller
{
    use AuthenticatesUsers, ValidatesRequests;

    /**
     * LoginController constructor.
     */
    public function __construct()
    {
        $this->middleware('nova.guest:'.config('nova.guard'))->except('logout');
    }

    /**
     * @return Factory|View
     */
    public function showLoginForm()
    {
        return view('maia::auth.login');
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect($this->redirectPath());
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
    protected function guard()
    {
        return Auth::guard(config('nova.guard'));
    }
}