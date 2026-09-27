@extends('layouts.base')

@section('title', 'Panton')

@section('body-class', 'min-h-screen bg-teal-950 text-slate-800 antialiased')

@section('content')
    <div class="relative min-h-screen overflow-hidden">
        <img src="{{ asset('images/panton-hero.png') }}" alt="" class="absolute inset-0 h-full w-full object-cover object-right-bottom" />
        <div class="absolute inset-0 bg-gradient-to-br from-teal-900/70 via-teal-700/45 to-cyan-900/70"></div>

        <div class="absolute right-8 top-8 z-10 hidden text-right text-white lg:block">
            <div class="ml-auto mb-3 flex w-fit items-center gap-1.5">
                <span class="h-3 w-6 rounded-full bg-white/90"></span>
                <span class="h-3 w-3 rounded-full bg-white/80"></span>
                <span class="h-3 w-4 rounded-full bg-white/70"></span>
                <span class="h-3 w-3 rounded-full bg-white/60"></span>
            </div>
            <p class="text-4xl font-semibold tracking-tight drop-shadow">Panton</p>
            <p class="mt-1 text-sm font-medium tracking-[0.3em] text-teal-50/90">ON YOUR SIDE</p>
        </div>

        <div class="absolute bottom-10 right-10 z-10 hidden max-w-sm text-right text-white lg:block">
            <p class="text-2xl font-semibold leading-snug drop-shadow">Every document, policy and process in one trusted place.</p>
        </div>

        <div class="relative z-10 flex min-h-screen">
            <section class="relative flex w-full flex-col justify-center bg-white/95 px-6 py-12 shadow-2xl backdrop-blur sm:px-12 lg:w-[46%] lg:rounded-r-[45%_50%] lg:bg-white lg:pr-28">
                <div class="mx-auto w-full max-w-md lg:mx-0 lg:ml-8 xl:ml-16">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-teal-500 to-cyan-700 text-lg font-bold text-white shadow-lg shadow-teal-700/30">P</span>
                        <div>
                            <p class="text-lg font-semibold text-slate-900">Panton</p>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-teal-700">Business Process Management</p>
                        </div>
                    </div>

                    <h1 class="mt-10 text-3xl font-semibold tracking-tight text-slate-900">Welcome back</h1>
                    <p class="mt-1 text-slate-500">Sign in to continue to your workspaces.</p>

                    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-4">
                        @csrf
                        <div>
                            <label for="username" class="label">Username</label>
                            <input id="username" name="username" value="{{ old('username') }}" autocomplete="username" class="input py-3" placeholder="Username" />
                        </div>
                        <div>
                            <label for="password" class="label">Password</label>
                            <input id="password" name="password" type="password" autocomplete="current-password" class="input py-3" placeholder="••••••••" />
                        </div>
                        @if ($errors->any())
                            <p class="rounded-lg bg-rose-50 px-3 py-2 text-sm text-rose-700">{{ $errors->first() }}</p>
                        @endif
                        <button type="submit" class="btn w-full bg-gradient-to-r from-teal-600 to-cyan-700 py-3 font-semibold tracking-[0.2em] text-white shadow-lg shadow-teal-700/30 hover:from-teal-700 hover:to-cyan-800">ENTER</button>
                    </form>

                    <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                        <div class="rounded-2xl border border-teal-100 bg-gradient-to-br from-teal-50 to-white p-5">
                            <h2 class="text-xl font-semibold text-teal-800">Vision</h2>
                            <p class="mt-2 text-sm leading-6 text-teal-900/80">To be the most trusted international healthcare provider</p>
                        </div>
                        <div class="rounded-2xl border border-cyan-100 bg-gradient-to-br from-cyan-50 to-white p-5">
                            <h2 class="text-xl font-semibold text-cyan-800">Mission</h2>
                            <p class="mt-2 text-sm leading-6 text-cyan-900/80">To take exemplary care of our patients anchored around our people who strive to continuously raise the bar in clinical, operational and service excellence</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
