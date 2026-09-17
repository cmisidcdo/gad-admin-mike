@extends('layouts.page')

@section('page-title', '| Document Approval')
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
                            @livewire('document-approval')
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Card -->
        </div>
    </div>
</div>
<!-- End Left side columns -->
@endsection
