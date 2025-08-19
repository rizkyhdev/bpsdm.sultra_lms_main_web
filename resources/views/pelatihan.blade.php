@extends('layout.app')
@section('content')


    <main class="main-content">
        <section class="hero">
            <h1>Pelatihan Sertifikasi PBJ</h1>
        </section>
        <section class="content">
        <div class="left-column">  
        <div class="left-card">
            <div class="simpan-image">
            <i class="fa-solid fa-bookmark simpan"></i>
            </div>
                <h2>Pelatihan PBJ Level - 1</h2>
                <div class="meta">
                    <span>2 hours</span>
                    <span>Beginner</span>
                    <span>Enrolled</span>
                </div>
                <div class="rating">⭐⭐⭐⭐⭐ (1)</div>
                <div class="user-info">
                    <i class="fa-solid fa-user icon-user1"></i>
                    <span class="username">Username</span>
                </div>
                <hr class="line">
                <div class="tabs">
                    <div class="tab-buttons">
                        <button onclick="openTab('tab1', this)" class="active">Desscription</button>
                        <button onclick="openTab('tab2', this)">Announcement</button>
                        <button onclick="openTab('tab3', this)">Review</button> 
                    </div>
                </div>
        </div>
        <div class="tab-content">
                    <div class="tab-content1" id="tab1">Desscription</div>
                     <div class="tab-content1" id="tab2" style="display:none;">Announcement</div>
                     <div class="tab-content1" id="tab3" style="display:none;">Nice</div>
            </div>
        </div>
        <div class="right-column">
            <div class="right-card">
                <button class="start-button">Start Learning</button>
                <div class="warning-box">
                    ⚠️ Complete all lessons to mark this course as complete
                </div>
            </div>
            <div class="enrolled-bar">
                <p class="enrolled-info">
                    You enrolled this course on <strong>July 2, 2025</strong>
                </p>
            </div>
            <div class="dropdown">
                <div class="level-dropdown">
                    <details><summary>Pelatihan PBJ Level-1</summary></details>
                    <details><summary>Pelatihan PBJ Level-2</summary></details>
                    <details><summary>Pelatihan PBJ Level-3</summary></details>
                </div>
            </div>
        </div>
    </div>
        </section>
    </main>
@endsection

    
