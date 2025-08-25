function openTab(tabId, button) {
  // Sembunyikan semua konten tab
  document.querySelectorAll('.tab-content1').forEach(tab => {
    tab.style.display = 'none';
  });

  // Tampilkan tab yang dipilih
  document.getElementById(tabId).style.display = 'block';

  // Hapus class active dari semua tombol
  document.querySelectorAll('.tab-buttons button').forEach(btn => {
    btn.classList.remove('active');
  });

  // Tambahkan class active ke tombol yang diklik
  button.classList.add('active');
}