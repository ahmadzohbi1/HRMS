@extends('layouts.master')

@section('title')
    Sidebar Dashboard
@endsection

@section('css')
    <!-- Lightbox css -->
    <link href="/assets/libs/magnific-popup/magnific-popup.min.css" rel="stylesheet"
          type="text/css"/>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Coming soon
        @endslot
        @slot('title')
            Coming soon
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-xl-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mini-stats-wid">
                        <div class="card-body">

                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
        </div>
    </div>
    <!-- end row -->
@endsection
@section('script')
    <!-- Chart JS -->
    <script src="/assets/libs/chart-js/chart-js.min.js"></script>
    <!-- Magnific Popup-->
    <script src="/assets/libs/magnific-popup/magnific-popup.min.js"></script>
@endsection
