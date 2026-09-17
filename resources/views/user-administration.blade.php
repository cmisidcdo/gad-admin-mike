@extends('layouts.page')

@section('page-title', '| User Administration')
@section('page-content')
<!-- Left side columns -->
<div class="col-lg-12">
    <div class="row">
        <div class="col">
            <div class="card info-card sales-card">
                <div class="card-body">
                    @livewire('user-administration')
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Left side columns -->
@endsection