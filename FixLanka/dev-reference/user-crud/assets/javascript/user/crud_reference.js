/*
 * User CRUD reference JavaScript
 *
 * Demonstrates:
 * - fetch helper
 * - create/update/delete flow
 * - simple table rendering
 * - loading joined insight data
 */

document.addEventListener('DOMContentLoaded', () => {
    const API_BASE = '/2nd-Year-Group-Project/FixLanka/dev-reference/user-crud/controllers/user/crud_reference_controller.php';

    const noteForm = document.getElementById('noteForm');
    const noteId = document.getElementById('noteId');
    const noteTitle = document.getElementById('noteTitle');
    const noteBody = document.getElementById('noteBody');
    const priorityLevel = document.getElementById('priorityLevel');
    const resetBtn = document.getElementById('resetBtn');
    const noteMessage = document.getElementById('noteMessage');
    const notesTbody = document.getElementById('notesTbody');
    const refreshInsightsBtn = document.getElementById('refreshInsightsBtn');
    const insightsTbody = document.getElementById('insightsTbody');

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    async function fetchJson(url, options) {
        const response = await fetch(url, {
            credentials: 'same-origin',
            ...(options || {})
        });

        const text = await response.text();
        let data;

        try {
            data = JSON.parse(text);
        } catch (error) {
            throw new Error('Invalid JSON response');
        }

        if (!response.ok || data?.success === false) {
            throw new Error(data?.message || `Request failed (${response.status})`);
        }

        return data;
    }

    function showMessage(message, isError) {
        noteMessage.textContent = message;
        noteMessage.className = isError ? 'message error' : 'message success';
    }

    function resetForm() {
        noteId.value = '';
        noteTitle.value = '';
        noteBody.value = '';
        priorityLevel.value = 'medium';
    }

    function renderNotes(notes) {
        if (!Array.isArray(notes) || notes.length === 0) {
            notesTbody.innerHTML = '<tr><td colspan="5">No notes found.</td></tr>';
            return;
        }

        notesTbody.innerHTML = notes.map((row) => {
            const rowData = encodeURIComponent(JSON.stringify(row));
            return `
                <tr>
                    <td>${Number(row.note_id) || 0}</td>
                    <td>${escapeHtml(row.title || '')}</td>
                    <td><span class="pill ${escapeHtml(row.priority_level || 'medium')}">${escapeHtml(row.priority_level || 'medium')}</span></td>
                    <td>${escapeHtml(row.created_at || '')}</td>
                    <td>
                        <button data-edit="${rowData}">Edit</button>
                        <button data-delete="${Number(row.note_id) || 0}" class="danger">Delete</button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function renderInsights(rows) {
        if (!Array.isArray(rows) || rows.length === 0) {
            insightsTbody.innerHTML = '<tr><td colspan="7">No rows.</td></tr>';
            return;
        }

        insightsTbody.innerHTML = rows.map((row) => `
            <tr>
                <td>${Number(row.request_id) || 0}</td>
                <td>${escapeHtml(row.job_title || '')}</td>
                <td>${escapeHtml(row.job_status || '')}</td>
                <td>${escapeHtml(row.priority_level || '')}</td>
                <td>${escapeHtml(row.agreed_amount || 0)}</td>
                <td>${escapeHtml(row.selected_provider_type || '')}</td>
                <td>${escapeHtml(row.user_rating || 0)}</td>
            </tr>
        `).join('');
    }

    async function loadNotes() {
        const result = await fetchJson(`${API_BASE}?action=list_notes`);
        renderNotes(result.notes || []);
    }

    async function loadInsights() {
        const result = await fetchJson(`${API_BASE}?action=insights`);
        renderInsights(result.rows || []);
    }

    // Submit handles both Create and Update based on hidden note_id.
    noteForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const payload = {
            note_id: noteId.value ? Number(noteId.value) : 0,
            title: noteTitle.value.trim(),
            body: noteBody.value.trim(),
            priority_level: priorityLevel.value,
        };

        if (!payload.title) {
            showMessage('Title is required.', true);
            return;
        }

        const action = payload.note_id > 0 ? 'update_note' : 'create_note';

        try {
            const result = await fetchJson(`${API_BASE}?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            showMessage(result.message || 'Saved.', false);
            resetForm();
            await loadNotes();
        } catch (error) {
            showMessage(error.message || 'Save failed.', true);
        }
    });

    notesTbody.addEventListener('click', async (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) return;

        const editPayload = target.getAttribute('data-edit');
        const deleteId = target.getAttribute('data-delete');

        if (editPayload) {
            const row = JSON.parse(decodeURIComponent(editPayload));
            noteId.value = String(row.note_id || '');
            noteTitle.value = row.title || '';
            noteBody.value = row.body || '';
            priorityLevel.value = row.priority_level || 'medium';
            showMessage('Edit mode loaded. Update and click Save.', false);
            return;
        }

        if (deleteId) {
            const confirmed = window.confirm('Delete this note?');
            if (!confirmed) return;

            try {
                const result = await fetchJson(`${API_BASE}?action=delete_note`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ note_id: Number(deleteId) }),
                });

                showMessage(result.message || 'Deleted.', false);
                await loadNotes();
            } catch (error) {
                showMessage(error.message || 'Delete failed.', true);
            }
        }
    });

    resetBtn.addEventListener('click', () => {
        resetForm();
        showMessage('Form reset.', false);
    });

    refreshInsightsBtn.addEventListener('click', async () => {
        try {
            await loadInsights();
            showMessage('Insights refreshed.', false);
        } catch (error) {
            showMessage(error.message || 'Insights load failed.', true);
        }
    });

    (async () => {
        try {
            await loadNotes();
            await loadInsights();
        } catch (error) {
            showMessage(error.message || 'Initial load failed.', true);
        }
    })();
});
