<header class="header">
    <div class="header-kiri">
        <div class="logo-container">
            <img src="{{ asset('images/aura-logo.png') }}" alt="Logo Aura" class="logo">
        </div> 
    </div>
    <div class="header-kanan">
        <nav class="navigation">
            <ul class="nav-list">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ url('/course') }}">Course</a></li>
                <li><a href="{{ url('/article') }}">Article</a></li>
                <li><a href="{{ url('/contact') }}">Contact</a></li>
            </ul>
        </nav>
        <div class="search">
            <input type="text" placeholder="Search..." class="search-input">
        </div>
        <div class="icons">
            <i class="fa-solid fa-bell icon-bell"></i>
            <i class="fa-solid fa-user icon-user"></i>
        </div>
    </div>
</header>
