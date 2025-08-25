const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('menu-toggle');

toggleBtn.addEventListener('click', () => {
  sidebar.classList.toggle('collapsed');
});
