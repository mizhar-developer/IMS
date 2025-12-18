@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6 mt-6">
        <h2 class="text-2xl font-semibold">Patients</h2>
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('patients.index') }}" class="flex items-center">
                <input name="q" value="{{ request('q') }}" placeholder="Search patients..."
                    class="px-3 py-2 border rounded-l text-sm" />
                <button
                    class="px-3 py-2 bg-slate-200 border rounded-r text-sm text-slate-900 dark:text-white dark:bg-slate-700">Search</button>
            </form>
            <a href="{{ route('patients.create') }}"
                class="inline-flex items-center gap-2 px-3 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700">
                <i class="fa-solid fa-plus"></i> Add Patient</a>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border shadow">
        <table class="min-w-full divide-y">
            <thead class="bg-slate-50 dark:bg-slate-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-900 divide-y">
                @foreach($patients as $p)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <td class="px-6 py-4 text-sm">{{ $p->id }}</td>
                        <td class="px-6 py-4 text-sm">{{ $p->first_name }} {{ $p->last_name }}</td>
                        <td class="px-6 py-4 text-sm">{{ $p->email ?? $p->phone }}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('patients.show', $p->id) }}"
                                class="inline-flex items-center gap-2 px-2 py-1 rounded border border-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 dark:border-slate-700 dark:hover:text-white"><i
                                    class="fa-regular fa-eye"></i> View</a>
                            <a href="{{ route('patients.edit', $p->id) }}"
                                class="inline-flex items-center gap-2 px-2 py-1 bg-slate-200 text-slate-900 rounded text-sm">
                                <i class="fa-solid fa-pen-to-square"></i> Edit</a>
                            <form method="POST" action="{{ route('patients.destroy', $p->id) }}" class="inline"
                                onsubmit="return confirm('Delete patient?');">
                                @csrf @method('DELETE')
                                <button class="px-2 py-1 bg-red-600 text-white rounded text-sm"><i
                                        class="fa-solid fa-trash"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $patients->appends(request()->only('q'))->links() }}</div>

@endsection