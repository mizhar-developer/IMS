@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto mt-6 bg-white dark:bg-slate-800 p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-3">Billing</h2>

        <div class="text-sm space-y-3">
            <div><span class="font-medium">Patient ID:</span> {{ $billing->patient_id }}</div>
            <div><span class="font-medium">Amount:</span> ${{ number_format($billing->amount, 2) }}</div>
            <div>
                <span class="font-medium">Items:</span>
                <ul class="mt-2 text-sm">
                    @foreach($billing->items as $it)
                        <li>{{ $it->description }} — {{ $it->quantity }} × {{ number_format($it->unit_price, 2) }} =
                            {{ number_format($it->total, 2) }}
                        </li>
                    @endforeach
                </ul>
            </div>
            @if(isset($billing->adjustment) && floatval($billing->adjustment) !== 0.0)
                <div><strong>Adjustments:</strong> {{ number_format($billing->adjustment, 2) }}</div>
            @endif
            <div><span class="font-medium">Paid:</span> {{ $billing->paid ? 'Yes' : 'No' }}</div>
        </div>
    </div>

@endsection