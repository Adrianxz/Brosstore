$(document).ready(function () {
  // Al hacer click en el botón editar, obtener datos y mostrar modal
  $(document).on('click', '.accion-editar', function () {
    const idRol = $(this).data('id');

    $.ajax({
      url: '../Backend/Rol/obtener.php',
      type: 'POST',
      data: { id: idRol },
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success') {
          $('#descripcionRol').val(response.data.ROLD_DESCRIP);
          $('#rolIdEditar').val(response.data.ROL_ID);
          $('#modalEditarRol').modal('show');
        } else {
          alert('Error al obtener datos del rol');
        }
      },
      error: function () {
        alert('Error de red al obtener datos del rol');
      }
    });
  });

  // Actualizar rol con el modal abierto
  $(document).ready(function () {
  $(document).on('click', '#btnActualizarRol', function (e) {
    e.preventDefault();

    let id = $('#rolIdEditar').val();
    let descripcion = $('#descripcionRol').val().trim();

    if (descripcion === '') {
      Swal.fire({
        icon: 'warning',
        title: 'Campo vacío',
        text: 'El campo descripción es obligatorio.',
        confirmButtonText: 'Entendido'
      });
      return;
    }

    $.ajax({
      url: '../Backend/Rol/actualizar.php',
      type: 'POST',
      data: { id: id, descripcion: descripcion },
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: '¡Actualizado!',
            text: 'El rol se ha actualizado correctamente.',
            confirmButtonText: 'Aceptar'
          }).then(() => {
            location.reload();
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: response.message,
            confirmButtonText: 'Cerrar'
          });
        }
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: 'error',
          title: 'Error de red',
          text: error,
          confirmButtonText: 'Cerrar'
        });
      }
    });
  });
});

});
