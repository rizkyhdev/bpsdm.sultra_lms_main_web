<!-- SubModule Add/Edit Modal -->
<div class="modal fade" id="subModuleModal" tabindex="-1" aria-labelledby="subModuleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="subModuleModalLabel">Add Sub-Module</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="subModuleForm" method="POST">
        @csrf
        <input type="hidden" name="_method" id="subModuleMethod" value="POST">
        <div class="modal-body">
          <div id="subModuleErrors" class="alert alert-danger" style="display: none;"></div>
          
          <div class="mb-3">
            <label class="form-label">Judul Sub-Module <span class="text-danger">*</span></label>
            <input type="text" name="judul" id="subModuleJudul" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" id="subModuleDeskripsi" class="form-control" rows="3"></textarea>
          </div>

          <!-- Order is managed automatically and via drag-and-drop, so we hide this from the user -->
          <input type="hidden" name="urutan" id="subModuleUrutan" value="">
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

<!-- SubModule Delete Confirmation Modal -->
<div class="modal fade" id="subModuleDeleteModal" tabindex="-1" aria-labelledby="subModuleDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="subModuleDeleteModalLabel">Delete Sub-Module</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="subModuleDeleteForm" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          <p>Are you sure you want to delete this sub-module?</p>
          <p class="text-danger"><strong>This will also delete:</strong></p>
          <ul class="text-danger">
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

