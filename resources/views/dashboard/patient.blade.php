@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-semibold mb-4">Patient Dashboard</h1>

        <div class="bg-white p-4 shadow rounded">
            <h2 class="font-semibold mb-2">Your Records</h2>
            <p class="text-sm">Contact reception or your doctor to view more details.</p>
        </div>

        <div class="bg-white p-4 shadow rounded mt-4">
            <h2 class="font-semibold mb-2">Recent Diagnoses</h2>
            <ul>
                @foreach($recentDiagnoses as $d)
                    <li class="text-sm">{{ $d->disease ?? 'Diagnosis' }} — {{ optional($d->doctor)->first_name ?? 'Unknown' }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection