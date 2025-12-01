<!-- Quiz Add/Edit Modal -->
<div class="modal fade" id="quizModal" tabindex="-1" aria-labelledby="quizModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="quizModalLabel">Add Quiz</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="quizForm" method="POST">
        @csrf
        <input type="hidden" name="_method" id="quizMethod" value="POST">
        <div class="modal-body">
          <div id="quizErrors" class="alert alert-danger" style="display: none;"></div>
          
          <div class="mb-3">
            <label class="form-label">Judul <span class="text-danger">*</span></label>
            <input type="text" name="judul" id="quizJudul" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" id="quizDeskripsi" class="form-control" rows="3"></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Nilai Minimum</label>
            <input type="number" name="nilai_minimum" id="quizNilaiMinimum" class="form-control" min="0" max="100" value="60">
          </div>

          <div class="mb-3">
            <label class="form-label">Maks Attempts</label>
            <input type="number" name="max_attempts" id="quizMaxAttempts" class="form-control" min="1" value="3">
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

<!-- Quiz Delete Confirmation Modal -->
<div class="modal fade" id="quizDeleteModal" tabindex="-1" aria-labelledby="quizDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="quizDeleteModalLabel">Delete Quiz</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="quizDeleteForm" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          <p>Are you sure you want to delete this quiz?</p>
          <p class="text-danger"><strong>This will also delete:</strong></p>
          <ul class="text-danger">
            <li>All questions</li>
            <li>All answer options</li>
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

