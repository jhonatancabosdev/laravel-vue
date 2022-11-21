@extends('layouts.front.app')

@section('content')

<div class="container">
    <div class="bg-white">
        <div class="row justify-content-center py-5">
            <div class="col-md-6 mb-5" id="login-container">
                <div class="p-5 border rounded bg-lighter">
                    
                    <h1 class="text-center mb-4 h3">Did you forget your password?</h1>

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form class="form-horizontal" role="form" method="POST" action="{{ route('password.email') }}">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('email') ? ' is-invalid' : '' }}">
                            <label for="email">Your Email</label>
                            <input id="email" type="email" placeholder="Type your email here." class="form-control" name="email" value="{{ old('email') }}" required>
                            @if ($errors->has('email'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-highlight border-radius-0">
                                Request Password Reset Link
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
