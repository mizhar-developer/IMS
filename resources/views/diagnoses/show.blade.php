@extends('layouts.app')

@section('content')
    <div class="mt-6 mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-semibold">Diagnosis #{{ $diagnosis->id }}</h2>
        <a href="{{ route('diagnoses.index') }}" class="px-3 py-2 bg-slate-200 rounded text-slate-900 dark:text-white border-slate-200 dark:bg-slate-700 dark:border-slate-600">Back</a>
    </div>

    <div class="rounded-lg border p-6 bg-white dark:bg-slate-900">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p><strong>Patient:</strong> {{ optional($diagnosis->patient)->first_name }} {{ optional($diagnosis->patient)->last_name }}</p>
                <p><strong>Doctor:</strong> {{ optional($diagnosis->doctor)->first_name }} {{ optional($diagnosis->doctor)->last_name }}</p>
                <p><strong>Disease:</strong> {{ $diagnosis->disease_type }}</p>
                <p class="mt-3"><strong>Report:</strong></p>
                <div class="mt-2 text-sm text-slate-700 dark:text-slate-300">{{ $diagnosis->report ?? 'No report text.' }}</div>
            </div>
            <div>
                @if($diagnosis->images && $diagnosis->images->count() > 0)
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($diagnosis->images as $img)
                            @php
                                try {
                                    $url = Storage::disk('s3')->temporaryUrl($img->s3_path, now()->addMinutes(10));
                                } catch (\Exception $e) {
                                    $url = Storage::disk('s3')->url($img->s3_path);
                                }
                            @endphp
                            <button data-src="{{ $url }}" class="diag-thumb inline-block rounded overflow-hidden border p-1">
                                <img src="{{ $url }}" alt="diag image" class="h-32 w-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="text-sm text-slate-500">No image attached to this diagnosis.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-6">
        <h3 class="text-lg font-semibold">Comments & Notes</h3>
        <!-- <div class="mt-3">
            @if(session('success'))
                <div class="p-3 bg-emerald-100 text-emerald-800 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif
        </div> -->

        <div class="mt-4">
            @foreach($comments as $c)
                <div class="border-b py-3">
                    <div class="text-sm text-slate-600">
                        <strong>
                            @if($c->user_type === 'staff')
                                {{ optional($c->staff)->first_name }} {{ optional($c->staff)->last_name }} (Staff)
                            @elseif($c->user_type === 'patient')
                                {{ optional($c->patient)->first_name }} {{ optional($c->patient)->last_name }} (Patient)
                            @else
                                User #{{ $c->user_id }}
                            @endif
                        </strong>
                        <span class="ml-2 text-xs text-slate-400">{{ $c->created_at }}</span>
                    </div>
                    @if($c->content)
                        <div class="mt-2 text-sm text-slate-700 dark:text-slate-300">{{ $c->content }}</div>
                    @endif
                    @if($c->image)
                        @php
                            try {
                                $imgUrl = Storage::disk('s3')->temporaryUrl($c->image->s3_path, now()->addMinutes(10));
                            } catch (\Exception $e) {
                                $imgUrl = Storage::disk('s3')->url($c->image->s3_path);
                            }
                        @endphp
                        <div class="mt-2">
                            <button data-src="{{ $imgUrl }}" class="diag-comment-thumb inline-block rounded overflow-hidden border border-slate-200 hover:shadow-sm">
                                <img src="{{ $imgUrl }}" alt="comment image" class="h-20 w-32 object-cover">
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            <form method="POST" action="{{ route('diagnoses.comments.store', $diagnosis->id) }}" enctype="multipart/form-data">
                @csrf
                <div>
                    <label class="block text-sm font-medium">Add note / comment</label>
                    <textarea name="content" rows="4" class="mt-1 block w-full rounded border px-3 py-2 text-sm"></textarea>
                </div>
                <div class="mt-2">
                    <label class="block text-sm font-medium">Attach image (optional)</label>
                    <input type="file" name="file" class="mt-1" />
                </div>
                <div class="mt-3">
                    <button class="px-3 py-2 bg-emerald-600 text-white rounded">Save Comment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- lightbox for comment images -->
    <div id="diag-comment-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-60">
        <div class="max-w-4xl max-h-[85vh] p-4">
            <button id="diag-comment-modal-close" class="mb-2 text-white text-xl">&times;</button>
            <img id="diag-comment-modal-img" src="" alt="Full image" class="w-full h-auto rounded shadow-lg">
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('click', function (e) {
            const t = e.target.closest('.diag-comment-thumb, .diag-thumb');
            if (t) {
                e.preventDefault();
                const src = t.getAttribute('data-src');
                const modal = document.getElementById('diag-comment-modal');
                const img = document.getElementById('diag-comment-modal-img');
                img.src = src;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
            if (e.target.id === 'diag-comment-modal' || e.target.id === 'diag-comment-modal-close') {
                const modal = document.getElementById('diag-comment-modal');
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                document.getElementById('diag-comment-modal-img').src = '';
            }
        });
    </script>
    @endpush

@endsection
