@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-semibold mb-4">Radiologist Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="p-4 bg-white shadow rounded">Images to Review: <strong>{{ $counts['images'] ?? 0 }}</strong></div>
            <div class="p-4 bg-white shadow rounded">Diagnoses: <strong>{{ $counts['diagnoses'] ?? 0 }}</strong></div>
            <div class="p-4 bg-white shadow rounded">Patients: <strong>{{ $counts['patients'] ?? 0 }}</strong></div>
        </div>

        <div class="bg-white p-4 shadow rounded">
            <h2 class="font-semibold mb-2">Recent Images</h2>
            <ul>
                @foreach($recentImages as $img)
                    <li class="text-sm">{{ $img->filename ?? $img->path ?? 'Image' }} —
                        {{ optional($img->uploader)->first_name ?? 'Unknown' }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection