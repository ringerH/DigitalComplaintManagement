@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Select Role') }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('select-role') }}" id="role-form">
                        @csrf
                        <input type="hidden" name="usertype" id="usertype" value="">
                        
                        <div class="flex flex-wrap justify-center gap-4 mb-3">
                            <!-- Student Card -->
                            <div class="card w-48 cursor-pointer hover:shadow-lg transition-shadow" onclick="selectRole('student')">
                                <div class="card-body text-center">
                                    <img src="{{ asset('images/icons/student-cap.png') }}" alt="Student Icon" class="w-12 h-12 mx-auto mb-2">
                                    <h5 class="card-title text-lg font-semibold">{{ __('Student') }}</h5>
                                </div>
                            </div>
                            <!-- Hospital Patient Card -->
                            <div class="card w-48 cursor-pointer hover:shadow-lg transition-shadow" onclick="selectRole('hospital_patient')">
                                <div class="card-body text-center">
                                    <img src="{{ asset('images/icons/hospital-cross.png') }}" alt="Hospital Patient Icon" class="w-12 h-12 mx-auto mb-2">
                                    <h5 class="card-title text-lg font-semibold">{{ __('Hospital Patient') }}</h5>
                                </div>
                            </div>
                        </div>
                        
                        @error('usertype')
                            <div class="text-center text-red-500 mb-3" role="alert">{{ $message }}</div>
                        @enderror
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectRole(role) {
    document.getElementById('usertype').value = role;
    document.getElementById('role-form').submit();
}
</script>
@endsection