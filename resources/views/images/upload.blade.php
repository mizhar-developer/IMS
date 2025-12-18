@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto mt-6 bg-white dark:bg-slate-800 p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Upload Image</h2>

        <form method="POST" action="{{ route('images.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Patient</label>
                    <select name="patient_id" class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900">
                        @foreach(\App\Models\Patient::latest()->limit(50)->get() as $p)
                            <option value="{{ $p->id }}" {{ request('patient_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->first_name }} {{ $p->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Image Type</label>
                    <select name="type" class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900">
                        <option>CT</option>
                        <option>MRI</option>
                        <option>X-ray</option>
                        <option>Ultrasound</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">File</label>
                    <input type="file" name="file" class="w-full" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Uploaded by (staff)</label>
                    <select name="uploaded_by" class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900">
                        <option value="">--</option>
                        @foreach(\App\Models\User::latest()->limit(50)->get() as $s)
                            <option value="{{ $s->id }}">{{ $s->first_name ?? $s->name }} {{ $s->last_name ?? '' }}
                                {{ $s->role ? '(' . $s->role . ')' : '' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button class="px-4 py-2 bg-emerald-600 text-white rounded">Upload</button>
            </div>
        </form>
    </div>

@endsection