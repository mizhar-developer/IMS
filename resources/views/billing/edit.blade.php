@extends('layouts.app')

@section('content')
    <div class="mt-6">
        <h2 class="text-2xl font-semibold">Adjust Invoice #{{ $billing->id }}</h2>

        <div class="mt-4 p-4 bg-white border rounded">
            <p><strong>Patient:</strong> {{ $billing->patient?->first_name }} {{ $billing->patient?->last_name }}</p>
            <p><strong>Current Amount:</strong> {{ number_format($billing->amount, 2) }}</p>
            <p class="mt-2"><strong>Items</strong></p>
            <ul class="text-sm mt-2">
                @foreach($billing->items as $it)
                    <li>{{ $it->description }} — {{ $it->quantity }} × {{ number_format($it->unit_price, 2) }} =
                        {{ number_format($it->total, 2) }}
                    </li>
                @endforeach
            </ul>

            <form action="{{ route('billing.adjust', $billing->id) }}" method="POST" class="mt-4">
                @csrf
                <div>
                    <label class="block text-sm">Adjustment (positive to add, negative to discount)</label>
                    <input type="number" step="0.01" name="adjustment" required class="w-full border p-2 rounded" />
                </div>

                <div class="mt-2">
                    <label class="block text-sm">Notes</label>
                    <textarea name="notes" class="w-full border p-2 rounded" rows="3"></textarea>
                </div>

                <div class="mt-4">
                    <button class="px-4 py-2 bg-yellow-600 text-white rounded">Apply Adjustment</button>
                    <a href="{{ route('billing.index') }}" class="ml-3 text-sm text-gray-600">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <div class="mt-4">
        @if(isset($billing->adjustment) && floatval($billing->adjustment) !== 0.0)
            <p><strong>Cumulative Adjustments:</strong> {{ number_format($billing->adjustment, 2) }}</p>
        @endif
    </div>
@endsection