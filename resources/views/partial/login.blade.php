<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="flex">
        <label for="email"
               class="w-1/5">{{ __('E-Mail Address') }}</label>

        <div class="w-4/5">
            <input id="email" type="email"
                   class="selectize-input{{ $errors->has('email') ? ' is-invalid' : '' }}"
                   name="email" value="{{ old('email') }}" required autofocus>

            @if ($errors->has('email'))
                <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
            @endif
        </div>
    </div>

    <div class="flex mt-6">
        <label for="email"
               class="w-1/5">{{ __('Password') }}</label>

        <div class="w-4/5">
            <input id="password" type="password"
                   class="selectize-input{{ $errors->has('password') ? ' is-invalid' : '' }}"
                   name="password" required>

            @if ($errors->has('password'))
                <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
            @endif
        </div>
    </div>

    <div class="my-6">
        <div class="col-md-6 offset-md-4">
            <div class="checkbox">
                <label>
                    <input type="checkbox"
                           name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}
                </label>
            </div>
        </div>
    </div>

    <div class="form-group row mb-0">
        <div class="col-md-8 offset-md-4">
            @include('components.button', [
            'type' => 'submit',
            'label' => __('Login')
            ])
            <a class="btn btn-link" href="{{ route('password.request') }}">
                {{ __('Forgot Your Password?') }}
            </a>
        </div>
    </div>
</form>