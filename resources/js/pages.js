import { cellText, loadRows, nextId, pillClass, saveRows, withGrid } from './grid';

function blankUser() {
    const today = new Date().toISOString().slice(0, 10);

    return {
        username: '',
        full_name: '',
        department: '',
        position: '',
        email: '',
        contact: '',
        fax: '',
        branch: '',
        employee_no: '',
        employee_code: '',
        user_type: 'View Only',
        login_status: 'Enable',
        activation_date: today,
        disable_date: '',
        maximum_attempts: 5,
        suspend_login: 'No',
        password: '',
        confirm_password: '',
    };
}

export function registerPages(Alpine) {
    Alpine.data('picker', (config) => ({
        items: config.items ?? [],
        columns: config.columns ?? [],
        single: Boolean(config.single),
        picks: [],
        query: '',
        page: 1,
        pageSize: 10,
        sortKey: config.columns?.[0]?.key ?? 'name',
        sortDir: 'asc',

        init() {
            if (config.storageKey) {
                this.items = loadRows(config.storageKey, this.items);
            }

            if (config.excludeId) {
                this.items = this.items.filter((item) => item.id !== config.excludeId);
            }

            this.$watch('query', () => {
                this.page = 1;
            });
        },

        filtered() {
            const query = this.query.trim().toLowerCase();
            let list = this.items;

            if (query) {
                list = list.filter((item) => this.columns.some((column) => String(item[column.key] ?? '').toLowerCase().includes(query)));
            }

            const direction = this.sortDir === 'asc' ? 1 : -1;
            const key = this.sortKey;

            return [...list].sort((a, b) => String(a[key] ?? '').localeCompare(String(b[key] ?? ''), undefined, { sensitivity: 'base' }) * direction);
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

        go(page) {
            this.page = Math.min(Math.max(1, page), this.pageCount());
        },

        setPageSize(size) {
            this.pageSize = Number(size);
            this.page = 1;
        },

        toggleSort(key) {
            if (this.sortKey === key) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortKey = key;
                this.sortDir = 'asc';
            }
        },

        has(id) {
            return this.picks.includes(id);
        },

        toggle(id) {
            if (this.single) {
                this.picks = this.picks[0] === id ? [] : [id];

                return;
            }

            this.picks = this.has(id) ? this.picks.filter((pick) => pick !== id) : [...this.picks, id];
        },

        cell(row, column) {
            return cellText(row, column);
        },

        pill(row, column) {
            return pillClass(row, column);
        },
    }));

    Alpine.data('userList', (config) => withGrid(config, {
        editUrl: config.editUrl,
        goEdit() {
            if (!this.requireSelection()) {
                return;
            }

            window.location = this.editUrl.replace('__ID__', this.selectedId);
        },
    }));

    Alpine.data('userForm', (config) => ({
        mode: config.mode,
        userId: config.userId,
        indexUrl: config.indexUrl,
        openStep: 1,
        error: '',
        missing: false,
        form: blankUser(),
        groupPicks: [],
        workspacePicks: [],
        widgetPicks: [],
        delegatePicks: [],

        init() {
            if (this.mode !== 'edit') {
                return;
            }

            const users = config.users;
            const user = users.find((item) => item.id === this.userId);

            if (!user) {
                this.missing = true;
                this.error = 'This user is not in the sample list.';

                return;
            }

            this.form = {
                ...blankUser(),
                ...user,
                password: '',
                confirm_password: '',
            };
            this.groupPicks = [...(user.group_ids ?? [])];
            this.workspacePicks = [...(user.workspace_ids ?? [])];
            this.widgetPicks = [...(user.widget_ids ?? [])];
            this.delegatePicks = [...(user.delegate_ids ?? [])];
        },

        async save() {
            this.error = this.validate();

            if (this.error) {
                this.openStep = 1;
                this.$refs.error?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                return;
            }

            const record = {
                username: this.form.username.trim(),
                full_name: this.form.full_name.trim(),
                department: this.form.department.trim(),
                position: this.form.position.trim(),
                email: this.form.email.trim(),
                contact: this.form.contact.trim(),
                fax: this.form.fax.trim(),
                branch: this.form.branch.trim(),
                employee_no: this.form.employee_no.trim(),
                employee_code: this.form.employee_code.trim(),
                user_type: this.form.user_type,
                login_status: this.form.login_status,
                activation_date: this.form.activation_date,
                disable_date: this.form.disable_date,
                maximum_attempts: Number(this.form.maximum_attempts),
                suspend_login: this.form.suspend_login,
                group_ids: [...this.groupPicks],
                workspace_ids: [...this.workspacePicks],
                widget_ids: [...this.widgetPicks],
                delegate_ids: [...this.delegatePicks],
            };

            const response = await fetch(this.mode === 'edit' ? config.updateUrl : config.storeUrl, {
                method: this.mode === 'edit' ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ ...record, password: this.form.password }),
            });

            if (!response.ok) {
                const body = await response.json().catch(() => ({}));
                this.error = body.message || Object.values(body.errors ?? {})[0]?.[0] || 'Could not save to the database.';
                this.openStep = 1;

                return;
            }

            window.location = `${this.indexUrl}?saved=1`;
        },

        validate() {
            const required = [
                ['username', 'Username'],
                ['full_name', 'Full name'],
                ['department', 'Department'],
                ['email', 'Email address'],
                ['activation_date', 'Activation date'],
                ['disable_date', 'Disable date'],
            ];

            for (const [key, label] of required) {
                if (!String(this.form[key] ?? '').trim()) {
                    return `${label} is required.`;
                }
            }

            if (!this.form.user_type) {
                return 'Role is required.';
            }

            if (!this.form.login_status) {
                return 'Login status is required.';
            }

            if (!this.form.suspend_login) {
                return 'Suspend login is required.';
            }

            if (!Number.isInteger(Number(this.form.maximum_attempts)) || Number(this.form.maximum_attempts) < 1) {
                return 'Maximum attempts must be at least 1.';
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email.trim())) {
                return 'Enter a valid email address.';
            }

            const password = this.form.password;
            const confirm = this.form.confirm_password;

            if (this.mode === 'create' && !password) {
                return 'Password is required.';
            }

            if (password || confirm) {
                if (password.length < 4) {
                    return 'Password must be at least 4 characters.';
                }

                if (password !== confirm) {
                    return 'Password and confirm password must match.';
                }
            }

            const users = loadRows('panton.directory', config.users);
            const duplicate = users.some((user) => user.username.toLowerCase() === this.form.username.trim().toLowerCase() && user.id !== this.userId);

            if (duplicate) {
                return 'That username is already in the list.';
            }

            if (this.delegatePicks.includes(this.userId)) {
                return 'A user cannot be delegated to themselves.';
            }

            return '';
        },
    }));

    Alpine.data('groupPage', (config) => withGrid(config, {
        modal: null,
        draftName: '',
        editingId: null,
        error: '',

        init() {
            if (new URLSearchParams(window.location.search).get('saved') === '1') {
                this.notice = 'Saved for this browser session.';
            }
        },

        openCreate() {
            this.editingId = null;
            this.draftName = '';
            this.error = '';
            this.modal = 'form';
        },

        openEdit() {
            if (!this.requireSelection('Select a group first.')) {
                return;
            }

            this.editingId = this.selectedId;
            this.draftName = this.selected().name;
            this.error = '';
            this.modal = 'form';
        },

        openDelete() {
            if (!this.requireSelection('Select a group first.')) {
                return;
            }

            this.modal = 'delete';
        },

        saveGroup() {
            const name = this.draftName.trim();

            if (!name) {
                this.error = 'Group name is required.';

                return;
            }

            const duplicate = this.rows.some((row) => row.name.toLowerCase() === name.toLowerCase() && row.id !== this.editingId);

            if (duplicate) {
                this.error = 'A group with that name already exists.';

                return;
            }

            if (this.editingId) {
                this.rows = this.rows.map((row) => (row.id === this.editingId ? { ...row, name } : row));
                this.notice = 'Group updated.';
            } else {
                this.rows = [...this.rows, { id: nextId(this.rows), name, total_users: 0 }];
                this.notice = 'Group added.';
            }

            this.persist();
            this.modal = null;
        },

        confirmDelete() {
            this.rows = this.rows.filter((row) => row.id !== this.selectedId);
            this.selectedId = null;
            this.persist();
            this.modal = null;
            this.notice = 'Group deleted.';
        },
    }));

    Alpine.data('workspaceList', (config) => withGrid(config, {
        editUrl: config.editUrl,
        goEdit() {
            if (!this.requireSelection('Select a workspace first.')) {
                return;
            }

            window.location = this.editUrl.replace('__ID__', this.selectedId);
        },
    }));

    Alpine.data('workspaceForm', (config) => ({
        mode: config.mode,
        workspaceId: config.workspaceId,
        indexUrl: config.indexUrl,
        users: config.users,
        missing: false,
        error: '',
        modal: null,
        userQuery: '',
        name: '',
        repository: false,
        approverIds: [],
        controlIds: [],

        init() {
            this.users = config.users;

            if (this.mode !== 'edit') {
                return;
            }

            const workspaces = config.workspaces;
            const workspace = workspaces.find((item) => item.id === this.workspaceId);

            if (!workspace) {
                this.missing = true;
                this.error = 'This workspace is not in the sample list.';

                return;
            }

            this.name = workspace.name;
            this.repository = Boolean(workspace.repository);
            this.approverIds = [...(workspace.approver_ids ?? [])];
            this.controlIds = [...(workspace.control_ids ?? [])];
        },

        filteredUsers() {
            const query = this.userQuery.trim().toLowerCase();

            if (!query) {
                return this.users;
            }

            return this.users.filter((user) => [user.username, user.full_name, user.department].some((value) => String(value).toLowerCase().includes(query)));
        },

        person(id) {
            return this.users.find((user) => user.id === id);
        },

        chosen(list) {
            return this[list].map((id) => this.person(id)).filter(Boolean);
        },

        has(list, id) {
            return this[list].includes(id);
        },

        togglePerson(list, id) {
            this[list] = this.has(list, id) ? this[list].filter((item) => item !== id) : [...this[list], id];
        },

        async save() {
            if (!this.name.trim()) {
                this.error = 'Area name is required.';

                return;
            }

            const response = await fetch(this.mode === 'edit' ? config.updateUrl : config.storeUrl, {
                method: this.mode === 'edit' ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    name: this.name.trim(),
                    repository: this.repository,
                    approver_ids: [...this.approverIds],
                    control_ids: [...this.controlIds],
                }),
            });

            if (!response.ok) {
                const body = await response.json().catch(() => ({}));
                this.error = body.message || Object.values(body.errors ?? {})[0]?.[0] || 'Could not save to the database.';

                return;
            }

            window.location = `${this.indexUrl}?saved=1`;
        },
    }));

    Alpine.data('modulePage', (config) => withGrid(config, {
        modal: null,
        error: '',
        fileName: '',
        draft: { name: '', description: '' },

        openInstall() {
            this.error = '';
            this.fileName = '';
            this.modal = 'install';
        },

        openEdit() {
            if (!this.requireSelection('Select a module first.')) {
                return;
            }

            this.draft = {
                name: this.selected().name,
                description: this.selected().description,
            };
            this.error = '';
            this.modal = 'edit';
        },

        openUninstall() {
            if (!this.requireSelection('Select a module first.')) {
                return;
            }

            this.modal = 'uninstall';
        },

        setStatus(status) {
            if (!this.requireSelection('Select a module first.')) {
                return;
            }

            this.rows = this.rows.map((row) => (row.id === this.selectedId ? { ...row, status } : row));
            this.persist();
            this.notice = status ? 'Module enabled.' : 'Module disabled.';
        },

        install() {
            const file = this.$refs.moduleFile?.files?.[0];

            if (!file) {
                this.error = 'Choose a module setup file (.xml).';

                return;
            }

            if (!file.name.toLowerCase().endsWith('.xml')) {
                this.error = 'The setup file must be an .xml file.';

                return;
            }

            const name = file.name.replace(/\.xml$/i, '');
            this.rows = [...this.rows, {
                id: nextId(this.rows),
                name,
                description: 'Installed from setup file',
                status: true,
            }];
            this.persist();
            this.modal = null;
            this.notice = `${name} installed for this browser session.`;
            this.$refs.moduleFile.value = '';
        },

        saveEdit() {
            if (!this.draft.name.trim()) {
                this.error = 'Module name is required.';

                return;
            }

            this.rows = this.rows.map((row) => (row.id === this.selectedId ? {
                ...row,
                name: this.draft.name.trim(),
                description: this.draft.description.trim(),
            } : row));
            this.persist();
            this.modal = null;
            this.notice = 'Module updated.';
        },

        confirmUninstall() {
            const name = this.selected()?.name ?? 'Module';
            this.rows = this.rows.filter((row) => row.id !== this.selectedId);
            this.selectedId = null;
            this.persist();
            this.modal = null;
            this.notice = `${name} uninstalled for this browser session.`;
        },
    }));

    Alpine.data('widgetList', (config) => withGrid(config, {
        editUrl: config.editUrl,

        modal: null,

        goEdit() {
            if (!this.requireSelection('Select a widget first.')) {
                return;
            }

            window.location = this.editUrl.replace('__ID__', this.selectedId);
        },

        openDelete() {
            if (!this.requireSelection('Select a widget first.')) {
                return;
            }

            this.modal = 'delete';
        },

        confirmDelete() {
            const name = this.selected()?.name ?? 'Widget';
            this.rows = this.rows.filter((row) => row.id !== this.selectedId);
            this.selectedId = null;
            this.persist();
            this.modal = null;
            this.notice = `${name} deleted.`;
        },

        setStatus(status) {
            if (!this.requireSelection('Select a widget first.')) {
                return;
            }

            this.rows = this.rows.map((row) => (row.id === this.selectedId ? { ...row, status } : row));
            this.persist();
            this.notice = status ? 'Widget enabled.' : 'Widget disabled.';
        },
    }));

    Alpine.data('widgetForm', (config) => ({
        mode: config.mode,
        widgetId: config.widgetId,
        indexUrl: config.indexUrl,
        openStep: 1,
        error: '',
        missing: false,
        name: '',
        height: '',
        width: '',
        modulePicks: [],
        workspacePicks: [],
        userPicks: [],
        groupPicks: [],

        init() {
            if (this.mode !== 'edit') {
                return;
            }

            const widgets = loadRows('panton.widgets', config.widgets);
            const widget = widgets.find((item) => item.id === this.widgetId);

            if (!widget) {
                this.missing = true;
                this.error = 'This widget is not in the sample list.';

                return;
            }

            this.name = widget.name;
            this.height = widget.height;
            this.width = widget.width;
            this.modulePicks = widget.module_id ? [widget.module_id] : [];
            this.workspacePicks = widget.workspace_id ? [widget.workspace_id] : [];
            this.userPicks = [...(widget.user_ids ?? [])];
            this.groupPicks = [...(widget.group_ids ?? [])];
        },

        save() {
            if (!this.name.trim()) {
                this.error = 'Widget name is required.';
                this.openStep = 1;

                return;
            }

            const height = Number(this.height);
            const width = Number(this.width);

            if (!Number.isInteger(height) || height < 1) {
                this.error = 'Widget height must be a positive whole number.';
                this.openStep = 1;

                return;
            }

            if (!Number.isInteger(width) || width < 1) {
                this.error = 'Widget width must be a positive whole number.';
                this.openStep = 1;

                return;
            }

            if (this.modulePicks.length !== 1) {
                this.error = 'Select one module.';
                this.openStep = 2;

                return;
            }

            const widgets = loadRows('panton.widgets', config.widgets);
            const record = {
                name: this.name.trim(),
                description: 'MCRE',
                status: true,
                height,
                width,
                module_id: this.modulePicks[0],
                workspace_id: this.workspacePicks[0] ?? null,
                user_ids: [...this.userPicks],
                group_ids: [...this.groupPicks],
            };

            const next = this.mode === 'edit'
                ? widgets.map((widget) => (widget.id === this.widgetId ? { ...widget, ...record, status: widget.status } : widget))
                : [...widgets, { ...record, id: nextId(widgets) }];

            saveRows('panton.widgets', next);
            window.location = `${this.indexUrl}?saved=1`;
        },
    }));

    Alpine.data('settingsPage', (config) => withGrid(config, {
        modal: null,
        error: '',
        editingId: null,
        draft: { key: '', value: '' },

        openCreate() {
            this.editingId = null;
            this.draft = { key: '', value: '' };
            this.error = '';
            this.modal = 'form';
        },

        openEdit() {
            if (!this.requireSelection('Select a setting first.')) {
                return;
            }

            this.editingId = this.selectedId;
            this.draft = { key: this.selected().key, value: this.selected().value };
            this.error = '';
            this.modal = 'form';
        },

        openDelete() {
            if (!this.requireSelection('Select a setting first.')) {
                return;
            }

            this.modal = 'delete';
        },

        saveSetting() {
            const key = this.draft.key.trim();
            const value = this.draft.value.trim();

            if (!key || !value) {
                this.error = 'Setting name and value are required.';

                return;
            }

            const duplicate = this.rows.some((row) => row.key.toLowerCase() === key.toLowerCase() && row.id !== this.editingId);

            if (duplicate) {
                this.error = 'That setting already exists.';

                return;
            }

            if (this.editingId) {
                this.rows = this.rows.map((row) => (row.id === this.editingId ? { ...row, key, value } : row));
                this.notice = 'Setting updated.';
            } else {
                this.rows = [...this.rows, { id: nextId(this.rows), key, value }];
                this.notice = 'Setting added.';
            }

            this.persist();
            this.modal = null;
        },

        confirmDelete() {
            this.rows = this.rows.filter((row) => row.id !== this.selectedId);
            this.selectedId = null;
            this.persist();
            this.modal = null;
            this.notice = 'Setting deleted.';
        },
    }));

    Alpine.data('marqueePage', (config) => ({
        flag: config.marquee.flag,
        text: config.marquee.text,
        savedFlag: config.marquee.flag,
        savedText: config.marquee.text,
        notice: '',

        init() {
            const saved = sessionStorage.getItem('panton.marquee');

            if (!saved) {
                return;
            }

            try {
                const parsed = JSON.parse(saved);
                this.flag = parsed.flag ?? this.flag;
                this.text = parsed.text ?? this.text;
                this.savedFlag = this.flag;
                this.savedText = this.text;
            } catch {
                // Keep the sample marquee.
            }
        },

        save() {
            this.savedFlag = this.flag;
            this.savedText = this.text;
            sessionStorage.setItem('panton.marquee', JSON.stringify({ flag: this.flag, text: this.text }));
            this.$dispatch('marquee-updated', { flag: this.flag, text: this.text });
            this.notice = 'Marquee saved for this browser session.';
        },

        reset() {
            this.flag = config.marquee.flag;
            this.text = config.marquee.text;
            this.savedFlag = this.flag;
            this.savedText = this.text;
            sessionStorage.removeItem('panton.marquee');
            this.$dispatch('marquee-updated', { flag: this.flag, text: this.text });
            this.notice = 'Marquee reset to the sample message.';
        },
    }));

}
