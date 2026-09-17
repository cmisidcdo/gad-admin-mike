@extends('layouts.page')

@section('page-title', '| Dashboard')
@section('page-content')
<!-- Left side columns -->
<div class="col-lg-12">
    <div class="row">
        <!-- Sales Card -->
        <div class="col">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Welcome! <strong>{{ Auth::user()->name }}</strong></span></h5>

                    <div class="d-flex align-items-center justify-content-center mt-5">
                        <img src="{{ asset('assets/img/undraw_city_life_gnpr.svg') }}" alt="File" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
        <!-- End Sales Card -->

    </div>
</div>
<!-- End Left side columns -->
@endsection