@extends('layouts.app')

@section('content')
    <div class="mt-6 mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-semibold">Staff profile</h2>
        <div class="flex items-center gap-2">
            <a href="{{ route('staff.index') }}" class="px-3 py-2 bg-slate-200 rounded text-slate-900 dark:text-white border-slate-200 dark:bg-slate-700 dark:border-slate-600">Back</a>
            <a href="{{ route('staff.edit', $member->id) }}" class="px-3 py-2 bg-amber-500 text-white rounded">Edit</a>
        </div>
    </div>

    <div class="rounded-lg border p-6 bg-white dark:bg-slate-900">
        <h3 class="text-xl font-medium mb-2">{{ $member->first_name }} {{ $member->last_name }}</h3>
        <div class="text-sm text-slate-600 dark:text-slate-500 mb-4">Role: <strong>{{ $member->role }}</strong></div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p><strong>Email:</strong> {{ $member->email ?? '—' }}</p>
                <p><strong>Phone:</strong> {{ $member->phone ?? '—' }}</p>
            </div>
            <div>
                    <p><strong>Notes:</strong></p>
                    <div class="mt-2 text-sm text-slate-700 dark:text-slate-300">{{ $member->notes ?? 'No notes.' }}</div>
                </div>
                <div>
                    <p><strong>Profile</strong></p>
                    @if($member->profile_picture)
                        @php
                            try { $pp = Storage::disk('s3')->temporaryUrl($member->profile_picture, now()->addMinutes(10)); }
                            catch (\Exception $e) { $pp = Storage::url($member->profile_picture); }
                        @endphp
                        <img src="{{ $pp }}" alt="profile" class="h-24 w-24 object-cover rounded">
                    @else
                        <div class="h-24 w-24 bg-slate-100 dark:bg-slate-700 rounded"></div>
                    @endif
            </div>
        </div>
    </div>

    <div class="mt-6">
        <h4 class="text-lg font-semibold mb-2">Uploaded images</h4>
        @if($images && $images->count())
            <div class="overflow-hidden rounded-lg border">
                <table class="min-w-full divide-y">
                    <thead class="bg-slate-50 dark:bg-slate-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Uploaded At</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-900 divide-y">
                        @foreach($images as $img)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800">
                                <td class="px-6 py-4 text-sm">{{ $img->id }}</td>
                                <td class="px-6 py-4 text-sm">{{ optional($img->patient)->first_name }}
                                    {{ optional($img->patient)->last_name }}</td>
                                <td class="px-6 py-4 text-sm">{{ $img->type }}</td>
                                <td class="px-6 py-4 text-sm">{{ $img->created_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $images->links() }}</div>
        @else
            <div class="text-sm text-slate-500">No uploaded images by this staff member.</div>
        @endif
    </div>

@endsection