@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mt-6">
        <h2 class="text-2xl font-semibold">{{ $patient->first_name }} {{ $patient->last_name }}</h2>
        <div class="flex gap-2">
            <a href="{{ route('diagnoses.create') }}?patient_id={{ $patient->id }}"
                class="px-3 py-1 rounded border hover:bg-slate-50 dark:hover:bg-slate-700 dark:hover:text-white"><i
                    class="fa-solid fa-upload"></i> Add Diagnosis / Upload</a>
            <a href="{{ route('diagnoses.create') }}?patient_id={{ $patient->id }}"
                class="px-3 py-1 rounded border hover:bg-slate-50 dark:hover:bg-slate-700 dark:hover:text-white"><i
                    class="fa-solid fa-notes-medical"></i> Add Diagnosis</a>
            <a href="{{ route('billing.patient', $patient->id) }}" class="px-3 py-1 rounded bg-emerald-600 text-white"><i
                    class="fa-solid fa-file-invoice-dollar"></i> Billing</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <section aria-labelledby="details-heading" class="bg-white dark:bg-slate-800 p-4 rounded shadow">
            <h3 id="details-heading" class="font-medium mb-2">Details</h3>
            <dl class="grid grid-cols-1 gap-2 text-sm">
                <div>
                    <dt class="font-semibold">Profile</dt>
                    <dd>
                        @if($patient->profile_picture)
                            @php
                                try {
                                    $pp = Storage::disk('s3')->temporaryUrl($patient->profile_picture, now()->addMinutes(10));
                                } catch (\Exception $e) {
                                    $pp = Storage::url($patient->profile_picture);
                                }
                            @endphp
                            <img src="{{ $pp }}" alt="profile" class="h-24 w-24 object-cover rounded mb-2">
                        @else
                            <div class="h-24 w-24 bg-slate-100 dark:bg-slate-700 rounded mb-2"></div>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold">Email</dt>
                    <dd>{{ $patient->email }}</dd>
                </div>
                <div>
                    <dt class="font-semibold">Phone</dt>
                    <dd>{{ $patient->phone }}</dd>
                </div>
                <div>
                    <dt class="font-semibold">DOB</dt>
                    <dd>{{ $patient->date_of_birth }}</dd>
                </div>
            </dl>
        </section>

        <section aria-labelledby="images-heading" class="bg-white dark:bg-slate-800 p-4 rounded shadow">
            <h3 id="images-heading" class="font-medium mb-2">Images</h3>
            <ul class="space-y-3">
                @foreach($images as $img)
                    <li class="p-2 border rounded hover:shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold">{{ $img->type }}</div>
                                <div class="text-sm text-slate-500">{{ $img->metadata['original_name'] ?? '' }}</div>
                            </div>
                            <div class="text-xs text-slate-400">{{ $img->created_at }}</div>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="mt-3">{{ $images->links() }}</div>
        </section>
    </div>

    <section class="mt-6 bg-white dark:bg-slate-800 p-4 rounded shadow">
        <h3 class="font-medium">Diagnoses</h3>
        <ul class="divide-y mt-3">
            @foreach($diagnoses as $d)
                <li class="py-3">
                    <div class="flex justify-between">
                        <div>
                            <div class="font-semibold">{{ $d->disease_type }}</div>
                            <div class="text-sm">{{ $d->report }}</div>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <div class="text-xs text-slate-400">By: {{ optional($d->doctor)->first_name }}
                                {{ optional($d->doctor)->last_name }}</div>
                            @if(auth()->user() && in_array(auth()->user()->role, ['doctor', 'radiologist', 'admin']))
                                <form method="POST" action="{{ route('diagnoses.destroy', $d->id) }}"
                                    onsubmit="return confirm('Delete this diagnosis?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-2 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded text-xs">Delete</button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @if($d->images && $d->images->count() > 0)
                        <div class="mt-3">
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($d->images as $img)
                                    @php
                                        try {
                                            $url = Storage::disk('s3')->temporaryUrl($img->s3_path, now()->addMinutes(10));
                                        } catch (\Exception $e) {
                                            $url = Storage::disk('s3')->url($img->s3_path);
                                        }
                                    @endphp
                                    <button data-src="{{ $url }}" class="inline-block rounded overflow-hidden border p-1">
                                        <img src="{{ $url }}" alt="diag image" class="h-20 w-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
        <div class="mt-3">{{ $diagnoses->links() }}</div>
    </section>

@endsection