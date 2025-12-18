@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto mt-6 bg-white dark:bg-slate-800 p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Edit Diagnosis #{{ $diagnosis->id }}</h2>

        <form method="POST" action="{{ route('diagnoses.update', $diagnosis->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Patient</label>
                    <div class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900">
                        {{ optional($diagnosis->patient)->first_name }} {{ optional($diagnosis->patient)->last_name }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Doctor</label>
                    <select name="doctor_id" class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900">
                        <option value="">--</option>
                        @foreach(\App\Models\User::where('role', 'doctor')->get() as $s)
                            <option value="{{ $s->id }}" {{ $diagnosis->doctor_id == $s->id ? 'selected' : '' }}>
                                {{ $s->first_name ?? $s->name }} {{ $s->last_name ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Disease Type</label>
                    <input name="disease_type" value="{{ $diagnosis->disease_type }}"
                        class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Report</label>
                    <textarea name="report"
                        class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900">{{ $diagnosis->report }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Confidence (%)</label>
                    <input name="confidence" value="{{ $diagnosis->confidence }}"
                        class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900" type="number" step="0.01">
                </div>


            </div>

            <div class="mt-4 flex justify-end">
                <button class="px-4 py-2 bg-emerald-600 text-white rounded">Save</button>
            </div>
        </form>
    </div>

@endsection