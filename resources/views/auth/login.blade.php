
<link href="{{ asset('frontend/assets/style.css') }}" rel="stylesheet"/>
<link href="{{ asset('assets/libs/bootstrap/bootstrap.min.css') }}" rel="stylesheet"/>

    <section class='bundles-section'>
        <div class='container py-5'>
            <div class='row m-0'>
                <div class='col-lg-12 text-center'>
                    <h1 class='page-title'>Sign In / Sign Up <br/>
                        {{--                        <span class='text-main'>Sign In / Sign Up</span>--}}
                    </h1>
                </div>
            </div>
            <div class="row justify-content-center mt-4">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="overflow-hidden">
                        <div class="card-body pt-0">
                            <div class="p-2">
                                <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                                    @csrf
                                    @if(request()->has("route"))
                                        <input type="hidden" name="route" value="{{request()->input("route")}}">
                                    @endif
                                    @if(request()->has("token"))
                                        <input type="hidden" name="token" value="{{request()->input("token")}}">
                                    @endif
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input name="email" type="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               value="{{ old('email') }}" id="email"
                                               placeholder="Enter Email" autocomplete="email" autofocus>
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <div
                                                class="input-group auth-pass-inputgroup @error('password') is-invalid @enderror">
                                            <input type="password" name="password"
                                                   class="form-control @error('password') is-invalid @enderror"
                                                   id="userpassword" placeholder="Enter password"
                                                   aria-label="Password" aria-describedby="password-addon">
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-check">
                                        <div class="float-end">
                                            @if (Route::has('password.request'))
                                                <a href="{{ route('password.request') }}" class="text-muted">Forgot
                                                    password?</a>
                                            @endif
                                        </div>
                                        <input class="form-check-input" type="checkbox"
                                               id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">
                                            Remember me
                                        </label>
                                    </div>

                                    <div class="d-grid mt-3">
                                        <button class="my-btn start-now-btn waves-effect waves-light" type="submit">Log
                                            In
                                        </button>
                                    </div>
                                </form>
                                
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>


