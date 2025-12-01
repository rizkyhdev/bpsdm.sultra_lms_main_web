<!-- Content Add/Edit Modal -->
<div class="modal fade" id="contentModal" tabindex="-1" aria-labelledby="contentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="contentModalLabel">Add Content</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="contentForm" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_method" id="contentMethod" value="POST">
        <div class="modal-body">
          <div id="contentErrors" class="alert alert-danger" style="display: none;"></div>
          
          <div class="mb-3">
            <label class="form-label">Judul Content <span class="text-danger">*</span></label>
            <input type="text" name="judul" id="contentJudul" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Tipe Content <span class="text-danger">*</span></label>
            <select name="tipe" id="contentTipe" class="form-control" required onchange="toggleContentFields()">
              <option value="">Pilih Tipe</option>
              <option value="text">Text (Plain Text)</option>
              <option value="html">HTML (Rich Text)</option>
              <option value="video">Video</option>
              <option value="youtube">YouTube Video</option>
              <option value="audio">Audio</option>
              <option value="pdf">PDF File</option>
              <option value="image">Image</option>
              <option value="link">External Link (PDF/File)</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Urutan <span class="text-danger">*</span></label>
            <input type="number" name="urutan" id="contentUrutan" class="form-control" min="1" value="1" required>
            <small class="text-muted">Order of display in the sub-module</small>
          </div>

          <!-- HTML Content Field -->
          <div class="mb-3" id="htmlContentField" style="display: none;">
            <label class="form-label">Konten HTML/Text</label>
            <textarea name="html_content" id="contentHtmlContent" class="form-control" rows="6"></textarea>
          </div>

          <!-- File Upload Field -->
          <div class="mb-3" id="fileContentField" style="display: none;">
            <label class="form-label">File</label>
            <input type="file" name="file_path" id="contentFilePath" class="form-control">
            <small class="text-muted">Upload file untuk content (video, audio, pdf, image)</small>
            <div id="currentFile" class="mt-2" style="display: none;">
              <small class="text-muted">Current file: <span id="currentFileName"></span></small>
            </div>
          </div>

          <!-- YouTube URL Field -->
          <div class="mb-3" id="youtubeContentField" style="display: none;">
            <label class="form-label">YouTube URL</label>
            <input type="url" name="youtube_url" id="contentYoutubeUrl" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
          </div>

          <!-- External URL Field -->
          <div class="mb-3" id="externalUrlField" style="display: none;">
            <label class="form-label">External URL</label>
            <input type="url" name="external_url" id="contentExternalUrl" class="form-control" placeholder="https://...">
          </div>

          <!-- Required Duration Field -->
          <div class="mb-3" id="durationField" style="display: none;">
            <label class="form-label">Required Duration (seconds)</label>
            <input type="number" name="required_duration" id="contentRequiredDuration" class="form-control" min="1" placeholder="Minimum time to view content">
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

<!-- Content Delete Confirmation Modal -->
<div class="modal fade" id="contentDeleteModal" tabindex="-1" aria-labelledby="contentDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="contentDeleteModalLabel">Delete Content</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="contentDeleteForm" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          <p>Are you sure you want to delete this content?</p>
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

<script>
function toggleContentFields() {
  const tipe = document.getElementById('contentTipe').value;
  
  // Hide all fields
  document.getElementById('htmlContentField').style.display = 'none';
  document.getElementById('fileContentField').style.display = 'none';
  document.getElementById('youtubeContentField').style.display = 'none';
  document.getElementById('externalUrlField').style.display = 'none';
  document.getElementById('durationField').style.display = 'none';
  
  // Show relevant fields based on type
  if (tipe === 'text' || tipe === 'html') {
    document.getElementById('htmlContentField').style.display = 'block';
    document.getElementById('durationField').style.display = 'block';
  } else if (tipe === 'youtube') {
    document.getElementById('youtubeContentField').style.display = 'block';
    document.getElementById('durationField').style.display = 'block';
  } else if (tipe === 'link') {
    document.getElementById('externalUrlField').style.display = 'block';
    document.getElementById('durationField').style.display = 'block';
  } else if (['video', 'audio', 'pdf', 'image'].includes(tipe)) {
    document.getElementById('fileContentField').style.display = 'block';
    document.getElementById('durationField').style.display = 'block';
  }
}
</script>

