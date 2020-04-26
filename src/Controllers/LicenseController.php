<?php

namespace SpaceCode\Maia\Controllers;

use App\License;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LicenseController
{
    public function post(Request $request)
    {
        $data = $request->all();

        // Check Data Email
        if($this->null($data['email'])) {
            return response()->json(['message' => _trans('resources.license.messages.error.email.null')], 422);
        }
        // Check Data Token
        if($this->null($data['token'])) {
            return response()->json(['message' => _trans('resources.license.messages.error.token.null')], 422);
        }
        // Check Data Key
        if($this->null($data['key'])) {
            return response()->json(['message' => _trans('resources.license.messages.error.key.null')], 422);
        }
        $user = User::where('email', $data['email'])->firstOrFail();
        // Check User Isset by Email
        if(!$user) {
            return response()->json(['message' => _trans('resources.license.messages.error.email.invalid', ['email' => $data['email']])], 422);
        }
        // Check User License Token and Data License Token Identity
        if($this->null($user->license_token) || $data['token'] !== $user->license_token) {
            return response()->json(['message' => _trans('resources.license.messages.error.token.invalid', ['token' => $data['license_token']])], 422);
        }
        $license = License::where('key', str_replace("\r\n", '', $data['key']))->firstOrFail();
        // Check License Isset by Key
        if(!$license || str_replace("\r\n", '', $data['key']) !== $license->key) {
            return response()->json(['message' => _trans('resources.license.messages.error.key.invalid')], 422);
        }
        // Check User License Url and Data License Url Identity
        if(!isset($data['url']) || $data['url'] !== $license->name) {
            return response()->json(['message' => _trans('resources.license.messages.error.url.invalid')], 422);
        }
        if($license->expired_at <= Carbon::now()->addWeek()->format('Y-m-d H:i:s')) {
            return response()->json(['expired' => true, 'message' => _trans('resources.license.messages.info.expired', ['date' => $license->expired_at])], 200);
        }
        return response()->json(null, 200);
    }

    public function get()
    {
        $data = [
            'email' => setting('license_email'),
            'token' => setting('license_token'),
            'url' => getenv('APP_URL'),
            'key' => setting('license_key')
        ];
        return response()->json(['data' => $data], 200);
    }

    public function null($data) {
        if(isset($data) && !is_null($data) && $data !== '') {
            return false;
        }
        return true;
    }
}
