@extends('layouts.app')

@section('content')
    <div class="mt-6 mb-6">
        <h2 class="text-2xl font-semibold">Billing</h2>
        <p class="text-sm text-slate-500">Billing dashboard — placeholder for accountant/admin functions.</p>
    </div>
    <div class="rounded-lg border p-4 bg-white dark:bg-slate-900">
        <div class="flex items-center justify-between">
            <h3 class="font-medium">Invoices</h3>
            <div class="flex gap-2">
                <a href="{{ route('billing.create') }}"
                    class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded">New Invoice</a>
                <a href="{{ route('billing.export') }}"
                    class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded">Export CSV</a>
            </div>
        </div>

        <p class="text-sm text-slate-500 mt-2 mb-2">List of recent invoices and payments.</p>

        <div class="overflow-hidden rounded-lg border">
            <table class="min-w-full divide-y">
                <thead class="bg-slate-50 dark:bg-slate-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices ?? [] as $inv)
                        <tr class="border-t">
                            <td class="p-2">{{ $inv->id }}</td>
                            <td class="p-2">{{ $inv->patient?->first_name }} {{ $inv->patient?->last_name }}</td>
                            <td class="p-2">{{ number_format($inv->amount, 2) }}</td>
                            <td class="p-2">{{ $inv->paid ? 'Paid' : 'Unpaid' }}</td>
                            <td class="p-2">{{ $inv->created_at->toDateString() }}</td>
                            <td class="p-2">
                                <a href="{{ route('billing.show', $inv->id) }}" class="text-blue-600">View</a>
                                @if(!$inv->paid)
                                    <!-- <a href="{{ route('billing.edit', $inv->id) }}" class="ml-3 text-yellow-600">Adjust</a> -->
                                    <form action="{{ route('billing.generate', $inv->id) }}" method="POST" target="_blank"
                                        class="inline-block ml-3">
                                        @csrf
                                        <button type="submit" class="text-green-600">Generate PDF</button>
                                    </form>
                                    <a href="{{ route('billing.show', ['id' => $inv->id, 'print' => 1]) }}" target="_blank"
                                        class="ml-3 text-gray-600">Print</a>
                                    <form action="{{ route('billing.destroy', $inv->id) }}" method="POST" class="inline-block ml-3"
                                        onsubmit="return confirm('Delete invoice #{{ $inv->id }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600">Delete</button>
                                    </form>
                                @else
                                    <form action="{{ route('billing.mark_unpaid', $inv->id) }}" method="POST"
                                        class="inline-block ml-3">
                                        @csrf
                                        <button type="submit" class="text-red-600"
                                            onclick="return confirm('Mark invoice #{{ $inv->id }} as unpaid?')">Mark Unpaid</button>
                                    </form>
                                    <a href="{{ route('billing.show', ['id' => $inv->id, 'print' => 1]) }}" target="_blank"
                                        class="ml-3 text-gray-600">Print</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-sm text-slate-500">No invoices yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 border rounded">
                <h4 class="font-medium">Totals</h4>
                <p class="text-sm text-slate-500 mt-2">Total outstanding:
                    <strong>{{ number_format(($totals['outstanding'] ?? 0), 2) }}</strong>
                </p>
                <p class="text-sm text-slate-500 mt-1">Total collected:
                    <strong>{{ number_format(($totals['collected'] ?? 0), 2) }}</strong>
                </p>
                <p class="text-sm text-slate-500 mt-1">Grand total:
                    <strong>{{ number_format(($totals['grand'] ?? 0), 2) }}</strong>
                </p>
            </div>

            <div class="p-4 border rounded md:col-span-2">
                <h4 class="font-medium">Generate Invoice for Patient</h4>
                <form action="{{ route('billing.generate_for_patient') }}" method="POST"
                    class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-3">
                    @csrf
                    <div>
                        <label class="block text-sm">Patient</label>
                        <select name="patient_id" required class="w-full border p-2 rounded">
                            <option value="">Select patient</option>
                            @foreach($patients ?? [] as $p)
                                <option value="{{ $p->id }}">{{ $p->first_name }} {{ $p->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm">Adjustment</label>
                        <input type="number" step="0.01" name="adjustment" value="0.00" class="w-full border p-2 rounded" />
                        <p class="text-xs text-slate-400">Positive to add fees, negative for discounts.</p>
                    </div>

                    <div>
                        <label class="block text-sm">Notes</label>
                        <input type="text" name="notes" class="w-full border p-2 rounded"
                            placeholder="Optional notes for invoice" />
                    </div>

                    <div class="md:col-span-3 mt-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Generate Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection