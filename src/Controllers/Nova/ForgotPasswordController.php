<?php

namespace SpaceCode\Maia\Controllers\Nova;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use ValidatesRequests;
    use SendsPasswordResetEmails;

    /**
     * ForgotPasswordController constructor.
     */
    public function __construct()
    {
        $this->middleware('nova.guest');

        ResetPassword::toMailUsing(function ($notifiable, $token) {
            return (new MailMessage)
                ->subject(trans('maia::resources.forgot.subject'))
                ->line(trans('maia::resources.forgot.receiving'))
                ->action(trans('maia::resources.forgot.reset'), url(config('nova.url').route('nova.password.reset', $token, false)))
                ->line(trans('maia::resources.forgot.no_action'));
        });
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('maia::auth.passwords.email');
    }

    /**
     * @return mixed
     */
    public function broker()
    {
        return Password::broker(config('nova.passwords'));
    }
}
