@extends('layouts.app', ['header' => 'Task Board', 'subheader' => 'Fast, focused workspace with live updates'])

@push('styles')
<style>
  .dashboard-hero {
    background: linear-gradient(130deg, #0d1f2a 0%, #134e4a 62%, #0f766e 100%);
    color: #f4fffc;
    border-radius: 18px;
    padding: 20px;
  }
  .hero-chip {
    background: rgba(255, 255, 255, .18);
    border: 1px solid rgba(255, 255, 255, .22);
    border-radius: 999px;
    font-size: 12px;
    padding: 4px 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .metric-card {
    border: 1px solid rgba(18, 36, 42, .08);
    border-radius: 14px;
    background: #fff;
    padding: 14px;
  }
  .metric-label { color: #5e6f75; font-size: 12px; }
  .metric-value { font-size: 26px; font-weight: 800; line-height: 1; }
  .toolbar-card {
    border: 1px solid rgba(18, 36, 42, .08);
    border-radius: 14px;
    background: #fff;
    padding: 14px;
  }
  .tab-btn {
    border: 1px solid rgba(18, 36, 42, .12);
    background: #fff;
    color: #20323a;
    border-radius: 999px;
    padding: 6px 12px;
    font-weight: 700;
  }
  .tab-btn.active {
    background: #0f766e;
    color: #fff;
    border-color: #0f766e;
  }
  .table-wrap {
    border: 1px solid rgba(18, 36, 42, .08);
    border-radius: 14px;
    overflow: hidden;
    background: #fff;
  }
  .loading-overlay {
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, .72);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 2;
  }
  .loading-overlay.show { display: flex; }
</style>
@endpush

@section('content')
<div class="dashboard-hero mb-3 mb-lg-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
      <div class="hero-chip mb-2">Private Workspace</div>
      <h2 class="h4 mb-1 text-white">Your tasks, streamlined</h2>
      <p class="mb-0 opacity-75 small">AJAX filters, quick actions, and clean organization without page reloads.</p>
    </div>
    <button class="btn btn-light fw-semibold" data-bs-toggle="modal" data-bs-target="#createModal">+ New Task</button>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-12 col-md-4">
    <div class="metric-card">
      <div class="metric-label mb-1">All Tasks</div>
      <div id="count-all" class="metric-value">{{ $counts['all'] }}</div>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="metric-card">
      <div class="metric-label mb-1">Pending</div>
      <div id="count-pending" class="metric-value">{{ $counts['pending'] }}</div>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="metric-card">
      <div class="metric-label mb-1">Done</div>
      <div id="count-done" class="metric-value">{{ $counts['done'] }}</div>
    </div>
  </div>
</div>

<div class="toolbar-card mb-3">
  <div class="d-flex flex-wrap gap-2 mb-3" id="taskTabs">
    <button class="tab-btn @if($tab === 'all') active @endif" type="button" data-tab="all">All</button>
    <button class="tab-btn @if($tab === 'pending') active @endif" type="button" data-tab="pending">Pending</button>
    <button class="tab-btn @if($tab === 'done') active @endif" type="button" data-tab="done">Done</button>
  </div>

  <form id="filtersForm" class="row g-2 align-items-center">
    <input type="hidden" name="tab" value="{{ $tab }}">
    <div class="col-12 col-md-5">
      <input id="searchInput" type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Search by title, notes, description...">
    </div>
    <div class="col-6 col-md-3">
      <select id="statusFilter" name="status" class="form-select">
        <option value="" @selected($status === '')>All Status</option>
        <option value="pending" @selected($status==='pending')>Pending</option>
        <option value="done" @selected($status==='done')>Done</option>
      </select>
    </div>
    <div class="col-6 col-md-2">
      <select id="perPageFilter" name="per_page" class="form-select">
        @foreach([10,25,50] as $n)
          <option value="{{ $n }}" @selected($perPage===$n)>{{ $n }}/page</option>
        @endforeach
      </select>
    </div>
    <div class="col-12 col-md-2 d-grid">
      <button id="clearBtn" class="btn btn-outline-secondary" type="button">Clear</button>
    </div>
  </form>
</div>

<div class="position-relative table-wrap">
  <div id="loadingOverlay" class="loading-overlay">
    <div class="spinner-border text-success" role="status"></div>
  </div>

  <div id="tasksContent" class="p-3">
    @include('tasks.partials.table')
  </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="createTaskForm" method="POST" action="{{ route('tasks.store') }}">
        @csrf
        <div class="modal-body">
          <div id="createErrors" class="alert alert-danger d-none py-2"></div>

          <div class="mb-3">
            <label class="form-label">Title</label>
            <input name="title" class="form-control" required>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label">Priority</label>
              <select name="priority" class="form-select" required>
                <option value="low">Low</option>
                <option value="medium" selected>Medium</option>
                <option value="high">High</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Due Date</label>
              <input type="date" name="due_date" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Status</label>
              <select name="status" class="form-select" required>
                <option value="pending" selected>Pending</option>
                <option value="done">Done</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
          </div>

          <div class="mb-0">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-dark" type="submit">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="editTaskForm" method="POST" action="#">
        @csrf
        @method('PUT')

        <div class="modal-body">
          <div id="editErrors" class="alert alert-danger d-none py-2"></div>

          <div class="mb-3">
            <label class="form-label">Title</label>
            <input id="editTitle" name="title" class="form-control" required>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label">Priority</label>
              <select id="editPriority" name="priority" class="form-select" required>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Due Date</label>
              <input id="editDueDate" type="date" name="due_date" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Status</label>
              <select id="editStatus" name="status" class="form-select" required>
                <option value="pending">Pending</option>
                <option value="done">Done</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea id="editNotes" name="notes" class="form-control" rows="3"></textarea>
          </div>

          <div class="mb-0">
            <label class="form-label">Description</label>
            <textarea id="editDescription" name="description" class="form-control" rows="3"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-dark" type="submit">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
  const filtersForm = document.getElementById('filtersForm');
  const tasksContent = document.getElementById('tasksContent');
  const searchInput = document.getElementById('searchInput');
  const statusFilter = document.getElementById('statusFilter');
  const perPageFilter = document.getElementById('perPageFilter');
  const clearBtn = document.getElementById('clearBtn');
  const loadingOverlay = document.getElementById('loadingOverlay');
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  const createModal = new bootstrap.Modal(document.getElementById('createModal'));
  const editModal = new bootstrap.Modal(document.getElementById('editModal'));
  const tabRoot = document.getElementById('taskTabs');

  const countNodes = {
    all: document.getElementById('count-all'),
    pending: document.getElementById('count-pending'),
    done: document.getElementById('count-done'),
  };

  let searchDebounce;
  let fetchController;

  function formParams(overrides = {}) {
    const params = new URLSearchParams(new FormData(filtersForm));
    Object.entries(overrides).forEach(([k, v]) => {
      if (v === null || v === undefined || v === '') {
        params.delete(k);
      } else {
        params.set(k, v);
      }
    });
    return params;
  }

  function setTab(tab) {
    filtersForm.querySelector('input[name="tab"]').value = tab;
    tabRoot.querySelectorAll('.tab-btn').forEach((btn) => {
      btn.classList.toggle('active', btn.dataset.tab === tab);
    });
  }

  function setLoading(state) {
    loadingOverlay.classList.toggle('show', state);
  }

  async function fetchTasks({ pushState = true, page = null } = {}) {
    const params = formParams({ page });

    if (fetchController) {
      fetchController.abort();
    }
    fetchController = new AbortController();

    setLoading(true);

    try {
      const response = await fetch(`{{ route('tasks.data') }}?${params.toString()}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        signal: fetchController.signal,
      });

      if (!response.ok) {
        showToast('Failed to load tasks.', 'error');
        return;
      }

      const payload = await response.json();
      tasksContent.innerHTML = payload.html;
      countNodes.all.textContent = payload.counts.all;
      countNodes.pending.textContent = payload.counts.pending;
      countNodes.done.textContent = payload.counts.done;

      if (pushState) {
        const url = new URL(window.location.href);
        url.search = params.toString();
        window.history.pushState({}, '', url);
      }
    } catch (error) {
      if (error.name !== 'AbortError') {
        showToast('Network error.', 'error');
      }
    } finally {
      setLoading(false);
    }
  }

  window.reloadTasksData = () => fetchTasks({ pushState: false });

  tabRoot.addEventListener('click', (event) => {
    const target = event.target.closest('.tab-btn');
    if (!target) return;
    setTab(target.dataset.tab);
    fetchTasks();
  });

  statusFilter.addEventListener('change', () => fetchTasks());
  perPageFilter.addEventListener('change', () => fetchTasks());

  searchInput.addEventListener('input', () => {
    clearTimeout(searchDebounce);
    searchDebounce = setTimeout(() => fetchTasks(), 350);
  });

  clearBtn.addEventListener('click', () => {
    searchInput.value = '';
    statusFilter.value = '';
    perPageFilter.value = '10';
    setTab('all');
    fetchTasks();
  });

  filtersForm.addEventListener('submit', (event) => {
    event.preventDefault();
    fetchTasks();
  });

  tasksContent.addEventListener('click', async (event) => {
    const pageLink = event.target.closest('#paginationWrapper a');
    if (pageLink) {
      event.preventDefault();
      const page = new URL(pageLink.href).searchParams.get('page') || '1';
      await fetchTasks({ page });
      return;
    }

    const statusBtn = event.target.closest('.btn-ajax-status');
    if (statusBtn) {
      event.preventDefault();
      await postForm(statusBtn.dataset.url, { status: statusBtn.dataset.status });
      await fetchTasks({ pushState: false });
      return;
    }

    const deleteBtn = event.target.closest('.btn-ajax-delete');
    if (deleteBtn) {
      event.preventDefault();
      const result = await Swal.fire({
        title: 'Delete this task?',
        text: "You won't be able to undo this.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it',
      });

      if (result.isConfirmed) {
        await postForm(deleteBtn.dataset.url, { _method: 'DELETE' });
        await fetchTasks({ pushState: false });
      }
      return;
    }

    const editBtn = event.target.closest('.btn-edit');
    if (editBtn) {
      document.getElementById('editTaskForm').action = editBtn.dataset.updateUrl;
      document.getElementById('editTitle').value = editBtn.dataset.title;
      document.getElementById('editDescription').value = editBtn.dataset.description;
      document.getElementById('editNotes').value = editBtn.dataset.notes;
      document.getElementById('editStatus').value = editBtn.dataset.status;
      document.getElementById('editPriority').value = editBtn.dataset.priority;
      document.getElementById('editDueDate').value = editBtn.dataset.dueDate;
      document.getElementById('editErrors').classList.add('d-none');
      return;
    }

    if (event.target.closest('#clearFiltersBtn')) {
      clearBtn.click();
    }
  });

  tasksContent.addEventListener('change', async (event) => {
    if (event.target.matches('#selectAll')) {
      tasksContent.querySelectorAll('.task-checkbox').forEach((checkbox) => {
        checkbox.checked = event.target.checked;
      });
      return;
    }

    if (event.target.matches('.quick-status-toggle')) {
      const next = event.target.checked ? 'done' : 'pending';
      await postForm(event.target.dataset.url, { _method: 'PATCH', status: next });
      await fetchTasks({ pushState: false });
    }
  });

  tasksContent.addEventListener('submit', async (event) => {
    const bulkForm = event.target.closest('#bulkForm');
    if (!bulkForm) return;

    event.preventDefault();

    const formData = new FormData(bulkForm);
    if (!formData.getAll('task_ids[]').length) {
      showToast('Select at least one task.', 'info');
      return;
    }

    const response = await fetch(`{{ route('tasks.bulk-action') }}`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData,
    });

    const payload = await response.json().catch(() => ({}));
    if (!response.ok) {
      showToast(payload.message || 'Bulk action failed.', 'error');
      return;
    }

    showToast(payload.message || 'Bulk action completed.');
    await fetchTasks({ pushState: false });
  });

  document.getElementById('createTaskForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    const errorBox = document.getElementById('createErrors');
    errorBox.classList.add('d-none');

    const response = await fetch(form.action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: new FormData(form)
    });

    const payload = await response.json().catch(() => ({}));

    if (response.status === 422) {
      errorBox.innerHTML = Object.values(payload.errors || {}).flat().map((e) => `<div>${e}</div>`).join('');
      errorBox.classList.remove('d-none');
      return;
    }

    if (!response.ok) {
      showToast('Task creation failed.', 'error');
      return;
    }

    form.reset();
    createModal.hide();
    showToast(payload.message || 'Task created.');
    await fetchTasks({ pushState: false });
  });

  document.getElementById('editTaskForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    const errorBox = document.getElementById('editErrors');
    errorBox.classList.add('d-none');

    const data = new FormData(form);
    data.append('_method', 'PUT');

    const response = await fetch(form.action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: data
    });

    const payload = await response.json().catch(() => ({}));

    if (response.status === 422) {
      errorBox.innerHTML = Object.values(payload.errors || {}).flat().map((e) => `<div>${e}</div>`).join('');
      errorBox.classList.remove('d-none');
      return;
    }

    if (!response.ok) {
      showToast('Task update failed.', 'error');
      return;
    }

    editModal.hide();
    showToast(payload.message || 'Task updated.');
    await fetchTasks({ pushState: false });
  });

  async function postForm(url, fields) {
    const data = new FormData();
    Object.entries(fields).forEach(([key, value]) => data.append(key, value));

    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: data
    });

    const payload = await response.json().catch(() => ({}));

    if (!response.ok) {
      showToast(payload.message || 'Action failed.', 'error');
      return false;
    }

    showToast(payload.message || 'Updated.');
    return true;
  }
})();
</script>
@endpush
