// clienteNuevo.js
$(document).ready(function () {
  $("#cliente-form").on("submit", function (e) {
    e.preventDefault();

    let datos = {
      nombre: $("#nombre").val(),
      apellido: $("#apellido").val(),
      identidad: $("#identidad").val(),
      correo: $("#correo").val(),
      telefono: $("#telefono").val(),
      direccion: $("#direccion").val(),
      pais: $("#pais").val(),
      ciudad: $("#ciudad").val()
    };

    $.ajax({
      url: "../backend/Ncliente.php",
      type: "POST",
      data: datos,
      success: function (response) {
        alert(response);
        $("#cliente-form")[0].reset();
        location.reload(); // Actualiza la tabla si lo deseas
      },
      error: function () {
        alert("Error al registrar el cliente.");
      }
    });
  });
});
