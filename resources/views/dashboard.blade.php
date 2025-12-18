@extends('layouts.app')

@section('content')
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white dark:bg-slate-800 p-4 rounded shadow">
                    <div class="text-sm text-slate-500">Patients</div>
                    <div class="text-2xl font-semibold">{{ $counts['patients'] }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 p-4 rounded shadow">
                    <div class="text-sm text-slate-500">Staff</div>
                    <div class="text-2xl font-semibold">{{ $counts['staff'] }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 p-4 rounded shadow">
                    <div class="text-sm text-slate-500">Images</div>
                    <div class="text-2xl font-semibold">{{ $counts['images'] }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 p-4 rounded shadow">
                    <div class="text-sm text-slate-500">Diagnoses</div>
                    <div class="text-2xl font-semibold">{{ $counts['diagnoses'] }}</div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-white dark:bg-slate-800 p-4 rounded shadow">
                    <h4 class="text-sm text-slate-500 mb-2">Images (last 14 days)</h4>
                                        <div class="h-44">
                                            <canvas id="imagesChart" class="w-full h-full"></canvas>
                                        </div>
                </div>

                <div class="bg-white dark:bg-slate-800 p-4 rounded shadow">
                    <h4 class="text-sm text-slate-500 mb-2">Staff by Role</h4>
                                        <div class="h-44">
                                            <canvas id="rolesChart" class="w-full h-full"></canvas>
                                        </div>
                </div>
            </div>
        </div>

        <aside class="bg-white dark:bg-slate-800 p-4 rounded shadow">
            <h3 class="text-lg font-medium mb-3">Recent activity</h3>
            <div class="space-y-4">
                <div>
                    <div class="text-sm font-medium">Recent Images</div>
                    <ul class="text-sm mt-2 space-y-2">
                        @forelse($recentImages as $img)
                            <li class="flex items-center justify-between">
                                <div>{{ $img->type }} — {{ $img->metadata['original_name'] ?? ('#' . $img->id) }}</div>
                                <div class="text-xs text-slate-400">{{ $img->created_at->diffForHumans() }}</div>
                            </li>
                        @empty
                            <li class="text-sm text-slate-500">No recent images</li>
                        @endforelse
                    </ul>
                </div>

                <div>
                    <div class="text-sm font-medium">Recent Diagnoses</div>
                    <ul class="text-sm mt-2 space-y-2">
                        @forelse($recentDiagnoses as $d)
                            <li class="flex items-center justify-between">
                                <div>{{ $d->disease_type }} @if($d->patient) — {{ $d->patient->first_name }} {{ $d->patient->last_name }}@endif</div>
                                <div class="text-xs text-slate-400">{{ $d->created_at->diffForHumans() }}</div>
                            </li>
                        @empty
                            <li class="text-sm text-slate-500">No recent diagnoses</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </aside>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function () {
            const imagesTimeline = @json($imagesTimeline ?? []);
            const roles = @json($staffByRole ?? []);

            // Images chart
            const imagesCtx = document.getElementById('imagesChart').getContext('2d');
            new Chart(imagesCtx, {
                type: 'line',
                data: {
                    labels: Object.keys(imagesTimeline),
                    datasets: [{
                        label: 'Images',
                        data: Object.values(imagesTimeline),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.08)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Roles pie
            const rolesCtx = document.getElementById('rolesChart').getContext('2d');
            new Chart(rolesCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(roles),
                    datasets: [{
                        data: Object.values(roles),
                        backgroundColor: ['#34d399', '#60a5fa', '#f59e0b', '#f87171', '#a78bfa']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        })();
    </script>
@endsection