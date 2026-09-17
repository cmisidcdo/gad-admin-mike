<div class="card col-8 mx-auto">
    <div class="card-body">
        <h5 class="card-title">Change Password</h5>
        <div class="col-12">
            @if(session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
        </div>
        <!-- Horizontal Form -->
        <form wire:submit="changePass({{ Auth::user()->id }})">
            <div class="row mb-3">
                <label for="currentPassword" class="col-sm-2 col-form-label">Current Password</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control @error('currentPassword') is-invalid @enderror" wire:model="currentPassword">
                    @error('currentPassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="newPassword" class="col-sm-2 col-form-label">New Password</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control @error('newPassword') is-invalid @enderror" wire:model="newPassword">
                    @error('newPassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="confirmPassword" class="col-sm-2 col-form-label">Confirm Password</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control @error('confirmPassword') is-invalid @enderror" wire:model="confirmPassword">
                    @error('confirmPassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="text-center">
                <button type="reset" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    Proceed
                    <div wire:loading.delay>
                        <!-- SVG loading spinner -->
                        <img src="{{ asset('assets/img/submit-loader-white.svg') }}" alt="Loading..." style="width: 50px; height: 50px;">
                    </div>
                </button>
            </div>
        </form>
        <!-- End Horizontal Form -->
    </div>
</div>