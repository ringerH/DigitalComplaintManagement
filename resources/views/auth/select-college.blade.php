@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Select College') }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('select-college') }}">
                        @csrf
                        <div class="row mb-3">
                            <label for="college_id" class="col-md-4 col-form-label text-md-end">{{ __('College') }}</label>
                            <div class="col-md-6">
                                <select id="college_id" class="form-control @error('college_id') is-invalid @enderror" name="college_id" required>
                                    <option value="">{{ __('Select a College') }}</option>
                                    @foreach ($colleges as $college)
                                        <option value="{{ $college->id }}">{{ $college->name }}</option>
                                    @endforeach
                                </select>
                                @error('college_id')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">{{ __('Continue') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection