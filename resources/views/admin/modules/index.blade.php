@extends('layouts.admin')

@section('title', 'Modul Kursus')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Kursus</a></li>
    <li class="breadcrumb-item active">Modul</li>
@endsection

@section('header-actions')
    @if(isset($course))
        <a href="{{ route('admin.modules.create', ['course' => $course->id]) }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Tambah Modul</a>
    @endif
@endsection

@section('content')
    {{-- Header konteks kursus --}}
    @if(isset($course))
        <div class="alert alert-light border">
            <strong>Kursus:</strong> {{ $course->judul }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                <tr>
                    <th>Urutan</th>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th class="text-right">Aksi</th>
                </tr>
                </thead>
                <tbody id="admin-module-order-body">
                @forelse($modules as $m)
                    <tr class="admin-module-row" data-id="{{ $m->id }}" draggable="true">
                        <td>
                            <i class="fas fa-grip-vertical text-muted mr-2"></i>
                            <span class="badge badge-secondary order-badge">{{ $m->urutan }}</span>
                        </td>
                        <td><a href="{{ route('admin.modules.show', $m) }}">{{ $m->judul }}</a></td>
                        <td>{{ \Illuminate\Support\Str::limit($m->deskripsi, 100) }}</td>
                        <td class="text-right">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.modules.show', $m) }}" class="btn btn-outline-secondary"><i class="fas fa-eye"></i></a>
                                @if(isset($course))
                                    <a href="{{ route('admin.modules.create', ['course' => $course->id]) }}" class="btn btn-outline-success" title="Tambah"><i class="fas fa-plus"></i></a>
                                @endif
                                <a href="{{ route('admin.modules.edit', $m) }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <button class="btn btn-outline-danger" data-toggle="modal" data-target="#confirmDeleteModal" data-action="{{ route('admin.modules.destroy', $m) }}"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">
            @include('partials._pagination', ['collection' => $modules])
        </div>
    </div>

    {{-- Modal konfirmasi hapus --}}
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">Apakah Anda yakin ingin menghapus data ini?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <form id="deleteFormModule" method="POST" action="#">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
$('#confirmDeleteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var action = button.data('action');
    $('#deleteFormModule').attr('action', action);
});

document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('admin-module-order-body');
    if (!tbody) return;

    let dragSrcEl = null;

    function handleDragStart(e) {
        dragSrcEl = this;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.outerHTML);
        this.classList.add('table-active');
    }

    function handleDragOver(e) {
        if (e.preventDefault) {
            e.preventDefault();
        }
        e.dataTransfer.dropEffect = 'move';
        return false;
    }

    function handleDragEnter() {
        this.classList.add('table-warning');
    }

    function handleDragLeave() {
        this.classList.remove('table-warning');
    }

    function handleDrop(e) {
        if (e.stopPropagation) {
            e.stopPropagation();
        }

        if (dragSrcEl !== this) {
            this.parentNode.removeChild(dragSrcEl);
            const dropHTML = e.dataTransfer.getData('text/html');
            this.insertAdjacentHTML('beforebegin', dropHTML);
            const droppedRow = this.previousSibling;
            addDnDHandlers(droppedRow);
            saveNewOrder();
        }
        this.classList.remove('table-warning');
        return false;
    }

    function handleDragEnd() {
        this.classList.remove('table-active');
        const rows = tbody.querySelectorAll('.admin-module-row');
        rows.forEach(function (row) {
            row.classList.remove('table-warning');
        });
    }

    function addDnDHandlers(row) {
        row.addEventListener('dragstart', handleDragStart);
        row.addEventListener('dragenter', handleDragEnter);
        row.addEventListener('dragover', handleDragOver);
        row.addEventListener('dragleave', handleDragLeave);
        row.addEventListener('drop', handleDrop);
        row.addEventListener('dragend', handleDragEnd);
    }

    function saveNewOrder() {
        const rows = tbody.querySelectorAll('.admin-module-row');
        rows.forEach(function (row, index) {
            const id = row.getAttribute('data-id');
            const newOrder = index + 1;

            const badge = row.querySelector('.order-badge');
            if (badge) {
                badge.textContent = newOrder;
            }

            fetch("{{ url('admin/modules') }}/" + id + "/reorder", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    module_id: parseInt(id, 10),
                    new_order: newOrder
                }),
            }).then(function (response) {
                if (!response.ok) {
                    console.error('Failed to save order for module ' + id);
                }
            }).catch(function (error) {
                console.error('Error saving order for module ' + id, error);
            });
        });
    }

    const rows = tbody.querySelectorAll('.admin-module-row');
    rows.forEach(function (row) {
        addDnDHandlers(row);
    });
});
</script>
@endsection


