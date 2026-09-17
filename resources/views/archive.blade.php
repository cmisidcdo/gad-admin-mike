@extends('layouts.page')

@section('page-title', '| Archive')
@section('page-content')

<!-- Left side columns -->
<div class="col-lg-12">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <!-- Sales Card -->
                <div class="col">
                    <div class="card info-card sales-card">
                        <div class="card-body">
                            @livewire('document-archive-table')
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Sales Card -->
        </div>
    </div>
    <!-- End Left side columns -->
</div>
</div>
<!-- End Left side columns -->
@endsection