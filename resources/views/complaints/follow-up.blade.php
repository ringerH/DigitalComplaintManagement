@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Follow Up on Complaint') }}</div>

                <div class="card-body">
                    <div class="mb-3">
                        <p class="d-block"><strong>Your Ticket:</strong> {{ $complaint->complaint_id }}</p>
                        <p class="d-block"><strong>Status:</strong> {{ $complaint->status }}</p>
                    </div>
                    <form method="POST" action="{{ route('complaints.follow-up') }}">
                        @csrf
                        <input type="hidden" name="complaint_id" value="{{ $complaint->id }}">
                        <div class="row mb-3">
                            <label for="follow_up_note" class="col-12 col-md-4 col-form-label text-md-end">{{ __('Follow-Up Note') }}</label>
                            <div class="col-12 col-md-6">
                                <textarea id="follow_up_note" class="form-control @error('follow_up_note') is-invalid @enderror" name="follow_up_note" rows="5" required>{{ old('follow_up_note') }}</textarea>
                                @error('follow_up_note')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-12 col-md-6 offset-md-4 text-center">
                                <button type="submit" class="btn btn-primary w-100 w-md-auto">{{ __('Submit Follow-Up') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection