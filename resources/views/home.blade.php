@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-12 px-4">
    <!-- Welcome Section -->
    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-gray-800">{{ Auth::guard('web')->user()->usertype === 'student' ? 'Student' : 'Hospital Patient' }} Dashboard</h1>
        <p class="text-gray-600 mt-2">Welcome, {{ Auth::guard('web')->user()->name }}! Manage your complaints seamlessly.</p>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-8 text-center border border-green-300 shadow-sm animate-fade-in">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-8 text-center border border-red-300 shadow-sm animate-fade-in">
            {{ session('error') }}
        </div>
    @endif

    <!-- Notification Toast -->
    <div id="notification-toast" class="fixed top-4 right-4 bg-green-600 text-white p-4 rounded-lg shadow-lg hidden animate-fade-in">
        <span id="notification-message"></span>
        <button id="close-toast" class="ml-4 text-white font-bold">×</button>
    </div>

    <!-- Complaint Submission Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-10">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Submit a New Complaint</h2>
        <!-- Category Selection -->
        <div class="mb-8">
            <label class="block text-sm font-medium text-gray-700 mb-3 text-center">Select Category</label>
            @if ($categories->isEmpty())
                <div class="bg-yellow-100 text-yellow-700 p-4 rounded-lg text-center">
                    No categories available. Please contact the administrator.
                </div>
            @else
                <div class="flex flex-wrap justify-center gap-4">
                    @foreach ($categories as $category)
                        <div 
                            class="category-card bg-white border border-gray-200 rounded-lg p-4 cursor-pointer transition-all duration-300 hover:shadow-md w-48 text-center {{ old('category_id') == $category->id ? 'selected' : '' }}" 
                            data-category-id="{{ $category->id }}"
                        >
                            <img src="{{ asset('images/icons/categories/' . strtolower(str_replace(' ', '-', $category->name)) . '.png') }}" alt="{{ $category->name }}" class="w-12 h-12 mx-auto mb-3">
                            <h6 class="text-sm font-semibold text-green-800">{{ $category->name }}</h6>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Complaint Form -->
        <form method="POST" action="{{ route('complaints.submit') }}" class="bg-white p-8" id="complaint-form" style="display: none;">
            @csrf
            <input type="hidden" name="category_id" id="category-input" value="{{ old('category_id') }}">
            @error('category_id')
                <p class="text-red-500 text-xs text-center mt-2">{{ $message }}</p>
            @enderror

            <!-- College Selection -->
            <div class="mb-6">
                <label for="college_id" class="block text-sm font-medium text-gray-700 mb-2">College</label>
                <select id="college_id" name="college_id" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" required>
                    <option value="">{{ __('Select a College') }}</option>
                    @foreach ($colleges as $college)
                        <option value="{{ $college->id }}" {{ old('college_id', Auth::user()->college_id) == $college->id ? 'selected' : '' }}>{{ $college->name }}</option>
                    @endforeach
                </select>
                @error('college_id')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dynamic Fields -->
            <div id="dynamic-fields" class="mb-8"></div>

            <!-- Common Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" name="title" id="title" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" value="{{ old('title') }}" required>
                    @error('title')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                    <select name="priority" id="priority" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" required>
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                    @error('priority')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="mb-8">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" rows="4" required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="text-center">
                <button type="submit" class="bg-green-600 text-white py-3 px-8 rounded-lg hover:bg-green-700 transition duration-300 disabled:opacity-50" @if($categories->isEmpty()) disabled @endif>
                    Submit Complaint
                </button>
            </div>
        </form>
    </div>

    <!-- Filter Section -->
    <div class="mb-6">
        <form method="GET" action="{{ route('home') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                <select id="status" name="status" class="form-control w-full rounded-lg border-gray-300 focus:ring-green-500">
                    <option value="">All</option>
                    <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="Resolved" {{ request('status') === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary py-2 px-4 h-fit mt-auto">Filter</button>
        </form>
    </div>

    <!-- Complaints Section -->
    <div id="complaint-history" class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Your Complaints</h2>
        @if($complaints->isEmpty())
            <p class="text-gray-600 text-center py-6">No complaints found.</p>
        @else
            <div class="space-y-4">
                @foreach($complaints as $complaint)
                    <div class="border border-gray-200 rounded-lg p-5 hover:shadow-lg transition duration-300 bg-gray-50">
                        <!-- Complaint Header -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                <span class="text-sm font-medium text-gray-700">Ticket: <a href="#" class="text-green-600 hover:underline">{{ $complaint->complaint_id }}</a></span>
                                <span class="text-sm font-medium text-gray-700">Status: 
                                    <span class="status-text {{ $complaint->status === 'Pending' ? 'text-red-600' : ($complaint->status === 'In Progress' ? 'text-yellow-600' : 'text-green-600') }}"
                                          data-complaint-id="{{ $complaint->complaint_id }}">
                                        {{ $complaint->status }}
                                    </span>
                                </span>
                                <span class="text-sm text-gray-500">Submitted: {{ $complaint->submitted_at->format('M d, Y H:i') }}</span>
                            </div>
                            <button class="toggle-details text-green-600 hover:text-green-800 text-sm font-medium" data-target="details-{{ $complaint->id }}">
                                View Details
                            </button>
                        </div>

                        <!-- Complaint Details (Collapsible) -->
                        <div id="details-{{ $complaint->id }}" class="details-content hidden mt-4 border-t border-gray-200 pt-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $complaint->category }}</h3>
                            <p class="text-gray-600 mb-2"><strong>Title:</strong> {{ $complaint->title }}</p>
                            <p class="text-gray-600 mb-2"><strong>Description:</strong> {{ $complaint->complaint_text }}</p>
                            <p class="text-gray-600 mb-2"><strong>College:</strong> {{ $complaint->college->name }}</p>
                            <p class="text-gray-600 mb-2"><strong>Priority:</strong> {{ ucfirst($complaint->priority) }}</p>
                            @if($complaint->additional_data)
                                <p class="text-gray-600 mb-2"><strong>Additional Details:</strong></p>
                                <ul class="list-disc pl-5">
                                    @foreach($complaint->additional_data as $key => $value)
                                        @if($value)
                                            <li>{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <!-- Follow-Up Button -->
                        @if(in_array($complaint->status, ['Pending', 'In Progress']))
                            <div class="mt-4 text-right">
                                <button class="open-follow-up-modal bg-blue-600 text-white py-1 px-4 rounded-lg hover:bg-blue-700 transition duration-300 text-sm shadow-sm hover:shadow-md" data-complaint-id="{{ $complaint->id }}">
                                    Follow Up
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Follow-Up Modal -->
<div id="follow-up-modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md animate-scale-in">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Submit a Follow-Up</h3>
        <form id="follow-up-form" method="POST" action="{{ route('complaints.follow-up') }}">
            @csrf
            <input type="hidden" name="complaint_id" id="follow-up-complaint-id">
            <div class="mb-4">
                <label for="follow_up_note" class="block text-sm font-medium text-gray-700 mb-2">Your Message</label>
                <textarea name="follow_up_note" id="follow_up_note" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" rows="4" required></textarea>
                @error('follow_up_note')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" class="close-modal bg-gray-300 text-gray-800 py-2 px-4 rounded-lg hover:bg-gray-400 transition duration-300">Cancel</button>
                <button type="submit" class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition duration-300">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
<style>
    .animate-fade-in { animation: fadeIn 0.5s ease-in-out; }
    .animate-scale-in { animation: scaleIn 0.3s ease-in-out; }
    .category-card {
        user-select: none;
        outline: none;
        -webkit-tap-highlight-color: transparent;
        animation: slide-in 0.5s ease-out forwards;
    }
    .category-card:nth-child(1) { animation-delay: 0.1s; }
    .category-card:nth-child(2) { animation-delay: 0.2s; }
    .category-card:nth-child(3) { animation-delay: 0.3s; }
    .category-card:nth-child(4) { animation-delay: 0.4s; }
    .category-card:nth-child(5) { animation-delay: 0.5s; }
    .category-card:focus { outline: none; }
    .category-card.selected {
        border-color: #15803d;
        background-color: #f0fdf4;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    @keyframes slide-in {
        0% { opacity: 0; transform: translateY(10px); }
        100% { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0-rc2/dist/web/pusher.min.js"></script>
<script>
    // Pass old input values to JavaScript
    const oldInput = @json(old());

    // Pusher and Echo Setup
    try {
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env('PUSHER_APP_KEY') }}',
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            forceTLS: true
        });

        window.Echo.private(`user.{{ Auth::guard('web')->id() }}`)
            .notification((notification) => {
                const toast = document.getElementById('notification-toast');
                const message = document.getElementById('notification-message');
                message.textContent = notification.message;
                toast.classList.remove('hidden');
                setTimeout(() => toast.classList.add('hidden'), 5000);

                const statusElement = document.querySelector(`span.status-text[data-complaint-id="${notification.complaint_id}"]`);
                if (statusElement) {
                    statusElement.textContent = notification.status;
                    statusElement.className = `status-text ${notification.status === 'Pending' ? 'text-red-600' : (notification.status === 'In Progress' ? 'text-yellow-600' : 'text-green-600')}`;
                }
            });
    } catch (error) {
        console.error('Echo/Pusher initialization failed:', error);
    }

    // Close Toast
    document.getElementById('close-toast').addEventListener('click', () => {
        document.getElementById('notification-toast').classList.add('hidden');
    });

    // Category Card Selection
    document.querySelectorAll('.category-card').forEach(card => {
        card.addEventListener('click', function () {
            document.querySelectorAll('.category-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('category-input').value = this.dataset.categoryId;
            document.getElementById('complaint-form').style.display = 'block';

            const fieldsDiv = document.getElementById('dynamic-fields');
            fieldsDiv.innerHTML = '';
            const categoryName = this.querySelector('h6').textContent;
            const usertype = '{{ Auth::guard('web')->user()->usertype }}';
            const categories = @json($categories);
            const formFields = @json(isset($formFields) ? $formFields['category_specific'] : []);

            console.log('Selected category name:', categoryName);
            console.log('Available formFields:', formFields);

            // Find the category to ensure we have the correct name
            const category = categories.find(cat => cat.id == this.dataset.categoryId);
            if (!category) {
                console.error('Category not found in categories list:', this.dataset.categoryId);
                return;
            }

            console.log('Category object:', category);

            // Get category-specific fields based on category name
            const categoryFieldsKey = Object.keys(formFields).find(key => key.toLowerCase() === category.name.toLowerCase());
            if (!categoryFieldsKey) {
                console.warn(`No matching fields found for category "${category.name}". Available keys:`, Object.keys(formFields));
                fieldsDiv.innerHTML = `<p class="text-red-500 text-center">No fields available for this category.</p>`;
                return;
            }
            const categoryFields = formFields[categoryFieldsKey] || [];
            console.log('Category fields for', category.name, ':', categoryFields);

            if (categoryFields.length === 0) {
                fieldsDiv.innerHTML = `<p class="text-red-500 text-center">No fields available for this category.</p>`;
                return;
            }

            // Helper function to create input fields
            const createField = (field) => {
                let html = `<div class="grid grid-cols-1 md:grid-cols-2 gap-6">`;
                field.forEach(f => {
                    console.log('Rendering field:', f);
                    html += `
                        <div>
                            <label for="${f.name}" class="block text-sm font-medium text-gray-700 mb-2">${f.label}${f.required !== false ? '' : ' (Optional)'}</label>
                    `;
                    if (f.type === 'select') {
                        html += `
                            <select name="${f.name}" id="${f.name}" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" ${f.required !== false ? 'required' : ''}>
                                <option value="">Select ${f.label}</option>
                        `;
                        for (const [value, label] of Object.entries(f.options)) {
                            const isSelected = oldInput[f.name] == value ? 'selected' : '';
                            html += `<option value="${value}" ${isSelected}>${label}</option>`;
                        }
                        html += `</select>`;
                    } else if (f.type === 'textarea') {
                        const textareaValue = oldInput[f.name] || '';
                        html += `
                            <textarea name="${f.name}" id="${f.name}" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" rows="4" ${f.required !== false ? 'required' : ''}>${textareaValue}</textarea>
                        `;
                    } else {
                        const inputValue = oldInput[f.name] || '';
                        html += `
                            <input type="${f.type}" name="${f.name}" id="${f.name}" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" value="${inputValue}" ${f.required !== false ? 'required' : ''}>
                        `;
                    }
                    html += `
                            @error('${f.name}')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    `;
                });
                html += `</div>`;
                return html;
            };

            fieldsDiv.innerHTML = createField(categoryFields);
        });
    });

    // Trigger initial selection
    @if(old('category_id'))
        document.addEventListener('DOMContentLoaded', () => {
            const oldCategoryCard = document.querySelector(`.category-card[data-category-id="{{ old('category_id') }}"]`);
            if (oldCategoryCard) {
                oldCategoryCard.classList.add('selected');
                oldCategoryCard.click();
            }
        });
    @endif

    // View Details Toggle
    document.querySelectorAll('.toggle-details').forEach(button => {
        button.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const content = document.getElementById(targetId);
            if (content) {
                content.classList.toggle('hidden');
                this.textContent = content.classList.contains('hidden') ? 'View Details' : 'Hide Details';
            } else {
                console.error(`Details element with ID ${targetId} not found.`);
            }
        });
    });

    // Follow-up Modal
    const modal = document.getElementById('follow-up-modal');
    const complaintIdInput = document.getElementById('follow-up-complaint-id');
    const form = document.getElementById('follow-up-form');

    document.querySelectorAll('.open-follow-up-modal').forEach(button => {
        button.addEventListener('click', function () {
            const complaintId = this.getAttribute('data-complaint-id');
            complaintIdInput.value = complaintId;
            modal.classList.remove('hidden');
        });
    });

    document.querySelectorAll('.close-modal').forEach(button => {
        button.addEventListener('click', function () {
            modal.classList.add('hidden');
            form.reset();
        });
    });

    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
            form.reset();
        }
    });
</script>
@endsection