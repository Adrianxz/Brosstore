$(document).ready(function () {
  // Abrir modal y cargar datos del cliente
  $(document).on('click', '.btnEditarCliente', function () {
    const id = $(this).data('id');

    $.ajax({
      url: '../Backend/Cliente/actualizar_cliente.php?op=obtener',
      type: 'POST',
      data: { CLIENTE_ID: id },
      dataType: 'json',
      success: function (data) {
        if (data) {
          $('#CLIENTE_ID').val(data.CLIENTE_ID);
          $('#CLIENTE_NOMBRE').val(data.CLIENTE_NOMBRE);
          $('#CLIENTE_APELLIDO').val(data.CLIENTE_APELLIDO);
          $('#CLIENTE_NUMIDENT').val(data.CLIENTE_NUMIDENT);
          $('#CLIENTE_CORREO').val(data.CLIENTE_CORREO);
          $('#CLIENTE_TEL').val(data.CLIENTE_TEL);
          $('#CLIENTE_DIRECCION').val(data.CLIENTE_DIRECCION);
          $('#PAIS').val(data.PAIS);
          $('#CIUDAD').val(data.CIUDAD);
          $('#CLIENTE_CONTRA').val(data.CLIENTE_CONTRA);
          $('#modalEditarCliente').modal('show');
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo obtener la información del cliente.'
          });
        }
      },
      error: function () {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error al comunicarse con el servidor.'
        });
      }
    });
  });

  // Actualizar cliente
  $('#btnActualizarCliente').click(function () {
    const formData = $('#formEditarCliente').serialize();

    $.ajax({
      url: '../Backend/Cliente/actualizar_cliente.php?op=actualizar',
      type: 'POST',
      data: formData,
      success: function (response) {
        if (response.trim() === 'ok') {
          Swal.fire({
            icon: 'success',
            title: 'Cliente actualizado correctamente',
            showConfirmButton: false,
            timer: 1500
          }).then(() => {
            $('#modalEditarCliente').modal('hide');
            location.reload();
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error al actualizar cliente',
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
  });
});
