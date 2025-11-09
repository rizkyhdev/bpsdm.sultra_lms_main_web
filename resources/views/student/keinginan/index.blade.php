@extends('student.layout')

@section('title', 'Daftar Keinginan')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-slate-900">Daftar Keinginan</h1>
    </div>

    @if(isset($wishlist) && $wishlist->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($wishlist as $course)
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ $course->judul }}</h3>
                    <p class="text-sm text-slate-600 mb-4 line-clamp-2">{{ Str::limit($course->deskripsi, 100) }}</p>
                    
                    @if($course->bidang_kompetensi)
                        <span class="pill mb-4 inline-block">{{ $course->bidang_kompetensi }}</span>
                    @endif

                    <div class="flex flex-col gap-2 mt-4">
                        <form action="{{ route('student.enroll', $course) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="btn-primary w-full">
                                Daftar
                            </button>
                        </form>
                        
                        <form action="{{ route('student.wishlist.destroy', $course) }}" method="POST" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 border border-slate-300 rounded-md text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($wishlist->hasPages())
            <div class="mt-6">
                {{ $wishlist->links() }}
            </div>
        @endif
    @else
        <x-empty-state 
            title="Belum ada pelatihan dalam daftar keinginan" 
            subtitle="Tambahkan pelatihan ke daftar keinginan untuk menyimpannya nanti." 
            :action="['label' => 'Jelajahi Pelatihan', 'url' => route('courses.index')]"
        />
    @endif
</div>
@endsection

