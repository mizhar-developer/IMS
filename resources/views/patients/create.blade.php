@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto mt-6 bg-white dark:bg-slate-800 p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">{{ isset($patient) ? 'Edit' : 'Add' }} Patient</h2>

        <form method="POST"
            action="{{ isset($patient) ? route('patients.update', $patient->id) : route('patients.store') }}"
            enctype="multipart/form-data">
            @csrf
            @if(isset($patient)) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">First Name</label>
                    <input name="first_name" required class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900"
                        value="{{ $patient->first_name ?? old('first_name') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Last Name</label>
                    <input name="last_name" required class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900"
                        value="{{ $patient->last_name ?? old('last_name') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input name="email" class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900"
                        value="{{ $patient->email ?? old('email') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Phone</label>
                    <input name="phone" class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900"
                        value="{{ $patient->phone ?? old('phone') }}">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium mb-1">Date of Birth</label>
                <input type="date" name="date_of_birth" class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900"
                    value="{{ $patient->date_of_birth ?? old('date_of_birth') }}">
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium mb-1">Notes</label>
                <textarea name="notes"
                    class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900">{{ $patient->notes ?? old('notes') }}</textarea>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium mb-1">Profile picture</label>
                @if(isset($patient) && $patient->profile_picture)
                    @php
                        try {
                            $pp = Storage::disk('s3')->temporaryUrl($patient->profile_picture, now()->addMinutes(10));
                        } catch (\Exception $e) {
                            $pp = Storage::url($patient->profile_picture ?? '');
                        }
                    @endphp
                    @if($pp)
                        <div class="mb-2"><img src="{{ $pp }}" alt="profile" class="h-20 w-20 object-cover rounded"></div>
                    @endif
                @endif
                <input type="file" name="profile_picture" accept="image/*" class="mt-1" />
            </div>

            <div class="mt-4 flex justify-end">
                <button
                    class="px-4 py-2 bg-emerald-600 text-white rounded">{{ isset($patient) ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </div>

@endsection