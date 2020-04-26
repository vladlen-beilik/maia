@extends('nova::auth.layout')

@section('content')

@include('nova::auth.partials.header')

<form
    class="bg-white shadow rounded-lg p-8 max-w-login mx-auto"
    method="POST"
    action="{{ route('nova.password.email') }}"
>
    {{ csrf_field() }}

    @component('nova::auth.partials.heading')
        {{ _trans('maia::resources.forgot.forgot') }}
    @endcomponent

    @if (session('status'))
    <div class="text-success text-center font-semibold my-3">
        {{ session('status') }}
    </div>
    @endif

    @include('nova::auth.partials.errors')

    <div class="mb-6 {{ $errors->has('email') ? ' has-error' : '' }}">
        <label class="block font-bold mb-2" for="email">{{ _trans('maia::resources.emailaddress') }}</label>
        <input class="form-control form-input form-input-bordered w-full" id="email" type="email" name="email" value="{{ old('email') }}" required>
    </div>

    <button class="w-full btn btn-default btn-primary hover:bg-primary-dark" type="submit">
        {{ _trans('maia::resources.forgot.send') }}
    </button>
</form>
@endsection
