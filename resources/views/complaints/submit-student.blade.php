@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Submit Complaint (Student)') }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('complaints.submit') }}">
                        @csrf
                        <div class="row mb-3">
                            <label for="college_id" class="col-md-4 col-form-label text-md-end">{{ __('College') }}</label>
                            <div class="col-md-6">
                                <select id="college_id" class="form-control @error('college_id') is-invalid @enderror" name="college_id" required>
                                    <option value="">{{ __('Select a College') }}</option>
                                    @foreach ($colleges as $college)
                                        <option value="{{ $college->id }}" {{ old('college_id', Auth::user()->college_id) == $college->id ? 'selected' : '' }}>{{ $college->name }}</option>
                                    @endforeach
                                </select>
                                @error('college_id')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="category" class="col-md-4 col-form-label text-md-end">{{ __('Category') }}</label>
                            <div class="col-md-6">
                                <select id="category" class="form-control @error('category') is-invalid @enderror" name="category" required>
                                    <option value="">{{ __('Select a Category') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="complaint_text" class="col-md-4 col-form-label text-md-end">{{ __('Complaint') }}</label>
                            <div class="col-md-6">
                                <textarea id="complaint_text" class="form-control @error('complaint_text') is-invalid @enderror" name="complaint_text" rows="5" required>{{ old('complaint_text') }}</textarea>
                                @error('complaint_text')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection