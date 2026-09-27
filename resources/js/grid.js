export function nextId(rows) {
    return rows.reduce((max, row) => Math.max(max, Number(row.id) || 0), 0) + 1;
}

export function loadRows(storageKey, fallback) {
    if (!storageKey) {
        return fallback;
    }

    const saved = sessionStorage.getItem(storageKey);

    if (!saved) {
        return fallback;
    }

    try {
        const parsed = JSON.parse(saved);

        return Array.isArray(parsed) ? parsed : fallback;
    } catch {
        return fallback;
    }
}

export function saveRows(storageKey, rows) {
    if (storageKey) {
        sessionStorage.setItem(storageKey, JSON.stringify(rows));
    }
}

export function dataGrid(config) {
    const searchFields = config.searchFields ?? [];

    return {
        rows: config.rows ?? [],
        columns: config.columns ?? [],
        searchFields,
        storageKey: config.storageKey ?? null,
        selectable: config.selectable !== false,
        query: '',
        field: searchFields[0]?.key ?? '',
        page: 1,
        pageSize: Number(config.pageSize ?? 10),
        sortKey: config.sortKey ?? null,
        sortDir: 'asc',
        selectedId: null,
        notice: '',

        init() {
            this.rows = loadRows(this.storageKey, this.rows);

            if (new URLSearchParams(window.location.search).get('saved') === '1') {
                this.notice = 'Saved for this browser session.';
            }

            this.$watch('query', () => {
                this.page = 1;
            });
            this.$watch('field', () => {
                this.page = 1;
            });
        },

        persist() {
            saveRows(this.storageKey, this.rows);
        },

        selected() {
            return this.rows.find((row) => row.id === this.selectedId) ?? null;
        },

        filtered() {
            const query = this.query.trim().toLowerCase();
            let list = this.rows;

            if (query && this.field) {
                list = list.filter((row) => String(row[this.field] ?? '').toLowerCase().includes(query));
            }

            if (this.sortKey) {
                const direction = this.sortDir === 'asc' ? 1 : -1;
                const key = this.sortKey;
                list = [...list].sort((a, b) => {
                    const left = a[key];
                    const right = b[key];

                    if (typeof left === 'number' && typeof right === 'number') {
                        return (left - right) * direction;
                    }

                    if (typeof left === 'boolean' && typeof right === 'boolean') {
                        return (Number(left) - Number(right)) * direction;
                    }

                    return String(left ?? '').localeCompare(String(right ?? ''), undefined, { sensitivity: 'base' }) * direction;
                });
            }

            return list;
        },

        total() {
            return this.filtered().length;
        },

        pageCount() {
            return Math.max(1, Math.ceil(this.total() / this.pageSize));
        },

        currentPage() {
            return Math.min(this.page, this.pageCount());
        },

        pageRows() {
            const start = (this.currentPage() - 1) * this.pageSize;

            return this.filtered().slice(start, start + this.pageSize);
        },

        rangeStart() {
            return this.total() === 0 ? 0 : (this.currentPage() - 1) * this.pageSize + 1;
        },

        rangeEnd() {
            return Math.min(this.currentPage() * this.pageSize, this.total());
        },

        setPageSize(size) {
            this.pageSize = Number(size);
            this.page = 1;
        },

        go(page) {
            this.page = Math.min(Math.max(1, page), this.pageCount());
        },

        listAll() {
            this.query = '';
            this.page = 1;
            this.notice = '';
        },

        refresh() {
            this.rows = loadRows(this.storageKey, this.rows);
            this.page = 1;
            this.notice = '';
        },

        toggleSort(key) {
            if (this.sortKey === key) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortKey = key;
                this.sortDir = 'asc';
            }
        },

        select(row) {
            if (!this.selectable) {
                return;
            }

            this.selectedId = row.id;
            this.notice = '';
        },

        requireSelection(message = 'Select a row first.') {
            if (!this.selected()) {
                this.notice = message;

                return false;
            }

            this.notice = '';

            return true;
        },

        cell(row, column) {
            return cellText(row, column);
        },

        pill(row, column) {
            return pillClass(row, column);
        },
    };
}

export function cellText(row, column) {
    const value = row[column.key];

    if (column.format === 'enabled') {
        return value ? 'Enabled' : 'Disabled';
    }

    return value ?? '';
}

const pillTones = {
    good: 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
    bad: 'bg-rose-50 text-rose-700 ring-1 ring-rose-200',
    warn: 'bg-amber-50 text-amber-800 ring-1 ring-amber-200',
    muted: 'bg-slate-100 text-slate-600 ring-1 ring-slate-200',
};

export function pillClass(row, column) {
    const value = row[column.key];

    if (column.format === 'enabled') {
        return value ? pillTones.good : pillTones.muted;
    }

    if (column.format === 'login') {
        return value === 'Enable' ? pillTones.good : pillTones.bad;
    }

    if (column.format === 'suspend') {
        return value === 'Yes' ? pillTones.warn : pillTones.muted;
    }

    return '';
}

export function withGrid(config, extra = {}) {
    const base = dataGrid(config);

    return {
        ...base,
        ...extra,
        init() {
            base.init.call(this);

            if (typeof extra.init === 'function') {
                extra.init.call(this);
            }
        },
    };
}
