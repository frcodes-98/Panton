@props([
    'items',
    'columns',
    'model',
    'single' => false,
    'title' => null,
    'storageKey' => null,
    'excludeId' => null,
])

<div
    x-data="picker(@js([
        'items' => $items,
        'columns' => $columns,
        'single' => $single,
        'storageKey' => $storageKey,
        'excludeId' => $excludeId,
    ]))"
    x-modelable="picks"
    x-model="{{ $model }}"
    class="overflow-hidden rounded-2xl border border-slate-200 bg-white"
>
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-4 py-3">
        <div class="flex items-center gap-2">
            @if ($title)
                <h3 class="text-sm font-semibold text-slate-800">{{ $title }}</h3>
            @endif
            <span class="pill bg-teal-50 text-teal-700 ring-1 ring-teal-100"><span x-text="picks.length"></span> selected</span>
        </div>
        <label class="relative w-full max-w-56">
            <span class="sr-only">Find</span>
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input x-model="query" type="search" placeholder="Find" class="input py-1.5 pl-9" />
        </label>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-teal-50/70 text-[11px] uppercase tracking-wider text-teal-800">
                <tr>
                    <th class="w-12 px-4 py-2.5"></th>
                    <th class="w-14 px-4 py-2.5 font-semibold">No.</th>
                    <template x-for="column in columns" :key="column.key">
                        <th class="px-4 py-2.5 font-semibold">
                            <button type="button" class="inline-flex items-center gap-1 hover:text-teal-950" @click="toggleSort(column.key)">
                                <span x-text="column.label"></span>
                                <span x-text="sortKey === column.key ? (sortDir === 'asc' ? '↑' : '↓') : ''"></span>
                            </button>
                        </th>
                    </template>
                </tr>
            </thead>
            <tbody>
                <template x-for="(row, index) in pageRows()" :key="row.id">
                    <tr class="cursor-pointer border-t border-slate-100 transition" :class="has(row.id) ? 'bg-teal-50' : 'hover:bg-slate-50'" @click="toggle(row.id)">
                        <td class="px-4 py-2.5">
                            <input :type="single ? 'radio' : 'checkbox'" class="h-4 w-4 accent-teal-600" :checked="has(row.id)" @click.stop="toggle(row.id)" />
                        </td>
                        <td class="px-4 py-2.5 text-slate-400" x-text="rangeStart() + index"></td>
                        <template x-for="column in columns" :key="column.key">
                            <td class="px-4 py-2.5">
                                <span x-show="pill(row, column)" class="pill" :class="pill(row, column)" x-text="cell(row, column)"></span>
                                <span x-show="!pill(row, column)" class="text-slate-800" x-text="cell(row, column)"></span>
                            </td>
                        </template>
                    </tr>
                </template>
                <tr x-show="total() === 0">
                    <td class="px-4 py-8 text-center text-slate-500" :colspan="columns.length + 2">No records match this search.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 px-4 py-2.5 text-sm text-slate-600">
        <p class="text-slate-500"><span x-text="rangeStart()"></span>–<span x-text="rangeEnd()"></span> of <span x-text="total()"></span></p>
        <div class="ml-auto flex items-center gap-1.5">
            <select class="input w-auto py-1" x-model.number="pageSize" @change="setPageSize(pageSize)" aria-label="Rows per page">
                <option value="10">10 / page</option>
                <option value="20">20 / page</option>
            </select>
            <button type="button" class="btn btn-secondary px-2.5 py-1" @click="go(currentPage() - 1)" :disabled="currentPage() === 1">Prev</button>
            <span class="px-1"><span x-text="currentPage()"></span> / <span x-text="pageCount()"></span></span>
            <button type="button" class="btn btn-secondary px-2.5 py-1" @click="go(currentPage() + 1)" :disabled="currentPage() === pageCount()">Next</button>
        </div>
    </div>
</div>
