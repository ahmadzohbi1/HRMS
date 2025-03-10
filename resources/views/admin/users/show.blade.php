@extends('layouts.master')

@section('title')
@lang('translation.resource_info', ['resource' => __('attributes.admins')])
@endsection

@section('css')
<link href="{{ asset('/assets/libs/magnific-popup/magnific-popup.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1')
admins
@endslot
@slot('li_2')
{{ route('admins.index') }}
@endslot
@slot('title')
<td>{{ $user->name }}</td>
@endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">

                    <div class="col-xl-12">
                        <div class="mt-5">
                            <div class="table-responsive">
                                <table class="table-borderle mb-0 table">
                                    <tbody>
                                        <tr>
                                            <th scope="row" style="width: 150px;">Name:</th>
                                            <td>{{ $user->name }}</td>
                                            <th scope="row" style="width: 150px;">Email:</th>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" style="width: 150px;">Role:</th>
                                            <td>{{ $user->roles->pluck('name')->join(', ') ?? 'N/A' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

            </div>
        </div>
        <!-- end card -->
    </div>
</div>
<!-- end row -->
@endsection
@section('script')
<!-- Magnific Popup-->
<script src="{{ URL::asset('/assets/libs/magnific-popup/magnific-popup.min.js') }}"></script>
<!-- Required datatable js -->
<script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>

<script>
    // magnific-popup
    $(".airlineImageLightBox").magnificPopup({
        type: "image",
        closeOnContentClick: !0,
        closeBtnInside: !1,
        fixedContentPos: !0,
        mainClass: "mfp-no-margins mfp-with-zoom",
        image: {
            verticalFit: !0
        },
        zoom: {
            enabled: !0,
            duration: 300
        }
    })
</script>

{{-- datatable init --}}
<script type="text/javascript">
    $(function () {

        // change status modal
        let chanegStatusModal = document.getElementById('chanegStatusModal')
        chanegStatusModal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            let button = event.relatedTarget

            // Update the modal's content.
            document.getElementById('chanegStatusForm').action = button.getAttribute('data-url');
        });

        // on form submit send ajax request
        $(".submit-btn").click(function (e) {
            e.preventDefault();
            let form = $("#chanegStatusForm");
            let url = form.attr('action');
            let data = form.serialize();

            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                success: function (data) {
                    $('#chanegStatusModal').modal('hide');
                    Swal.fire({
                        timer: "1000",
                        text: data.message,
                        icon: "success"
                    }).then(function () {
                        table.draw();
                    });

                },
                error: function (data) {
                    if (data.responseJSON.status === 500) {
                        Swal.fire({
                            timer: "20000",
                            title: data.responseJSON.message,
                            text: data.responseJSON.errors,
                            customClass: "swal-error",
                            icon: "error",
                        })
                    }

                    Swal.fire({
                        timer: "2000",
                        text: data.responseJSON.message,
                        icon: "warning",
                    });
                }
            });
        });
    });
</script>
@endsection