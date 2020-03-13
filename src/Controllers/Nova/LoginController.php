<?php

namespace SpaceCode\Maia\Controllers\Nova;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('maia::auth.login');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
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