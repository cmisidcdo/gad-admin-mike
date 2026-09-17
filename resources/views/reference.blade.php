@extends('layouts.page')

@section('page-title', '| Reference')
@section('page-content')

<!-- Left side columns -->
<div class="col-lg-12">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <!-- Card -->
                <div class="col">
                    <div class="card info-card sales-card">
                        <div class="card-body">
                            @livewire('document-type-entry')
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Card -->
        </div>
    </div>
    <!-- End Left side columns -->
</div>
</div>
<!-- End Left side columns -->
@endsection