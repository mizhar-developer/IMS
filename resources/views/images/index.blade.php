@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mt-6 mb-6">
        <h2 class="text-2xl font-semibold">Medical Images</h2>
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('images.index') }}" class="flex items-center">
                <input name="q" value="{{ request('q') }}" placeholder="Search images, patient or uploader..."
                    class="px-3 py-2 border rounded-l text-sm w-64" />
                <button
                    class="px-3 py-2 bg-slate-200 border rounded-r text-sm text-slate-900 dark:text-white dark:bg-slate-700">Search</button>
            </form>
            <a href="{{ route('diagnoses.create') }}?patient_id={{ request('patient_id') }}"
                class="inline-flex items-center gap-2 px-3 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700">
                <i class="fa-solid fa-upload"></i> Create Diagnosis & Upload</a>
        </div>
    </div>

    <div class="text-sm text-slate-500 mb-3">Total images: {{ $images->total() }}</div>

    <div class="overflow-hidden rounded-lg border">
        <table class="min-w-full divide-y">
            <thead class="bg-slate-50 dark:bg-slate-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Thumb</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Patient</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Uploaded By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Uploaded At</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-900 divide-y">
                @foreach($images as $img)
                    @php
                        try {
                            $thumbUrl = $img->s3_path ? Storage::disk('s3')->temporaryUrl($img->s3_path, now()->addMinutes(10)) : null;
                        } catch (\Exception $e) {
                            $thumbUrl = $img->s3_path ? Storage::disk('s3')->url($img->s3_path) : null;
                        }
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <td class="px-6 py-4 text-sm">
                            @if($thumbUrl)
                                <button data-src="{{ $thumbUrl }}"
                                    class="img-thumb inline-block rounded overflow-hidden border border-slate-200 hover:shadow-sm">
                                    <img src="{{ $thumbUrl }}" alt="thumb" class="h-12 w-20 object-cover">
                                </button>
                            @else
                                <div class="h-12 w-20 bg-slate-100 dark:bg-slate-700"></div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $img->id }}</td>
                        <td class="px-6 py-4 text-sm">{{ optional($img->patient)->first_name }}
                            {{ optional($img->patient)->last_name }}
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $img->type }}</td>
                        <td class="px-6 py-4 text-sm">{{ optional($img->uploader)->first_name }}
                            {{ optional($img->uploader)->last_name }}
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $img->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $images->appends(request()->only('q'))->links() }}</div>

    <!-- Lightbox modal -->
    <div id="img-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-60">
        <div class="max-w-4xl max-h-[85vh] p-4">
            <button id="img-modal-close" class="mb-2 text-white text-xl">&times;</button>
            <img id="img-modal-img" src="" alt="Full image" class="w-full h-auto rounded shadow-lg">
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('click', function (e) {
                const t = e.target.closest('.img-thumb');
                if (t) {
                    e.preventDefault();
                    const src = t.getAttribute('data-src');
                    const modal = document.getElementById('img-modal');
                    const img = document.getElementById('img-modal-img');
                    img.src = src;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
                if (e.target.id === 'img-modal' || e.target.id === 'img-modal-close') {
                    const modal = document.getElementById('img-modal');
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                    document.getElementById('img-modal-img').src = '';
                }
            });
        </script>
    @endpush

@endsection