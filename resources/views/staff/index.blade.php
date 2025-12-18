@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mt-6 mb-6">
        <h2 class="text-2xl font-semibold">Medical Staff</h2>
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('staff.index') }}" class="flex items-center">
                <input name="q" value="{{ request('q') }}" placeholder="Search staff..."
                    class="px-3 py-2 border rounded-l text-sm" />
                <button class="px-3 py-2 bg-slate-200 border rounded-r text-sm text-slate-900 dark:text-white dark:bg-slate-700">Search</button>
            </form>
            <a href="{{ route('staff.create') }}"
                class="inline-flex items-center gap-2 px-3 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700">
                <i class="fa-solid fa-plus"></i> Add Staff</a>
        </div>
    </div>

    <!-- <div class="overflow-x-auto bg-white dark:bg-slate-800 rounded shadow">
            <table class="w-full table-auto">
                <thead class="text-sm text-slate-500">
                    <tr class="border-b"> -->
    <div class="overflow-hidden rounded-lg border shadow">
        <table class="min-w-full divide-y">
            <thead class="bg-slate-50 dark:bg-slate-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($staff as $s)
                    <tr class="border-b hover:bg-slate-50 dark:hover:bg-slate-800">
                        <td class="px-4 py-3">{{ $s->id }}</td>
                        <td class="px-4 py-3">{{ $s->first_name }} {{ $s->last_name }}</td>
                        <td class="px-4 py-3">{{ $s->role }}</td>
                        <td class="px-4 py-3">{{ $s->email ?? $s->phone }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('staff.show', $s->id) }}" class="inline-flex items-center gap-2 px-2 py-1 rounded border border-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 text-sm mr-2"><i class="fa-regular fa-eye"></i> View</a>
                            <a href="{{ route('staff.edit', $s->id) }}" class="inline-flex items-center gap-2 px-2 py-1 bg-slate-200 text-slate-900 rounded text-sm mr-2"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                            <form method="POST" action="{{ route('staff.destroy', $s->id) }}" onsubmit="return confirm('Delete?');" class="inline">
                                @csrf @method('DELETE')
                                <button class="px-2 py-1 bg-red-600 text-white rounded text-sm"><i class="fa-solid fa-trash"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $staff->appends(request()->only('q'))->links() }}
    </div>

@endsection