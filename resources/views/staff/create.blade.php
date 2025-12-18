@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto mt-6 bg-white dark:bg-slate-800 p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">{{ isset(
        $member
    ) ? 'Edit Staff Member' : 'Add Staff Member' }}</h2>

        <form method="POST" action="{{ isset(
        $member
    ) ? route('staff.update', $member->id) : route('staff.store') }}" enctype="multipart/form-data">
            @csrf
            @if(isset($member)) @method('PATCH') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">First Name</label>
                    <input name="first_name" required class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900"
                        value="{{ old('first_name', isset($member) ? $member->first_name : '') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Last Name</label>
                    <input name="last_name" required class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900"
                        value="{{ old('last_name', isset($member) ? $member->last_name : '') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Role</label>
                    <select name="role" class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900">
                        @php $r = old('role', isset($member) ? $member->role : ''); @endphp
                        <option value="admin" {{ $r == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="doctor" {{ $r == 'doctor' ? 'selected' : '' }}>Doctor</option>
                        <option value="radiologist" {{ $r == 'radiologist' ? 'selected' : '' }}>Radiologist</option>
                        <option value="accountant" {{ $r == 'accountant' ? 'selected' : '' }}>Accountant</option>
                        <option value="receptionist" {{ $r == 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                        <option value="manager" {{ $r == 'manager' ? 'selected' : '' }}>Manager</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input name="email" class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900"
                        value="{{ old('email', isset($member) ? $member->email : '') }}">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium mb-1">Phone</label>
                <input name="phone" class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900"
                    value="{{ old('phone', isset($member) ? $member->phone : '') }}">
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium mb-1">Password</label>
                <input name="password" type="password" class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900" />
                <p class="text-xs text-slate-500 mt-1">Leave blank to keep existing password when editing.</p>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium mb-1">Confirm Password</label>
                <input name="password_confirmation" type="password" class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900" />
            </div>

            
            <div class="mt-4">
                <label class="block text-sm font-medium mb-1">Profile picture</label>
                @if(isset($member) && $member->profile_picture)
                    @php
                        try {
                            $pp = Storage::disk('s3')->temporaryUrl($member->profile_picture, now()->addMinutes(10));
                        } catch (\Exception $e) {
                            $pp = Storage::url($member->profile_picture ?? '');
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
                    class="px-4 py-2 bg-emerald-600 text-white rounded">{{ isset($member) ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </div>

@endsection