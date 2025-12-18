@extends('layouts.app')

@section('content')
    <div class="mt-6 text-slate-900 dark:text-slate-100">
        <style>
            /* PDF-friendly styles for DomPDF */
            @page {
                margin: 18mm;
            }

            body {
                font-family: "DejaVu Sans", Arial, Helvetica, sans-serif;
                font-size: 12px;
                color: #222;
            }

            .invoice-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .invoice-logo {
                height: 64px;
            }

            .invoice-box {
                border: 1px solid #e5e7eb;
                padding: 16px;
                margin-top: 16px;
                color: #111 !important;
            }

            .price-list {
                margin-top: 8px;
                padding-left: 0;
                list-style: none;
            }

            .price-list li {
                padding: 4px 0;
            }

            .total {
                margin-top: 12px;
                font-weight: 700;
                font-size: 1.05em;
            }

            .small {
                font-size: 0.85em;
                color: #555;
            }

            /* dark-mode overrides for app dark theme (Tailwind uses .dark on root) */
            .dark .small {
                color: #ddd !important;
            }

            .dark .invoice-box {
                color: #eee !important;
            }

            .actions {
                margin-top: 16px;
            }

            /* Ensure table-friendly rendering */
            table {
                border-collapse: collapse;
                width: 100%;
            }

            th,
            td {
                padding: 6px;
                border: 1px solid #ddd;
            }

            /* Print helpers: hide UI chrome when printing */
            @media print {

                nav,
                header,
                .no-print,
                .actions,
                .sidebar,
                .topbar {
                    display: none !important;
                }

                a,
                button {
                    text-decoration: none;
                    color: #000 !important;
                }
            }
        </style>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <i class="fa-solid fa-hospital text-2xl text-emerald-400"></i>
                <!-- <span class="font-semibold text-lg">IMS</span> -->
                <div>
                    <h2 class="text-2xl font-semibold">Invoice #{{ $billing->id }}</h2>
                    <div class="text-sm text-slate-600">Date: {{ $billing->created_at->toDateString() }}</div>
                </div>
            </div>
            <div class="text-right">
                <div><strong>Amount:</strong> {{ number_format($billing->amount, 2) }}</div>
                <div><strong>Status:</strong> {{ $billing->paid ? 'Paid' : 'Unpaid' }}</div>
            </div>
        </div>

        <div class="mt-6 p-4 bg-white border rounded invoice-box dark:bg-slate-800 dark:text-slate-100">
            <p><strong>Bill To:</strong></p>
            <p>{{ $billing->patient?->first_name }} {{ $billing->patient?->last_name }}</p>

            <div class="mt-4">
                <h4 class="font-medium">Price Details</h4>
                <ul class="text-sm mt-2">
                    @foreach($billing->items as $item)
                        <li>{{ $item->description }} — {{ $item->quantity }} × {{ number_format($item->unit_price, 2) }} =
                            {{ number_format($item->total, 2) }}</li>
                    @endforeach
                    @if(isset($billing->adjustment) && floatval($billing->adjustment) !== 0.0)
                    <li>Adjustments: {{ number_format($billing->adjustment, 2) }}</li>
                @endif
                </ul>
            </div>

            

            @if(!$billing->paid)
            <div class="mt-4"><strong>Total unpaid for patient:</strong> {{ number_format($patientOutstanding ?? 0, 2) }}</div>
                <div class="mt-4 text-sm text-red-600">
                    <strong>Note:</strong> This invoice is unpaid. Please arrange payment at your earliest convenience.
                </div>
            @else
            <div class="mt-4"><strong>Total paid for patient:</strong> {{ number_format($patientOutstanding ?? 0, 2) }}</div>
                <div class="mt-4 text-sm text-green-600">
                    <strong>Thank you!</strong> This invoice has been paid.
                </div>
            @endif


        </div>

        <div class="mt-4">
            <a href="{{ route('billing.index') }}"
                class="text-blue-600 float-left no-print px-4 py-2 bg-gray-600 text-white rounded mr-4">Back to invoices</a>
            @if(!$billing->paid)
                <form action="{{ route('billing.generate', $billing->id) }}" method="POST" target="_blank"
                    class="no-print float-right">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Mark Paid</button>
                </form>
                <button type="button" onclick="window.print()"
                    class="mr-4 px-4 py-2 bg-gray-600 text-white rounded no-print float-right">Print</button>
            @else
                <div class="mt-2">
                    <form action="{{ route('billing.mark_unpaid', $billing->id) }}" method="POST"
                        class="inline-block no-print float-right">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded"
                            onclick="return confirm('Mark invoice #{{ $billing->id }} as unpaid?')">Mark Unpaid</button>
                    </form>
                    <button type="button" onclick="window.print()"
                        class="mr-4 px-4 py-2 bg-gray-600 text-white rounded no-print float-right">Print</button>
                </div>
            @endif
        </div>

    </div>
    @if(!empty($autoprint))
        <script>
            // Auto-trigger print when opened with ?print=1
            window.addEventListener('DOMContentLoaded', function () {
                setTimeout(function () { window.print(); }, 200);
            });
        </script>
    @endif
@endsection