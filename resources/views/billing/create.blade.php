@extends('layouts.app')

@section('content')
    <div class="mt-6">
        <h2 class="text-2xl font-semibold">Create Invoice</h2>

        <form action="{{ route('billing.generate_for_patient') }}" method="POST" class="mt-4">
            @csrf
            <div>
                <label class="block text-sm">Patient</label>
                <select name="patient_id" required class="w-full border p-2 rounded">
                    <option value="">Select patient</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}">{{ $p->first_name }} {{ $p->last_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mt-2">
                <label class="block text-sm">Adjustment</label>
                <input type="number" step="0.01" name="adjustment" value="0.00" class="w-full border p-2 rounded" />
            </div>

            <div class="mt-2">
                <label class="block text-sm">Notes</label>
                <input type="text" name="notes" class="w-full border p-2 rounded" placeholder="Optional notes" />
            </div>

            <div class="mt-4">
                <button class="px-4 py-2 bg-blue-600 text-white rounded">Generate Invoice</button>
            </div>
        </form>
    </div>
@endsection