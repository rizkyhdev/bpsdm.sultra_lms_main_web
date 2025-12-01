<!-- Module Add/Edit Modal -->
<div class="modal fade" id="moduleModal" tabindex="-1" aria-labelledby="moduleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="moduleModalLabel">Add Module</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="moduleForm" method="POST">
        @csrf
        <input type="hidden" name="_method" id="moduleMethod" value="POST">
        <div class="modal-body">
          <div id="moduleErrors" class="alert alert-danger" style="display: none;"></div>
          
          <div class="mb-3">
            <label class="form-label">Judul Module <span class="text-danger">*</span></label>
            <input type="text" name="judul" id="moduleJudul" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" id="moduleDeskripsi" class="form-control" rows="3"></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Urutan <span class="text-danger">*</span></label>
            <input type="number" name="urutan" id="moduleUrutan" class="form-control" min="1" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i>Save
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Module Delete Confirmation Modal -->
<div class="modal fade" id="moduleDeleteModal" tabindex="-1" aria-labelledby="moduleDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="moduleDeleteModalLabel">Delete Module</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="moduleDeleteForm" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          <p>Are you sure you want to delete this module?</p>
          <p class="text-danger"><strong>This will also delete:</strong></p>
          <ul class="text-danger">
            <li>All sub-modules</li>
            <li>All contents</li>
            <li>All quizzes and questions</li>
          </ul>
          <p><strong>This action cannot be undone!</strong></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

