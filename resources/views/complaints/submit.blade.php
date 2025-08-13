@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Submit a Complaint') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('complaints.submit') }}">
                        @csrf
                        <div class="row mb-3">
                            <label for="complainant_name" class="col-12 col-md-4 col-form-label text-md-end">{{ __('Your Name') }}</label>
                            <div class="col-12 col-md-6">
                                <input id="complainant_name" type="text" class="form-control @error('complainant_name') is-invalid @enderror" name="complainant_name" value="{{ Auth::user()->name }}" readonly>
                                @error('complainant_name')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="college_id" class="col-12 col-md-4 col-form-label text-md-end">{{ __('College') }}</label>
                            <div class="col-12 col-md-6">
                                <select id="college_id" class="form-control @error('college_id') is-invalid @enderror" name="college_id" required>
                                    <option value="">Select a College</option>
                                    @foreach ($colleges as $college)
                                        <option value="{{ $college->id }}">{{ $college->name }}</option>
                                    @endforeach
                                </select>
                                @error('college_id')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="complaint_text" class="col-12 col-md-4 col-form-label text-md-end">{{ __('Complaint') }}</label>
                            <div class="col-12 col-md-6">
                                <textarea id="complaint_text" class="form-control @error('complaint_text') is-invalid @enderror" name="complaint_text" rows="5" required>{{ old('complaint_text') }}</textarea>
                                @error('complaint_text')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-12 col-md-6 offset-md-4 text-center">
                                <button type="submit" class="btn btn-primary w-100 w-md-auto">{{ __('Submit Complaint') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection