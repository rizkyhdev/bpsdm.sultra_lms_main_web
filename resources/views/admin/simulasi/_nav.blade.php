{{-- Sub-navigasi modul Simulasi Uji Kompetensi --}}
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a @class(['nav-link', 'active' => request()->routeIs('admin.simulasi.index')])
           href="{{ route('admin.simulasi.index') }}"
           @if(request()->routeIs('admin.simulasi.index')) aria-current="page" @endif>
            <i class="fas fa-desktop me-1"></i> Monitoring Simulasi
        </a>
    </li>
    <li class="nav-item">
        <a @class(['nav-link', 'active' => request()->routeIs('admin.simulasi.soal.*')])
           href="{{ route('admin.simulasi.soal.index') }}"
           @if(request()->routeIs('admin.simulasi.soal.*')) aria-current="page" @endif>
            <i class="fas fa-database me-1"></i> Bank Soal
        </a>
    </li>
</ul>
