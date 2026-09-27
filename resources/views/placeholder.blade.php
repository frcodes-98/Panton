@extends('layouts.base')

@section('title', $tile['name'].' · Panton')

@section('body-class', 'min-h-screen bg-teal-950 text-white antialiased')

@section('content')
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-gradient-to-br from-teal-800 via-teal-900 to-cyan-950 px-6">
        <div class="pointer-events-none absolute -left-32 -top-32 h-96 w-96 rounded-full bg-teal-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-cyan-400/15 blur-3xl"></div>
        <div class="relative w-full max-w-lg overflow-hidden rounded-3xl bg-white text-slate-800 shadow-2xl">
            <div class="flex items-center gap-4 bg-gradient-to-r from-teal-600 to-cyan-700 px-8 py-6 text-white">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20 ring-1 ring-white/30">
                    <x-icon :name="$tile['icon']" class="h-6 w-6" />
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-teal-100">{{ $tile['kind'] === 'app' ? 'Application' : 'Workspace' }}</p>
                    <h1 class="text-2xl font-semibold">{{ $tile['name'] }}</h1>
                </div>
            </div>
            <div class="p-8">
                <p class="text-slate-600">This area is not in the screen spec yet. System Control is the part you can use in this pass.</p>
                <div class="mt-6 flex gap-2">
                    <a href="{{ route('desktop') }}" class="btn btn-primary">Back to desktop</a>
                    <a href="{{ route('system.welcome') }}" class="btn btn-secondary">Open System Control</a>
                </div>
            </div>
        </div>
    </div>
@endsection
