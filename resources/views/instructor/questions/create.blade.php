@extends('layouts.instructor')

@section('title','Tambah Pertanyaan')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.quizzes.show', $quiz) }}">{{ $quiz->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Question Create</li>
  </ol>
  {{-- Binding: $quiz --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <form action="{{ route('instructor.questions.store', $quiz->id) }}" method="post">
        @csrf
        <div class="form-group">
          <label>Pertanyaan</label>
          <textarea name="pertanyaan" class="form-control" rows="3" required>{{ old('pertanyaan') }}</textarea>
          @error('pertanyaan')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Tipe</label>
          <select name="tipe" class="form-control" required id="tipeSelect">
            @foreach(['multiple_choice','true_false','essay'] as $t)
              <option value="{{ $t }}" {{ old('tipe')==$t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ', $t)) }}</option>
            @endforeach
          </select>
          @error('tipe')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Bobot</label>
          <input type="number" name="bobot" value="{{ old('bobot', 1) }}" class="form-control" min="1">
          @error('bobot')<small class="text-danger">{{ $message }}</small>@enderror
        </div>

        <hr>
        @php
          $selectedType = old('tipe', 'multiple_choice');
          $showOptions = in_array($selectedType, ['multiple_choice', 'true_false']);
        @endphp
        <div id="answerOptionsSection" style="display: {{ $showOptions ? 'block' : 'none' }};">
          <h6>Opsi Jawaban (untuk Multiple Choice / True False)</h6>
          <div id="optionsContainer">
            {{-- Dynamic repeatable group: name="answer_options[index][teks_jawaban]" & answer_options[index][is_correct] --}}
            @php $oldOptions = old('answer_options', []); @endphp
            @if(!empty($oldOptions))
              @foreach($oldOptions as $idx => $opt)
                <div class="form-row align-items-center mb-2 option-row">
                  <div class="col">
                    <input type="text" class="form-control" name="answer_options[{{ $idx }}][teks_jawaban]" value="{{ $opt['teks_jawaban'] ?? '' }}" placeholder="Teks jawaban">
                  </div>
                  <div class="col-auto">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="answer_options[{{ $idx }}][is_correct]" value="1" {{ !empty($opt['is_correct']) ? 'checked' : '' }}>
                      <label class="form-check-label">Benar?</label>
                    </div>
                  </div>
                  <div class="col-auto">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-option">Hapus</button>
                  </div>
                </div>
              @endforeach
            @endif
          </div>
          <button type="button" id="addOptionBtn" class="btn btn-sm btn-outline-primary">Tambah Opsi</button>
        </div>

        <div class="d-flex justify-content-between mt-3">
          <a href="{{ route('instructor.questions.index', $quiz) }}" class="btn btn-light">Batal</a>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('optionsContainer');
    var addBtn = document.getElementById('addOptionBtn');
    var answerOptionsSection = document.getElementById('answerOptionsSection');
    var tipeSelect = document.getElementById('tipeSelect');
    
    function getCurrentIndex() {
      return container ? container.querySelectorAll('.option-row').length : 0;
    }
    
    function toggleAnswerOptions() {
      if (!tipeSelect || !answerOptionsSection) return;
      
      var selectedType = tipeSelect.value;
      if (selectedType === 'multiple_choice' || selectedType === 'true_false') {
        answerOptionsSection.style.display = 'block';
        // Initialize with 3 options if empty (and no old data exists)
        var existingRows = getCurrentIndex();
        if (container && existingRows === 0) {
          for (var i = 0; i < 3; i++) {
            addRow();
          }
        }
      } else {
        answerOptionsSection.style.display = 'none';
      }
    }
    
    function addRow() {
      if (!container) {
        console.error('Container not found');
        return;
      }
      
      var index = getCurrentIndex();
      var row = document.createElement('div');
      row.className = 'form-row align-items-center mb-2 option-row';
      row.innerHTML = '<div class="col">\
        <input type="text" class="form-control" name="answer_options['+index+'][teks_jawaban]" placeholder="Teks jawaban" required>\
      </div>\
      <div class="col-auto">\
        <div class="form-check">\
          <input class="form-check-input" type="checkbox" name="answer_options['+index+'][is_correct]" value="1">\
          <label class="form-check-label">Benar?</label>\
        </div>\
      </div>\
      <div class="col-auto">\
        <button type="button" class="btn btn-sm btn-outline-danger remove-option">Hapus</button>\
      </div>';
      container.appendChild(row);
    }
    
    function onClick(e){
      if(e.target && e.target.classList.contains('remove-option')){
        e.preventDefault();
        e.stopPropagation();
        var row = e.target.closest('.option-row');
        if (row) {
          row.remove();
        }
      }
    }
    
    // Initialize on page load
    if (tipeSelect) {
      toggleAnswerOptions();
      tipeSelect.addEventListener('change', toggleAnswerOptions);
    }
    
    // Add event listeners
    if (addBtn) {
      addBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Add button clicked');
        addRow();
      });
    } else {
      console.error('Add button not found');
    }
    
    if (container) {
      container.addEventListener('click', onClick);
    } else {
      console.error('Container not found');
    }
  });
</script>
@endpush


