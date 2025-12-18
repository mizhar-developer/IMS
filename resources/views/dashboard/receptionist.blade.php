@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-semibold mb-4">Receptionist Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="p-4 bg-white shadow rounded">Patients: <strong>{{ $counts['patients'] ?? 0 }}</strong></div>
            <div class="p-4 bg-white shadow rounded">Staff: <strong>{{ $counts['staff'] ?? 0 }}</strong></div>
            <div class="p-4 bg-white shadow rounded">Appointments: <strong>—</strong></div>
        </div>

        <div class="bg-white p-4 shadow rounded">
            <h2 class="font-semibold mb-2">Quick Actions</h2>
            <ul>
                <li class="text-sm"><a href="{{ route('patients.index') }}">Manage Patients</a></li>

            </ul>
        </div>
    </div>
@endsection