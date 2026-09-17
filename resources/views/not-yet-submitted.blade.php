@extends('layouts.page')

@section('page-title', '| Not Yet Submitted')
@section('page-content')
<!-- Left side columns -->
<div class="col-lg-12">
    <div class="row">
        <div class="col">
            <div class="card info-card sales-card">
                <div class="card-body">
                    @livewire('not-yet-submitted')
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Left side columns -->
@endsection
