@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto mt-6 bg-white dark:bg-slate-800 p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Create Diagnosis</h2>

        <form method="POST" action="{{ route('diagnoses.store') }}" enctype="multipart/form-data">
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

                {{-- image selection removed: uploads are handled directly in this form --}}

                <div>
                    <label class="block text-sm font-medium mb-1">Doctor</label>
                    <select name="doctor_id" class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900">
                        <option value="">--</option>
                        @foreach(\App\Models\User::where('role', 'doctor')->get() as $s)
                            <option value="{{ $s->id }}">{{ $s->first_name ?? $s->name }} {{ $s->last_name ?? '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Image Type</label>
                    <select name="image_type" class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900">
                        <option value="diagnosis">Diagnosis (generic)</option>
                        <option value="MRI">MRI</option>
                        <option value="CT">CT</option>
                        <option value="X-ray">X-ray</option>
                        <option value="Ultrasound">Ultrasound</option>
                        <option value="PET">PET</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Disease Type</label>
                    <input name="disease_type" class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Report</label>
                    <textarea name="report" class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Confidence (%)</label>
                    <input name="confidence" class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900"
                        type="number" step="0.01">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Attach Images</label>
                    <input type="file" name="images[]" multiple class="w-full" accept="image/*">
                    <p class="text-xs text-slate-500 mt-1">You can upload multiple images (JPEG, PNG, etc.).</p>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button class="px-4 py-2 bg-emerald-600 text-white rounded">Save</button>
            </div>
        </form>
    </div>

@endsection