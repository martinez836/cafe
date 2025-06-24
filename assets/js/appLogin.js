document.addEventListener('DOMContentLoaded', function() {
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      const correo = document.getElementById('correo').value;
      const contrasena = document.getElementById('contrasena').value;
      try {
        const response = await fetch('../controllers/login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ correo, contrasena })
        });
        const data = await response.json();
        if (data.success) {
          window.location.href = 'mesero.php';
        } else {
          Swal.fire('Error', data.message || 'Credenciales incorrectas', 'error');
        }
      } catch (err) {
        Swal.fire('Error', 'Error de conexión', 'error');
      }
    });
  }
}); 