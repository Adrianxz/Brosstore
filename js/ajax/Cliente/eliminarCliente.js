$(document).on('click', '.btnEliminarCliente', function () {
  const id = $(this).data('id');
  const nombre = $(this).data('nombre');

  Swal.fire({
    title: `¿Estás seguro de eliminar al cliente "${nombre}"?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: '../Backend/Cliente/eliminar_cliente.php?op=eliminar',
        type: 'POST',
        data: { CLIENTE_ID: id },
        success: function (response) {
          if (response.trim() === 'ok') {
            Swal.fire({
              icon: 'success',
              title: 'Cliente eliminado',
              showConfirmButton: false,
              timer: 1500
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error al eliminar cliente',
              text: response
            });
          }
        },
        error: function (xhr, status, error) {
          Swal.fire({
            icon: 'error',
            title: 'Error de red',
            text: error
          });
        }
      });
    }
  });
});
