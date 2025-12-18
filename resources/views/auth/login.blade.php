@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto mt-10 bg-white dark:bg-slate-800 p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Login</h2>
        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input name="email" class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900"
                    value="{{ old('email') }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Password</label>
                <input name="password" type="password" class="w-full border rounded px-3 py-2 bg-white dark:bg-slate-900"
                    required>
            </div>

            <div class="flex items-center mb-4">
                <input type="checkbox" name="remember" id="remember" class="h-4 w-4 text-emerald-600">
                <label for="remember" class="ml-2 text-sm">Remember me</label>
            </div>

            <div class="flex justify-end">
                <button class="px-4 py-2 bg-emerald-600 text-white rounded">Login</button>
            </div>
        </form>
    </div>
@endsection