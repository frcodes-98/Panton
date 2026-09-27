@props([
    'alpine' => 'dataGrid',
    'rows' => [],
    'columns' => [],
    'searchFields' => [],
    'storageKey' => null,
    'pageSize' => 10,
    'sortKey' => null,
    'selectable' => true,
    'extra' => [],
    'title' => null,
])

@php
    $config = array_merge([
        'rows' => $rows,
        'columns' => $columns,
        'searchFields' => $searchFields,
        'storageKey' => $storageKey,
        'pageSize' => $pageSize,
        'sortKey' => $sortKey,
        'selectable' => $selectable,
    ], $extra);
@endphp

<div x-data="{{ $alpine }}(@js($config))" x-cloak class="panel overflow-hidden">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
        <div class="flex items-center gap-3">
            @if ($title)
                <h2 class="font-semibold text-slate-900">{{ $title }}</h2>
            @endif
            <span class="pill bg-teal-50 text-teal-700 ring-1 ring-teal-100"><span x-text="total()"></span> records</span>
        </div>
        <div class="flex min-w-64 flex-1 items-center justify-end gap-2">
            <label class="relative w-full max-w-64">
                <span class="sr-only">Find</span>
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input x-model="query" type="search" placeholder="Find" class="input py-2 pl-9" />
            </label>
            <select x-show="searchFields.length > 1" x-model="field" class="input w-auto py-2" aria-label="Search by">
                <template x-for="searchField in searchFields" :key="searchField.key">
                    <option :value="searchField.key" x-text="searchField.label"></option>
                </template>
            </select>
            <button type="button" class="btn btn-secondary" @click="listAll()">List all</button>
        </div>
    </div>

    @if (isset($toolbar))
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-100 bg-slate-50/70 px-5 py-3">
            {{ $toolbar }}
            <p x-show="selected()" class="ml-auto text-xs text-slate-500">Selected: <span class="font-medium text-teal-800" x-text="selected()?.[columns[0].key]"></span></p>
        </div>
    @endif

    <div x-show="notice" class="flex items-center gap-2 border-b border-teal-100 bg-teal-50 px-5 py-2.5 text-sm text-teal-800">
        <x-icon name="check" class="h-4 w-4" />
        <span x-text="notice"></span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead>
                <tr class="bg-gradient-to-r from-teal-700 to-cyan-800 text-[11px] uppercase tracking-wider text-teal-50">
                    <th class="w-16 px-5 py-3 font-semibold">No.</th>
                    <template x-for="column in columns" :key="column.key">
                        <th class="px-5 py-3 font-semibold" :class="column.align === 'right' ? 'text-right' : ''">
                            <button type="button" class="inline-flex items-center gap-1 hover:text-white" @click="toggleSort(column.key)">
                                <span x-text="column.label"></span>
                                <span class="text-teal-200" x-text="sortKey === column.key ? (sortDir === 'asc' ? '↑' : '↓') : ''"></span>
                            </button>
                        </th>
                    </template>
                </tr>
            </thead>
            <tbody>
                <template x-for="(row, index) in pageRows()" :key="row.id">
                    <tr
                        class="cursor-pointer border-t border-slate-100 transition"
                        :class="selectedId === row.id ? 'bg-teal-50 shadow-[inset_4px_0_0_0_var(--color-teal-600)]' : (index % 2 ? 'bg-slate-50/60 hover:bg-teal-50/50' : 'bg-white hover:bg-teal-50/50')"
                        @click="select(row)"
                    >
                        <td class="px-5 py-3 text-slate-400" x-text="rangeStart() + index"></td>
                        <template x-for="column in columns" :key="column.key">
                            <td class="px-5 py-3" :class="column.align === 'right' ? 'text-right' : ''">
                                <span x-show="pill(row, column)" class="pill" :class="pill(row, column)" x-text="cell(row, column)"></span>
                                <span x-show="!pill(row, column)" class="text-slate-800" :class="column === columns[0] ? 'font-medium text-slate-900' : ''" x-text="cell(row, column)"></span>
                            </td>
                        </template>
                    </tr>
                </template>
                <tr x-show="total() === 0">
                    <td class="px-5 py-12 text-center text-slate-500" :colspan="columns.length + 1">No records match this search.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="flex flex-wrap items-center gap-3 border-t border-slate-100 px-5 py-3 text-sm text-slate-600">
        <p class="text-slate-500">
            Displaying <span class="font-medium text-slate-700" x-text="rangeStart()"></span>–<span class="font-medium text-slate-700" x-text="rangeEnd()"></span> of <span class="font-medium text-slate-700" x-text="total()"></span>
        </p>
        <div class="ml-auto flex flex-wrap items-center gap-1.5">
            <select class="input w-auto py-1.5" x-model.number="pageSize" @change="setPageSize(pageSize)" aria-label="Rows per page">
                <option value="10">10 / page</option>
                <option value="20">20 / page</option>
                <option value="50">50 / page</option>
            </select>
            <button type="button" class="btn btn-secondary px-2.5 py-1.5" @click="go(1)" :disabled="currentPage() === 1">First</button>
            <button type="button" class="btn btn-secondary px-2.5 py-1.5" @click="go(currentPage() - 1)" :disabled="currentPage() === 1">Prev</button>
            <span class="rounded-lg bg-teal-50 px-3 py-1.5 font-medium text-teal-800"><span x-text="currentPage()"></span> / <span x-text="pageCount()"></span></span>
            <button type="button" class="btn btn-secondary px-2.5 py-1.5" @click="go(currentPage() + 1)" :disabled="currentPage() === pageCount()">Next</button>
            <button type="button" class="btn btn-secondary px-2.5 py-1.5" @click="go(pageCount())" :disabled="currentPage() === pageCount()">Last</button>
            <button type="button" class="btn btn-secondary px-2.5 py-1.5" @click="refresh()" title="Refresh">Refresh</button>
        </div>
    </div>

    @if (isset($dialogs))
        {{ $dialogs }}
    @endif
</div>
