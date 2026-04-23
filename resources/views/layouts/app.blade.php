<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Seal Station') }} - @yield('title')</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-slate-50 text-slate-900 min-h-screen">
        <div class="flex min-h-screen">
            <aside class="w-72 border-r border-slate-200 bg-white overflow-y-auto">
                <div class="px-6 py-7 border-b border-slate-200">
                    <div class="text-2xl font-semibold text-red-700">Seal</div>
                    <p class="mt-2 text-sm text-slate-500">Fuel station operations</p>
                </div>
                <nav class="px-4 py-6 space-y-1">
                    <a href="{{ route('dashboard') }}" class="block rounded-lg px-4 py-3 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-red-700 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Dashboard</a>
                    <a href="{{ route('shift.management') }}" class="block rounded-lg px-4 py-3 text-sm font-medium {{ request()->routeIs('shift.management') ? 'bg-red-700 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Shift Management</a>
                    <a href="{{ route('wetstock') }}" class="block rounded-lg px-4 py-3 text-sm font-medium {{ request()->routeIs('wetstock') ? 'bg-red-700 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Wetstock</a>
                    <a href="{{ route('reports') }}" class="block rounded-lg px-4 py-3 text-sm font-medium {{ request()->routeIs('reports') ? 'bg-red-700 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Reports</a>
                    <a href="{{ route('financials') }}" class="block rounded-lg px-4 py-3 text-sm font-medium {{ request()->routeIs('financials') ? 'bg-red-700 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Financials</a>
                    <a href="{{ route('customers') }}" class="block rounded-lg px-4 py-3 text-sm font-medium {{ request()->routeIs('customers') ? 'bg-red-700 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Customers</a>
                </nav>
            </aside>

            <main class="flex-1 p-6 lg:p-8">
                <header class="mb-8">
                    <h1 class="text-3xl font-semibold tracking-tight text-slate-900">@yield('title')</h1>
                    <p class="mt-2 text-sm text-slate-500">@yield('subtitle')</p>
                </header>

                <div class="space-y-6">
                    @yield('content')
                </div>
            </main>
        </div>

        @yield('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
