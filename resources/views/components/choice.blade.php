@props(['model', 'options'])

<div class="inline-flex flex-wrap rounded-xl bg-slate-100 p-1">
    @foreach ($options as $option)
        <label class="cursor-pointer">
            <input type="radio" value="{{ $option }}" x-model="{{ $model }}" class="peer sr-only" />
            <span class="block rounded-lg px-3.5 py-1.5 text-sm font-medium text-slate-600 transition peer-checked:bg-white peer-checked:text-teal-800 peer-checked:shadow-sm peer-focus-visible:ring-2 peer-focus-visible:ring-teal-500">{{ $option }}</span>
        </label>
    @endforeach
</div>
