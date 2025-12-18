@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-semibold mb-4">Accountant Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="p-4 bg-white shadow rounded">Billings: <strong>{{ $counts['billings'] ?? 0 }}</strong></div>
            <div class="p-4 bg-white shadow rounded">Patients: <strong>{{ $counts['patients'] ?? 0 }}</strong></div>
        </div>

        <div class="bg-white p-4 shadow rounded">
            <h2 class="font-semibold mb-2">Recent Billings</h2>
            <p class="text-sm">Use the Reports page to export billing data.</p>
        </div>
    </div>
@endsection