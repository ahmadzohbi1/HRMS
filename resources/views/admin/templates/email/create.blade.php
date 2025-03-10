@extends('layouts.master')

@section('title')
    @lang('translation.add_resource', ['resource' => __('attributes.email')])
@endsection

@section('plugin-css')

@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.email.email')
        @endslot
        @slot('li_2')
            {{ route('templates.email.index') }}
        @endslot
        @slot('title')
            @lang('translation.add_resource', ['resource' => __('attributes.email')])
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form class="needs-validation" novalidate action="{{ route('templates.email.store') }}"
                          method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-8">

                                <div class="row mb-4">
                                    <label for="title"
                                           class="col-sm-3 col-form-label">@lang('translation.email.title')</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="title" name="title"
                                               value="{{ old('title') }}" required>
                                        <div class="valid-feedback">
                                            @lang('validation.good')
                                        </div>
                                        <div class="invalid-feedback">
                                            @lang('validation.required', ['attribute' => __('translation.email.title')])
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="type"
                                           class="col-sm-3 col-form-label">@lang('translation.email.type')</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="type" name="type"
                                               value="{{ old('type') }}" required>
                                        <div class="valid-feedback">
                                            @lang('validation.good')
                                        </div>
                                        <div class="invalid-feedback">
                                            @lang('validation.required', ['attribute' => __('translation.email.type')])
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="body"
                                           class="col-sm-3 col-form-label">@lang('translation.email.body')</label>
                                    <div class="col-sm-9">
                                        <textarea name="body" id="body" class="form-control" rows="10"
                                                  required>{{ old('body') }}</textarea>
                                        <div class="invalid-feedback">
                                            @lang('validation.required', ['attribute' => __('translation.email.body')])
                                        </div>
                                    </div>
                                </div>

                                <div class="row justify-content-end">
                                    <div class="col-sm-9">
                                        <div>
                                            <button class="btn btn-primary"
                                                    type="submit">@lang('buttons.submit')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- end card -->
        </div> <!-- end col -->
    </div>
@endsection

@section('script')

@endsection