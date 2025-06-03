$(document).ready(function () {
  $(document).on('click', '#guardarRol', function () {
    const descripcion = $('#ROLD_DESCRIP').val().trim();
    let mensaje = "";

    if (descripcion === '') {
      mensaje = "El campo 'Nombre del rol' es obligatorio.";
    }

    if (mensaje !== "") {
      const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.onmouseenter = Swal.stopTimer;
          toast.onmouseleave = Swal.resumeTimer;
        }
      });
      Toast.fire({
        icon: "warning",
        title: mensaje
      });
      return;
    }

    // Validación AJAX para verificar si ya existe el rol
    $.ajax({
      url: '../Backend/Rol/insertar_rol.php?op=existe',
      type: 'POST',
      data: { ROLD_DESCRIP: descripcion },
      success: function (respuesta) {
        if (respuesta.trim() === 'existe') {
          Swal.fire({
            icon: 'warning',
            title: 'Ya existe un rol con ese nombre',
            text: 'Intenta con otro nombre.'
          });
        } else {
          // Si no existe, proceder a insertar
          var formData = new FormData($('#rol-form')[0]);

          $.ajax({
            url: '../Backend/Rol/insertar_rol.php?op=insertar',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
              if (response.trim() === 'ok') {
                Swal.fire({
                  icon: 'success',
                  title: '✅ Rol registrado correctamente',
                  showConfirmButton: false,
                  timer: 1500
                }).then(() => {
                  $('#rol-form')[0].reset();
                  location.reload();
                });
              } else {
                Swal.fire({
                  icon: 'error',
                  title: '❌ Error al registrar el rol',
                  text: response
                });
              }
            },
            error: function (xhr, status, error) {
              Swal.fire({
                icon: 'error',
                title: '❌ Error de red',
                text: error
              });
            }
          });
        }
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: 'error',
          title: '❌ Error de validación',
          text: error
        });
      }
    });
  });
});
