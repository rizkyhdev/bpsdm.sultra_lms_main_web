@extends('layouts.instructor')

@section('title','Edit Pertanyaan')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.quizzes.show', $question->quiz) }}">{{ $question->quiz->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Question Edit</li>
  </ol>
  {{-- Binding: $question --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <form action="{{ route('instructor.questions.update', $question->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label>Pertanyaan</label>
          <textarea name="pertanyaan" class="form-control" rows="3" required>{{ old('pertanyaan', $question->pertanyaan) }}</textarea>
          @error('pertanyaan')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Tipe</label>
          <select name="tipe" class="form-control" required id="tipeSelect">
            @foreach(['multiple_choice','true_false','essay'] as $t)
              <option value="{{ $t }}" {{ old('tipe', $question->tipe)==$t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ', $t)) }}</option>
            @endforeach
          </select>
          @error('tipe')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Bobot</label>
          <input type="number" name="bobot" value="{{ old('bobot', $question->bobot) }}" class="form-control" min="1">
          @error('bobot')<small class="text-danger">{{ $message }}</small>@enderror
        </div>

        <hr>
        <h6>Opsi Jawaban</h6>
        <div id="optionsContainer">
          @foreach($question->answerOptions as $idx => $opt)
            <div class="form-row align-items-center mb-2 option-row">
              <div class="col">
                <input type="text" class="form-control" name="answer_options[{{ $idx }}][teks_jawaban]" value="{{ old('answer_options.'.$idx.'.teks_jawaban', $opt->teks_jawaban) }}" placeholder="Teks jawaban">
              </div>
              <div class="col-auto">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="answer_options[{{ $idx }}][is_correct]" value="1" {{ old('answer_options.'.$idx.'.is_correct', $opt->is_correct) ? 'checked' : '' }}>
                  <label class="form-check-label">Benar?</label>
                </div>
              </div>
              <div class="col-auto">
                <button type="button" class="btn btn-sm btn-outline-danger remove-option">Hapus</button>
              </div>
            </div>
          @endforeach
        </div>
        <button type="button" id="addOptionBtn" class="btn btn-sm btn-outline-primary">Tambah Opsi</button>

        <div class="d-flex justify-content-between mt-3">
          <a href="{{ route('instructor.questions.index', $question->quiz->id) }}" class="btn btn-light">Batal</a>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  (function() {
    var container = document.getElementById('optionsContainer');
    var addBtn = document.getElementById('addOptionBtn');
    var index = container.querySelectorAll('.option-row').length;
    function addRow() {
      var row = document.createElement('div');
      row.className = 'form-row align-items-center mb-2 option-row';
      row.innerHTML = '<div class="col">\
        <input type="text" class="form-control" name="answer_options['+index+'][teks_jawaban]" placeholder="Teks jawaban">\
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
      index++;
    }
    function onClick(e){
      if(e.target && e.target.classList.contains('remove-option')){
        e.target.closest('.option-row').remove();
      }
    }
    addBtn && addBtn.addEventListener('click', addRow);
    container && container.addEventListener('click', onClick);
  })();
  </script>
@endpush


