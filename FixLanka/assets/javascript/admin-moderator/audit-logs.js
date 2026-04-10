(function () {
  const config = window.FIXLANKA_AUDIT_LOGS || {};
  const apiUrl = config.apiUrl || '/2nd-Year-Group-Project/FixLanka/api/audit-logs.php';

  let page = 1;
  let pages = 1;
  let total = 0;

  function $(id) {
    return document.getElementById(id);
  }

  function setMessage(text) {
    const el = $('auditMessage');
    if (!el) return;
    if (!text) {
      el.style.display = 'none';
      el.textContent = '';
      return;
    }
    el.style.display = 'block';
    el.textContent = text;
  }

  function escapeJsonForDisplay(value) {
    try {
      if (value === null || value === undefined) return '';
      if (typeof value === 'string') return value;
      return JSON.stringify(value, null, 2);
    } catch (e) {
      return '';
    }
  }

  function renderRows(rows) {
    const tbody = $('auditTableBody');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (!rows || rows.length === 0) {
      const tr = document.createElement('tr');
      const td = document.createElement('td');
      td.colSpan = 8;
      td.className = 'audit-logs-loading';
      td.textContent = 'No logs found.';
      tr.appendChild(td);
      tbody.appendChild(tr);
      return;
    }

    rows.forEach((r) => {
      const tr = document.createElement('tr');

      const time = document.createElement('td');
      time.textContent = r.occurred_at || '';

      const actor = document.createElement('td');
      const actorText = [r.actor_role, r.actor_user_id].filter((v) => v !== null && v !== undefined && v !== '').join('#');
      actor.textContent = actorText || 'system';

      const action = document.createElement('td');
      action.textContent = r.action || '';

      const entity = document.createElement('td');
      const entityText = [r.entity_type, r.entity_id].filter((v) => v !== null && v !== undefined && v !== '').join('#');
      entity.textContent = entityText || '';

      const endpoint = document.createElement('td');
      const endpointText = [r.http_method, r.endpoint].filter(Boolean).join(' ');
      endpoint.textContent = endpointText;

      const ip = document.createElement('td');
      ip.textContent = r.ip_address || '';

      const status = document.createElement('td');
      status.textContent = (r.status_code !== null && r.status_code !== undefined) ? String(r.status_code) : '';

      const details = document.createElement('td');
      const detailsEl = document.createElement('details');
      detailsEl.className = 'audit-logs-details';

      const summary = document.createElement('summary');
      summary.textContent = 'View';

      const pre = document.createElement('pre');
      pre.className = 'audit-logs-code';
      pre.textContent = escapeJsonForDisplay(r.details);

      detailsEl.appendChild(summary);
      detailsEl.appendChild(pre);
      details.appendChild(detailsEl);

      tr.appendChild(time);
      tr.appendChild(actor);
      tr.appendChild(action);
      tr.appendChild(entity);
      tr.appendChild(endpoint);
      tr.appendChild(ip);
      tr.appendChild(status);
      tr.appendChild(details);

      tbody.appendChild(tr);
    });
  }

  function updatePagination() {
    const prevBtn = $('auditPrev');
    const nextBtn = $('auditNext');
    const pageText = $('auditPageText');
    const totalText = $('auditTotalText');

    if (pageText) pageText.textContent = `Page ${page}${pages ? ` of ${pages}` : ''}`;
    if (totalText) totalText.textContent = `${total} total`;

    if (prevBtn) prevBtn.disabled = page <= 1;
    if (nextBtn) nextBtn.disabled = page >= pages;
  }

  async function loadLogs() {
    setMessage('');

    const limitEl = $('auditLimit');
    const limit = limitEl ? parseInt(limitEl.value, 10) : 50;

    const tbody = $('auditTableBody');
    if (tbody) {
      tbody.innerHTML = '<tr><td colspan="8" class="audit-logs-loading">Loading logs...</td></tr>';
    }

    try {
      const url = new URL(apiUrl, window.location.origin);
      url.searchParams.set('page', String(page));
      url.searchParams.set('limit', String(limit));

      const res = await fetch(url.toString(), { credentials: 'same-origin' });

      let json = null;
      try {
        json = await res.json();
      } catch (_) {
        json = null;
      }

      if (!res.ok) {
        if (res.status === 403) {
          setMessage('Forbidden: admin access required.');
        } else if (json && typeof json.message === 'string' && json.message.trim() !== '') {
          setMessage(json.message);
        } else {
          setMessage(`Failed to load logs (HTTP ${res.status}).`);
        }
        renderRows([]);
        pages = 1;
        total = 0;
        updatePagination();
        return;
      }

      if (!json || json.success !== true) {
        const msg = (json && typeof json.message === 'string' && json.message.trim() !== '')
          ? json.message
          : 'Failed to load logs.';
        setMessage(msg);
        renderRows([]);
        pages = 1;
        total = 0;
        updatePagination();
        return;
      }

      renderRows(json.data || []);

      const meta = json.meta || {};
      page = Number(meta.page || page);
      pages = Number(meta.pages || 1);
      total = Number(meta.total || 0);

      updatePagination();
    } catch (e) {
      setMessage('Error loading logs.');
      renderRows([]);
      pages = 1;
      total = 0;
      updatePagination();
    }
  }

  function bind() {
    const prevBtn = $('auditPrev');
    const nextBtn = $('auditNext');
    const refreshBtn = $('auditRefresh');
    const limitEl = $('auditLimit');

    if (prevBtn) {
      prevBtn.addEventListener('click', function () {
        if (page > 1) {
          page -= 1;
          loadLogs();
        }
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', function () {
        if (page < pages) {
          page += 1;
          loadLogs();
        }
      });
    }

    if (refreshBtn) {
      refreshBtn.addEventListener('click', function () {
        loadLogs();
      });
    }

    if (limitEl) {
      limitEl.addEventListener('change', function () {
        page = 1;
        loadLogs();
      });
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    bind();
    loadLogs();
  });
})();
