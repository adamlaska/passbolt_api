// Refresh the page when clicking on the button
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('js_setup_plugin_check').addEventListener('click', (e) => {
    e.preventDefault();
    window.location.reload();
  });
});
