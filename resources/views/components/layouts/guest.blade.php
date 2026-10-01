@props([
    'title' => null,
    'eyebrow' => 'Supermarket Goods Hub',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    <link rel="icon" href="/favicon.ico">
    <x-theme-script />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-ink-100">
<div class="flex min-h-screen flex-col justify-center px-4 py-10 sm:px-6">
    <div class="mx-auto w-full max-w-md">

        <a href="{{ route('catalog.index') }}" class="mb-8 flex flex-col items-center text-center">
            <span class="grid size-14 place-items-center rounded-2xl bg-brand-600 text-white shadow-pop">
                <x-icon name="store" class="size-7" />
            </span>
            <h1 class="mt-4 text-xl font-bold tracking-tight text-ink-900">{{ config('app.name') }}</h1>
            <p class="text-sm text-ink-500">{{ $eyebrow }}</p>
        </a>

        <div class="card">
            <div class="card-body sm:p-7">
                @if ($title)
                    <h2 class="mb-1 text-lg font-bold tracking-tight text-ink-900">{{ $title }}</h2>
                @endif
                <x-flash />
                {{ $slot }}
            </div>
        </div>

        <p class="mt-6 text-center text-xs text-ink-400">
            &copy; {{ now()->year }} {{ config('app.name') }}
        </p>
    </div>
</div>
</body>
</html>
