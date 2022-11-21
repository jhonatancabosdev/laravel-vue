@extends('layouts.front.app')

@section('breadcrumb_text', 'Account Login')

@section('body_class', 'login account')

@section('content')

    <div class="container">
        
        <div class="bg-white">
            
            <div class="row justify-content-center py-5">

                <div class="col-md-6 mb-5" id="login-container">

                    <div class="p-5 border border-radius-0 bg-lighter">

                        <h1 class="text-center mb-4 h3">Login to your account</h1>

                        <form action="{{ route('login') }}" method="post" class="form-horizontal">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    class="form-control border-radius-0 {{ $errors->has('email') ? 'is-invalid' : '' }}" 
                                    placeholder="Email" autofocus>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif

                            </div>
                            <div class="form-group">

                                <label for="password">Password</label>
                                <input 
                                    type="password" 
                                    name="password" 
                                    id="password" 
                                    value="{{ old('password') }}"
                                    class="form-control border-radius-0 {{ $errors->has('password') ? 'is-invalid' : '' }}" 
                                    placeholder="******">

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif

                            </div>
                            <div class="row justify-content-end px-3">
                                <button class="btn btn-highlight ml-2 border-radius-0" type="submit">Login</button>
                            </div>
                        </form>
                        <div class="d-flex justify-content-center align-items-center flex-row flex-wrap mt-4 mb-2">
                            <a class="mb-3 btn btn-secondary border-radius-0 mx-1" href="{{route('password.request')}}">I forgot my password</a><br>
                            <button class="mb-3 btn btn-secondary border-radius-0 mx-1 jq-create-account">Create new account</button>
                        </div>

                    </div>
                </div>

                <div class="col-md-7 mb-5" id="register-container">

                    <div class="p-5 border border-radius-0 bg-lighter">

                        <h1 class="text-center mb-4 h3">Create your account</h1>

                        <form action="{{ route('register') }}" method="post" role="form" id="registration-form" class="form-horizontal">
                            
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label for="register_name">Name</label>
                                <input type="text" id="register_name" autofocus name="name" value="{{ old('name') }}" class="form-control border-radius-0 {{ $errors->has('name') ? 'is-invalid' : '' }}" placeholder="Name" autofocus>
                                @if ($errors->has('name'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="new_email">Email</label>
                                <input type="email" id="new_email" name="email" value="{{ old('email') }}" class="form-control border-radius-0 {{ $errors->has('email') ? 'is-invalid' : '' }}" placeholder="example@gmail.com">
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone number</label>
                                <input 
                                    type="phone" 
                                    id="phone" 
                                    name="phone" 
                                    value="{{ old('phone') }}" 
                                    class="form-control border-radius-0 {{ $errors->has('phone') ? 'is-invalid' : '' }}" 
                                    placeholder="Ex: 3105551212">

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif

                            </div>
                            <div class="form-group">
                                <label for="new_password">Password</label>
                                <input id="new_password" type="password" class="form-control border-radius-0 {{ $errors->has('password') ? 'is-invalid' : '' }}" name="password" placeholder="******">
                                 @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="new_password-confirm" class="control-label">Confirm Password</label>
                                <input id="new_password-confirm" type="password" class="form-control border-radius-0 {{ $errors->has('password') ? 'is-invalid' : '' }}" name="password_confirmation" placeholder="******">
                                @if ($errors->has('password_confirmation'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group row">
                                <label for="apply-for-vendor" class="col-md-10 col-form-label text-md-right">Do you want to apply for a vendor account?</label>

                                <div class="col-md-2 px-0">
                                    <input tabindex="0" id="apply-for-vendor" type="checkbox" class="form-control border-radius-0" name="apply_for_vendor" />
                                </div>
                            </div>

                            <div class="row justify-content-end px-3">
                                <button
                                    type="submit"
                                    class="g-recaptcha btn btn-highlight border-radius-0"
                                    data-sitekey="{{ config('recaptcha.key') }}"
                                    data-callback='onSubmit'
                                    data-action='submit'
                                    >
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="https://www.google.com/recaptcha/api.js"></script>
<script>
function onSubmit(token) {
document.getElementById("registration-form").submit();
}
</script>
@endsection

@section('css')

    <style>
        @if(request()->has('register'))
            #login-container{
                display: none;
            }
        @else
            #register-container{
                display: none;
            }
        @endif
    </style>

@endsection

@push('scripts')
    
    <script>
        $(function(){
            $('body').on('click', '.jq-create-account', function(event) {
                event.preventDefault();
                $('#register-container').fadeOut(500, function() {
                    $('#register-container').addClass('was-validated')
                });
                $(this).hide()
                window.location.search = 'register';
            });
            
            if(window.location.hash.includes('apply-for-vendor-account')){
                $('#apply-for-vendor').attr('checked','checked');
            }
        })
    </script>

@endpush
