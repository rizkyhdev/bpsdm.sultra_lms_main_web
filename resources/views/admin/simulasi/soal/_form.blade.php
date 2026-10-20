@csrf

<div class="card shadow-sm border-0" style="border-radius: 12px;">
    <div class="card-body">
        <div class="mb-4">
            <label for="pertanyaan" class="form-label fw-semibold">Pertanyaan <span class="text-danger">*</span></label>
            <textarea id="pertanyaan" name="pertanyaan" rows="5" required maxlength="5000"
                      class="form-control @error('pertanyaan') is-invalid @enderror"
                      placeholder="Tuliskan pertanyaan…">{{ old('pertanyaan', $question->pertanyaan) }}</textarea>
            @error('pertanyaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        @foreach (\App\Models\SimulationQuestion::OPTIONS as $huruf)
            @php($field = 'pilihan_' . strtolower($huruf))
            <div class="mb-3">
                <label for="{{ $field }}" class="form-label fw-semibold">Pilihan {{ $huruf }} <span class="text-danger">*</span></label>
                <textarea id="{{ $field }}" name="{{ $field }}" rows="2" required maxlength="2000"
                          class="form-control @error($field) is-invalid @enderror">{{ old($field, $question->$field) }}</textarea>
                @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        @endforeach

        <fieldset class="mt-4">
            <legend class="form-label fw-semibold fs-6 mb-2">Kunci Jawaban <span class="text-danger">*</span></legend>
            <div class="btn-group" role="group" aria-label="Kunci jawaban">
                @foreach (\App\Models\SimulationQuestion::OPTIONS as $huruf)
                    <input type="radio" class="btn-check" name="kunci_jawaban" id="kunci_{{ $huruf }}" value="{{ $huruf }}"
                           autocomplete="off" required @checked(old('kunci_jawaban', $question->kunci_jawaban) === $huruf)>
                    <label class="btn btn-outline-success px-4" for="kunci_{{ $huruf }}">{{ $huruf }}</label>
                @endforeach
            </div>
            @error('kunci_jawaban')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </fieldset>
    </div>
    <div class="card-footer bg-white d-flex justify-content-end gap-2">
        <a href="{{ route('admin.simulasi.soal.index') }}" class="btn btn-outline-secondary">Batal</a>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1" aria-hidden="true"></i>{{ $submitLabel }}</button>
    </div>
</div>
