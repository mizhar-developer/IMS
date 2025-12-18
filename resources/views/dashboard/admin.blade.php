@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-semibold mb-4">Admin Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="p-4 bg-white shadow rounded">Total Patients: <strong>{{ $counts['patients'] ?? 0 }}</strong></div>
            <div class="p-4 bg-white shadow rounded">Total Staff: <strong>{{ $counts['staff'] ?? 0 }}</strong></div>
            <div class="p-4 bg-white shadow rounded">Total Images: <strong>{{ $counts['images'] ?? 0 }}</strong></div>
            <div class="p-4 bg-white shadow rounded">Total Diagnoses: <strong>{{ $counts['diagnoses'] ?? 0 }}</strong></div>
            <div class="p-4 bg-white shadow rounded">Billings: <strong>{{ $counts['billings'] ?? 0 }}</strong></div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div class="bg-white p-4 shadow rounded">
                <h2 class="font-semibold mb-2">Staff by Role</h2>
                <ul>
                    @foreach($staffByRole as $role => $num)
                        <li class="text-sm">{{ ucfirst($role) }}: {{ $num }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-white p-4 shadow rounded">
                <h2 class="font-semibold mb-2">Recent Activity</h2>
                <ul>
                    @foreach($recentDiagnoses as $d)
                        <li class="text-sm">{{ $d->disease ?? 'Diagnosis' }} —
                            {{ optional($d->doctor)->first_name ?? 'Unknown' }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection