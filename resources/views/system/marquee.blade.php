@extends('layouts.system')

@section('title', 'Marquee · Panton')

@section('heading', 'Marquee Configuration')

@section('main')
    <div x-data="marqueePage(@js(['marquee' => $marquee]))" x-cloak class="mx-auto max-w-3xl space-y-4">
        <p x-show="notice" class="flex items-center gap-2 rounded-2xl bg-teal-50 px-4 py-3 text-sm text-teal-800 ring-1 ring-teal-100">
            <x-icon name="check" class="h-4 w-4" /> <span x-text="notice"></span>
        </p>

        <section class="panel overflow-hidden">
            <div class="flex items-center justify-between bg-gradient-to-r from-amber-400 to-orange-500 px-6 py-4 text-white">
                <div>
                    <h2 class="font-semibold">Live preview</h2>
                    <p class="text-sm text-white/85">How the announcement looks across the top of Panton.</p>
                </div>
                <span class="pill bg-white/20 text-white ring-1 ring-white/30" x-text="flag === 'on' ? 'On' : 'Off'"></span>
            </div>
            <div class="bg-slate-100 p-4">
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                    <div class="overflow-hidden py-1.5 text-sm font-medium transition" :class="flag === 'on' ? 'bg-amber-300 text-amber-950' : 'bg-slate-200 text-slate-400'">
                        <p :class="flag === 'on' ? 'animate-marquee whitespace-nowrap' : 'px-4'" x-text="flag === 'on' ? (text || 'Type a message below') : 'The marquee is turned off'"></p>
                    </div>
                    <div class="flex gap-2 p-4">
                        <span class="h-3 w-20 rounded-full bg-slate-100"></span>
                        <span class="h-3 w-32 rounded-full bg-slate-100"></span>
                        <span class="h-3 w-16 rounded-full bg-slate-100"></span>
                    </div>
                </div>
            </div>
        </section>

        <form class="panel space-y-5 p-6" @submit.prevent="save()">
            <div class="grid gap-3 sm:grid-cols-[9rem_1fr] sm:items-center">
                <p class="label mb-0">Marquee flag</p>
                <x-choice model="flag" :options="['on', 'off']" />
                <label class="label mb-0" for="marquee-text">Marquee text</label>
                <input id="marquee-text" x-model="text" class="input" />
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-100 pt-5">
                <button type="button" class="btn btn-secondary" @click="reset()">Reset</button>
                <button type="submit" class="btn btn-primary px-5">Save</button>
            </div>
        </form>
    </div>
@endsection
