$(document).ready(function () {
  $(document).on('click', '#guardarCliente', function () {
    const nombre = $('#CLIENTE_NOMBRE').val().trim();
    const apellido = $('#CLIENTE_APELLIDO').val().trim();
    const numIdent = $('#CLIENTE_NUMIDENT').val().trim();
    const correo = $('#CLIENTE_CORREO').val().trim();
    const contra = $('#CLIENTE_CONTRA').val().trim();

    if (nombre === '' || apellido === '' || numIdent === '' || correo === '' || contra === '') {
      Swal.fire({
        icon: 'warning',
        title: 'Campos obligatorios',
        text: 'Completa todos los campos requeridos.'
      });
      return;
    }

    const formData = new FormData($('#cliente-form')[0]);

    $.ajax({
      url: '../Backend/Cliente/insertar_cliente.php?op=insertar',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        if (response.trim() === 'ok') {
          Swal.fire({
            icon: 'success',
            title: 'Cliente registrado',
            timer: 1500,
            showConfirmButton: false
          }).then(() => {
            $('#cliente-form')[0].reset();
            location.reload();
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error al registrar',
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
