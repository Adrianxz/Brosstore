$(document).ready(function () {
  $(document).on('click', '.btnEliminarRol', function (e) {
    e.preventDefault();

    let id = $(this).data('id');
    let nombre = $(this).data('nombre');

    Swal.fire({
      title: `¿Seguro que quieres eliminar el rol "${nombre}"?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'No, cancelar',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        // Enviar AJAX para eliminar
        $.ajax({
          url: '../Backend/Rol/eliminar.php',
          type: 'POST',
          data: { id: id },
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              Swal.fire('Eliminado', 'Rol eliminado correctamente.', 'success').then(() => {
                location.reload();
              });
            } else {
              Swal.fire('Error', '❌ ' + response.message, 'error');
            }
          },
          error: function (xhr, status, error) {
            Swal.fire('Error', '❌ Error de red: ' + error, 'error');
          }
        });
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        Swal.fire('Cancelado', 'La acción fue cancelada.', 'info');
      }
    });
  });
});
