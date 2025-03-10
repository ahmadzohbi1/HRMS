@extends('layouts.master')

@section('title')
    @lang('translation.edit_resource', ['resource' => __('attributes.notification')])
@endsection

@section('plugin-css')

@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.notification.notification')
        @endslot
        @slot('li_2')
            {{ route('templates.notification.index') }}
        @endslot
        @slot('title')
            @lang('translation.edit_resource', ['resource' => __('attributes.notification')])
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
                    <form class="needs-validation" novalidate
                          action="{{ route('templates.notification.update', $notification->id)}}"
                          method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-8">

                                <div class="row mb-4">
                                    <label for="title"
                                           class="col-sm-3 col-form-label">@lang('translation.notification.title')</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="title" name="title"
                                               value="{{ old('title', $notification->title) }}" required>
                                        <div class="valid-feedback">
                                            @lang('validation.good')
                                        </div>
                                        <div class="invalid-feedback">
                                            @lang('validation.required', ['attribute' => __('translation.notification.title')])
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="type"
                                           class="col-sm-3 col-form-label">@lang('translation.notification.type')</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="type" name="type"
                                               value="{{ old('type', $notification->type) }}" required>
                                        <div class="valid-feedback">
                                            @lang('validation.good')
                                        </div>
                                        <div class="invalid-feedback">
                                            @lang('validation.required', ['attribute' => __('translation.notification.type')])
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="body"
                                           class="col-sm-3 col-form-label">@lang('translation.notification.body')</label>
                                    <div class="col-sm-9">
                                        <textarea name="body" id="body" class="form-control" rows="8"
                                                  required>{{ old('body', $notification->body) }}</textarea>
                                        <div class="invalid-feedback">
                                            @lang('validation.required', ['attribute' => __('translation.notification.body')])
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