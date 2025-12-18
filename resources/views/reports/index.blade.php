@extends('layouts.app')

@section('content')
    <div class="mt-6 mb-6">
        <h2 class="text-2xl font-semibold">Reports</h2>
        <p class="text-sm text-slate-500">Generate and export reports by date range.</p>
    </div>

    <form method="get" action="{{ route('reports.index') }}" class="flex items-end gap-3 mb-4">
        <div>
            <label class="block text-sm">Start date</label>
            <input type="date" name="start_date" value="{{ $start ?? '' }}" class="border rounded px-3 h-10">
        </div>
        <div>
            <label class="block text-sm">End date</label>
            <input type="date" name="end_date" value="{{ $end ?? '' }}" class="border rounded px-3 h-10">
        </div>
        <div>
            <button type="submit"
                class="flex items-center h-10 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <i class="fa-solid fa-filter mr-2" aria-hidden="true"></i>Filter
            </button>
        </div>
        <div class="ml-auto flex gap-2">
            <a href="{{ route('reports.export.excel', request()->query()) }}"
                class="flex items-center h-10 px-3 bg-sky-600 hover:bg-sky-700 text-white rounded text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
                <i class="fa-solid fa-file-excel mr-2" aria-hidden="true"></i>Export Excel
            </a>
            <a href="{{ route('reports.export.csv', request()->query()) }}"
                class="flex items-center h-10 px-3 bg-amber-600 hover:bg-amber-700 text-white rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-300">
                <i class="fa-solid fa-file-csv mr-2" aria-hidden="true"></i>Export CSV
            </a>
            <a href="{{ route('reports.export.pdf', request()->query()) }}"
                class="flex items-center h-10 px-3 bg-rose-600 hover:bg-rose-700 text-white rounded text-sm focus:outline-none focus:ring-2 focus:ring-rose-300">
                <i class="fa-solid fa-file-pdf mr-2" aria-hidden="true"></i>Export PDF
            </a>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="rounded-lg border p-4 bg-white dark:bg-slate-900">
            <h3 class="font-medium">Diagnoses</h3>
            <p class="text-2xl font-bold">{{ count($rows ?? []) }}</p>
        </div>
        <div class="rounded-lg border p-4 bg-white dark:bg-slate-900">
            <h3 class="font-medium">Images Uploaded</h3>
            <p class="text-2xl font-bold">{{ $imagesCount ?? 0 }}</p>
        </div>
        <div class="rounded-lg border p-4 bg-white dark:bg-slate-900">
            <h3 class="font-medium">Billing Total</h3>
            <p class="text-2xl font-bold">{{ number_format($billingSum ?? 0, 2) }}</p>
        </div>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-slate-900 border rounded">
        <table class="min-w-full divide-y">
            <thead class="bg-slate-50 dark:bg-slate-800">
                <tr>
                    <th class="p-2 text-left">ID</th>
                    <th class="p-2 text-left">Patient</th>
                    <th class="p-2 text-left">Doctor</th>
                    <th class="p-2 text-left">Disease</th>
                    <th class="p-2 text-left">Created At</th>
                    <th class="p-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $r)
                    <tr class="border-t">
                        <td class="p-2">{{ $r['id'] }}</td>
                        <td class="p-2">{{ $r['patient'] }}</td>
                        <td class="p-2">{{ $r['doctor'] }}</td>
                        <td class="p-2">{{ $r['disease_type'] }}</td>
                        <td class="p-2">{{ $r['created_at'] }}</td>
                        <td class="p-2">
                            @if(auth()->user() && in_array(auth()->user()->role, ['doctor', 'radiologist', 'admin']))
                                <form method="POST" action="{{ route('diagnoses.destroy', $r['id']) }}"
                                    onsubmit="return confirm('Delete this diagnosis?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-2 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded text-xs">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection