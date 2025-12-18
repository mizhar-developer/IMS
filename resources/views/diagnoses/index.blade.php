@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mt-6 mb-6">
        <h2 class="text-2xl font-semibold">Diagnoses</h2>
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('diagnoses.index') }}" class="flex items-center">
                <input name="q" value="{{ request('q') }}" placeholder="Search diagnoses..."
                    class="px-3 py-2 border rounded-l text-sm w-64" />
                <button class="px-3 py-2 bg-slate-200 border rounded-r text-sm text-slate-900 dark:text-white  dark:bg-slate-700 ">Search</button>
            </form>
            <a href="{{ route('diagnoses.create') }}"
                class="inline-flex items-center gap-2 px-3 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700">
                <i class="fa-solid fa-notes-medical"></i> Add Diagnosis</a>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border">
        <table class="min-w-full divide-y">
            <thead class="bg-slate-50 dark:bg-slate-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Thumb</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Patient</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Image</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Doctor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-900 divide-y">
                @foreach($diagnoses as $d)
                            @php
                                try {
                                    $first = $d->images->first();
                                    $s3path = optional($first)->s3_path;
                                    $thumbUrl = $s3path ? Storage::disk('s3')->temporaryUrl($s3path, now()->addMinutes(10)) : null;
                                } catch (\Exception $e) {
                                    $first = $d->images->first();
                                    $s3path = optional($first)->s3_path;
                                    $thumbUrl = $s3path ? Storage::disk('s3')->url($s3path) : null;
                                }
                            @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <td class="px-6 py-4 text-sm">
                            @if($thumbUrl)
                                <button data-src="{{ $thumbUrl }}" class="diag-img-thumb inline-block rounded overflow-hidden border border-slate-200 hover:shadow-sm">
                                    <img src="{{ $thumbUrl }}" alt="thumb" class="h-12 w-20 object-cover">
                                </button>
                            @else
                                <div class="h-12 w-20 bg-slate-100 dark:bg-slate-700"></div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm"><a href="{{ route('diagnoses.show', $d->id) }}" class="text-sky-600 hover:underline">{{ $d->id }}</a></td>
                        <td class="px-6 py-4 text-sm">{{ optional($d->patient)->first_name }} {{ optional($d->patient)->last_name }}</td>
                        <td class="px-6 py-4 text-sm">{{ optional($d->images->first())->type }}</td>
                        <td class="px-6 py-4 text-sm">{{ $d->disease_type }}</td>
                        <td class="px-6 py-4 text-sm">{{ optional($d->doctor)->first_name }} {{ optional($d->doctor)->last_name }}</td>
                        <td class="px-6 py-4 text-sm">{{ $d->created_at }}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('diagnoses.show', $d->id) }}" class="inline-flex items-center gap-2 px-2 py-1 rounded border border-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 text-sm"><i class="fa-regular fa-eye"></i> View</a>
                            <a href="{{ route('diagnoses.edit', $d->id) }}" class="inline-flex items-center gap-2 px-2 py-1 rounded border border-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 text-sm ml-2"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                            @if(auth()->user() && in_array(auth()->user()->role, ['doctor','radiologist','admin']))
                                <form method="POST" action="{{ route('diagnoses.destroy', $d->id) }}" class="inline-block ml-2" onsubmit="return confirm('Delete this diagnosis?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-2 px-2 py-1 rounded bg-rose-600 text-white text-sm">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Lightbox modal for diagnosis images -->
    <div id="diag-img-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-60">
        <div class="max-w-4xl max-h-[85vh] p-4">
            <button id="diag-img-modal-close" class="mb-2 text-white text-xl">&times;</button>
            <img id="diag-img-modal-img" src="" alt="Full image" class="w-full h-auto rounded shadow-lg">
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('click', function (e) {
            const t = e.target.closest('.diag-img-thumb');
            if (t) {
                e.preventDefault();
                const src = t.getAttribute('data-src');
                const modal = document.getElementById('diag-img-modal');
                const img = document.getElementById('diag-img-modal-img');
                img.src = src;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
            if (e.target.id === 'diag-img-modal' || e.target.id === 'diag-img-modal-close') {
                const modal = document.getElementById('diag-img-modal');
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                document.getElementById('diag-img-modal-img').src = '';
            }
        });
    </script>
    @endpush

    <div class="mt-4">{{ $diagnoses->appends(request()->only('q'))->links() }}</div>

@endsection