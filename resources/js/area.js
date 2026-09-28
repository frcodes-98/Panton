function uid(prefix) {
    return `${prefix}-${Date.now().toString(36)}-${Math.random().toString(36).slice(2, 7)}`;
}

function slug(name) {
    return String(name).toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
}

export function registerArea(Alpine) {
    Alpine.data('areaExplorer', (config) => ({
        name: config.name,
        slug: config.slug,
        selectedFolderId: null,
        selectedFileId: null,
        expanded: {},
        folderQuery: '',
        fileQuery: '',
        viewing: 'current',
        clipboard: null,
        modal: null,
        notice: '',
        draftName: '',
        draftParentId: null,
        draftFile: { reference: '', description: '', rev: '', effective: '' },
        draftRev: { rev: '', effective: '', note: '' },
        draftWorkflow: 'Draft',

        libraryUrl: config.libraryUrl,
        folders: config.folders ?? [],
        files: config.files ?? [],
        history: config.history ?? [],
        areaSecurity: config.areaSecurity ?? ['Admin', 'Editor', 'View Only'],

        async persist() {
            const response = await fetch(this.libraryUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    folders: this.folders,
                    files: this.files,
                    history: this.history.slice(0, 40),
                    areaSecurity: this.areaSecurity,
                }),
            });

            if (!response.ok) {
                this.notice = 'Could not save to the database.';

                return;
            }

            const data = await response.json();
            const mapOf = (kind, id) => {
                if (id == null || id === '') {
                    return id;
                }

                return data.idMap?.[kind]?.[String(id)] ?? id;
            };
            this.selectedFolderId = mapOf('folders', this.selectedFolderId);
            this.selectedFileId = mapOf('files', this.selectedFileId);
            if (this.clipboard?.id != null) {
                this.clipboard.id = mapOf(this.clipboard.kind === 'file' ? 'files' : 'folders', this.clipboard.id);
            }
            const expanded = {};
            Object.entries(this.expanded).forEach(([id, open]) => {
                expanded[mapOf('folders', id)] = open;
            });
            this.expanded = expanded;
            this.folders = data.folders;
            this.files = data.files;
            this.history = data.history;
            this.areaSecurity = data.areaSecurity;
        },

        log(text) {
            this.history.unshift({ id: uid('h'), at: new Date().toLocaleString(), text });
            this.notice = text;
        },

        folder(id) {
            return this.folders.find((item) => item.id === id) ?? null;
        },

        file(id) {
            return this.files.find((item) => item.id === id) ?? null;
        },

        children(parentId) {
            return this.folders
                .filter((folder) => (folder.parentId ?? null) === parentId)
                .sort((a, b) => a.name.localeCompare(b.name));
        },

        branchMatches(folder) {
            const query = this.folderQuery.trim().toLowerCase();
            if (!query) {
                return true;
            }
            if (folder.name.toLowerCase().includes(query)) {
                return true;
            }

            return this.children(folder.id).some((child) => this.branchMatches(child));
        },

        treeRows() {
            const rows = [];
            const walk = (parentId, depth) => {
                this.children(parentId).forEach((folder) => {
                    if (!this.branchMatches(folder)) {
                        return;
                    }
                    rows.push({ ...folder, depth, open: this.expanded[folder.id] !== false, hasChildren: this.children(folder.id).length > 0 });
                    if (this.expanded[folder.id] !== false) {
                        walk(folder.id, depth + 1);
                    }
                });
            };
            walk(null, 0);

            return rows;
        },

        path(folderId) {
            const names = [];
            let current = this.folder(folderId);
            while (current) {
                names.unshift(current.name);
                current = this.folder(current.parentId);
            }

            return names.join(' / ');
        },

        visibleFiles() {
            const query = this.fileQuery.trim().toLowerCase();

            return this.files.filter((file) => {
                if ((file.folderId ?? null) !== this.selectedFolderId) {
                    return false;
                }
                if (this.viewing === 'current' && file.archived) {
                    return false;
                }
                if (!query) {
                    return true;
                }

                return `${file.reference} ${file.description}`.toLowerCase().includes(query);
            });
        },

        selectFolder(id) {
            this.selectedFolderId = id;
            this.selectedFileId = null;
        },

        toggleFolder(id) {
            this.expanded[id] = this.expanded[id] === false;
        },

        startFolder(parentId) {
            this.draftParentId = parentId;
            this.draftName = '';
            this.modal = 'folder';
        },

        async saveFolder() {
            const name = this.draftName.trim();
            if (!name) {
                this.notice = 'Enter a folder name.';

                return;
            }
            const folder = { id: uid('folder'), parentId: this.draftParentId, name, security: ['Admin', 'Editor', 'View Only'] };
            this.folders.push(folder);
            if (folder.parentId) {
                this.expanded[folder.parentId] = true;
            }
            this.selectedFolderId = folder.id;
            this.modal = null;
            this.log(folder.parentId ? `Created subfolder “${name}”.` : `Created folder “${name}”.`);
            await this.persist();
        },

        startFile() {
            this.draftFile = { reference: '', description: '', rev: '0', effective: new Date().toISOString().slice(0, 10) };
            this.modal = 'file';
        },

        async saveFile() {
            const reference = this.draftFile.reference.trim();
            const description = this.draftFile.description.trim();
            if (!reference || !description) {
                this.notice = 'Reference no. and description are required.';

                return;
            }
            const file = {
                id: uid('file'),
                folderId: this.selectedFolderId,
                reference,
                description,
                rev: this.draftFile.rev.trim() || '0',
                effective: this.draftFile.effective,
                archived: false,
                workflow: 'Draft',
                versions: [],
            };
            this.files.push(file);
            this.selectedFileId = file.id;
            this.modal = null;
            this.log(`Added “${reference}”.`);
            await this.persist();
        },

        selectedKind() {
            if (this.selectedFileId) {
                return 'file';
            }
            if (this.selectedFolderId) {
                return 'folder';
            }

            return null;
        },

        async trash() {
            const kind = this.selectedKind();
            if (!kind) {
                this.notice = 'Select a folder or a document first.';

                return;
            }
            if (kind === 'file') {
                const file = this.file(this.selectedFileId);
                this.files = this.files.filter((item) => item.id !== file.id);
                this.selectedFileId = null;
                this.log(`Moved “${file.reference}” to trash.`);
            } else {
                const folder = this.folder(this.selectedFolderId);
                const ids = this.descendantIds(folder.id);
                this.folders = this.folders.filter((item) => !ids.includes(item.id));
                this.files = this.files.filter((item) => !ids.includes(item.folderId));
                this.selectedFolderId = folder.parentId ?? null;
                this.log(`Removed folder “${folder.name}”.`);
            }
            await this.persist();
        },

        descendantIds(id) {
            const ids = [id];
            this.children(id).forEach((child) => ids.push(...this.descendantIds(child.id)));

            return ids;
        },

        cut() {
            this.copy('cut');
        },

        copy(mode = 'copy') {
            const kind = this.selectedKind();
            if (!kind) {
                this.notice = 'Select a folder or a document first.';

                return;
            }
            this.clipboard = { mode, kind, id: kind === 'file' ? this.selectedFileId : this.selectedFolderId };
            const label = kind === 'file' ? this.file(this.clipboard.id).reference : this.folder(this.clipboard.id).name;
            this.notice = mode === 'cut' ? `Cut “${label}”. Choose where to paste it.` : `Copied “${label}”.`;
        },

        paste() {
            if (!this.clipboard) {
                this.notice = 'Nothing to paste. Cut or copy first.';

                return;
            }
            if (this.clipboard.kind === 'file') {
                this.pasteFile();
            } else {
                this.pasteFolder();
            }
        },

        async pasteFile() {
            const source = this.file(this.clipboard.id);
            if (!source) {
                this.clipboard = null;
                this.notice = 'That document is no longer here.';

                return;
            }
            if (this.clipboard.mode === 'cut') {
                source.folderId = this.selectedFolderId;
                this.log(`Moved “${source.reference}”.`);
                this.clipboard = null;
            } else {
                const copy = { ...source, id: uid('file'), folderId: this.selectedFolderId, versions: [...(source.versions ?? [])] };
                this.files.push(copy);
                this.selectedFileId = copy.id;
                this.log(`Pasted a copy of “${source.reference}”.`);
            }
            await this.persist();
        },

        async pasteFolder() {
            const source = this.folder(this.clipboard.id);
            if (!source) {
                this.clipboard = null;
                this.notice = 'That folder is no longer here.';

                return;
            }
            const target = this.selectedFolderId;
            if (target && this.descendantIds(source.id).includes(target)) {
                this.notice = 'A folder cannot be pasted inside itself.';

                return;
            }
            if (this.clipboard.mode === 'cut') {
                source.parentId = target;
                this.selectedFolderId = source.id;
                this.log(`Moved folder “${source.name}”.`);
                this.clipboard = null;
            } else {
                const created = this.duplicateFolder(source.id, target);
                this.selectedFolderId = created;
                this.log(`Pasted a copy of “${source.name}”.`);
            }
            if (target) {
                this.expanded[target] = true;
            }
            await this.persist();
        },

        duplicateFolder(sourceId, parentId) {
            const source = this.folder(sourceId);
            const copy = { ...source, id: uid('folder'), parentId };
            this.folders.push(copy);
            this.files.filter((file) => file.folderId === sourceId).forEach((file) => {
                this.files.push({ ...file, id: uid('file'), folderId: copy.id, versions: [...(file.versions ?? [])] });
            });
            this.children(sourceId).forEach((child) => this.duplicateFolder(child.id, copy.id));

            return copy.id;
        },

        openVersion() {
            const file = this.file(this.selectedFileId);
            if (!file) {
                this.notice = 'Select a document first.';

                return;
            }
            this.draftRev = { rev: '', effective: new Date().toISOString().slice(0, 10), note: '' };
            this.modal = 'version';
        },

        async saveVersion() {
            const file = this.file(this.selectedFileId);
            const rev = this.draftRev.rev.trim();
            if (!file || !rev) {
                this.notice = 'Enter the new revision.';

                return;
            }
            file.versions = [...(file.versions ?? []), { rev: file.rev, effective: file.effective, at: new Date().toLocaleString() }];
            file.rev = rev;
            file.effective = this.draftRev.effective;
            this.modal = null;
            this.log(`Saved revision ${rev} for “${file.reference}”.`);
            await this.persist();
        },

        openSecurity() {
            this.modal = 'security';
        },

        securityList() {
            const folder = this.folder(this.selectedFolderId);

            return folder ? (folder.security ?? ['Admin', 'Editor', 'View Only']) : this.areaSecurity;
        },

        async toggleSecurity(role) {
            const folder = this.folder(this.selectedFolderId);
            const list = new Set(this.securityList());
            if (list.has(role)) {
                list.delete(role);
            } else {
                list.add(role);
            }
            const next = ['Admin', 'Editor', 'View Only'].filter((item) => list.has(item));
            if (folder) {
                folder.security = next;
            } else {
                this.areaSecurity = next;
            }
            await this.persist();
        },

        openWorkflow() {
            const file = this.file(this.selectedFileId);
            if (!file) {
                this.notice = 'Select a document first.';

                return;
            }
            this.draftWorkflow = file.workflow || 'Draft';
            this.modal = 'workflow';
        },

        async saveWorkflow() {
            const file = this.file(this.selectedFileId);
            if (!file) {
                return;
            }
            file.workflow = this.draftWorkflow;
            file.archived = this.draftWorkflow === 'Obsolete';
            this.modal = null;
            this.log(`“${file.reference}” is now ${file.workflow}.`);
            await this.persist();
        },
    }));
}
