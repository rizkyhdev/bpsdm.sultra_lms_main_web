@extends('layouts.admin')

@section('title', 'Ubah Pertanyaan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.quizzes.show', $question->quiz_id) }}">Kuis</a></li>
    <li class="breadcrumb-item active">Ubah Pertanyaan</li>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.questions.update', $question) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="pertanyaan">Pertanyaan</label>
                        <textarea id="pertanyaan" name="pertanyaan" rows="3" class="form-control @error('pertanyaan') is-invalid @enderror">{{ old('pertanyaan', $question->pertanyaan) }}</textarea>
                        @error('pertanyaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-2">
                        <label for="tipe">Tipe</label>
                        <select id="tipe" name="tipe" class="form-control @error('tipe') is-invalid @enderror">
                            <option value="multiple_choice" @if(old('tipe', $question->tipe)==='multiple_choice') selected @endif>Pilgan</option>
                            <option value="true_false" @if(old('tipe', $question->tipe)==='true_false') selected @endif>Benar/Salah</option>
                            <option value="essay" @if(old('tipe', $question->tipe)==='essay') selected @endif>Esai</option>
                        </select>
                        @error('tipe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-2">
                        <label for="bobot">Bobot</label>
                        <input type="number" id="bobot" name="bobot" value="{{ old('bobot', $question->bobot) }}" class="form-control @error('bobot') is-invalid @enderror">
                        @error('bobot')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Opsi jawaban dinamis --}}
                <div id="optionsSection" class="border rounded p-3 mb-3" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Opsi Jawaban</strong>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="addOptionBtn"><i class="fas fa-plus"></i> Tambah Opsi</button>
                    </div>
                    <div id="optionsContainer">
                        @foreach(($answerOptions ?? []) as $idx => $opt)
                            <div class="form-row align-items-center mb-2">
                                <div class="col-md-7">
                                    <input type="text" name="answer_options[{{ $idx }}][teks_jawaban]" class="form-control" value="{{ $opt->teks_jawaban }}">
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="answer_options[{{ $idx }}][is_correct]" @if($opt->is_correct) checked @endif>
                                        <label class="form-check-label">Benar?</label>
                                    </div>
                                </div>
                                <div class="col-md-2 text-right">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-option"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.questions.index', $question->quiz_id) }}" class="btn btn-outline-secondary">Kembali</a>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
(function(){
  var tipe = document.getElementById('tipe');
  var section = document.getElementById('optionsSection');
  var container = document.getElementById('optionsContainer');
  var addBtn = document.getElementById('addOptionBtn');

  function updateVisibility(){
    var show = (tipe.value === 'multiple_choice' || tipe.value === 'true_false');
    section.style.display = show ? 'block' : 'none';
  }
  tipe.addEventListener('change', updateVisibility);
  updateVisibility();

  function addNewOption(){
    var idx = container.children.length;
    var row = document.createElement('div');
    row.className = 'form-row align-items-center mb-2';
    row.innerHTML = '\
      <div class="col-md-7">\
        <input type="text" name="answer_options['+idx+'][teks_jawaban]" class="form-control" placeholder="Teks jawaban">\
      </div>\
      <div class="col-md-3">\
        <div class="form-check">\
          <input class="form-check-input" type="checkbox" name="answer_options['+idx+'][is_correct]">\
          <label class="form-check-label">Benar?</label>\
        </div>\
      </div>\
      <div class="col-md-2 text-right">\
        <button type="button" class="btn btn-sm btn-outline-danger remove-option"><i class="fas fa-trash"></i></button>\
      </div>';
    container.appendChild(row);
  }

  addBtn && addBtn.addEventListener('click', addNewOption);
  container && container.addEventListener('click', function(e){
    if(e.target.closest('.remove-option')){ e.target.closest('.form-row').remove(); }
  });
})();
</script>
@endsection


