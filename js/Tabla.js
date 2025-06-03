// js/Tabla.js
document.addEventListener("DOMContentLoaded", function () {
  $('#miTabla').DataTable({
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50, 100],
    language: {
      search: "Buscar:",
      lengthMenu: "Mostrar _MENU_ registros por página",
      zeroRecords: "No se encontraron registros",
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
      infoEmpty: "Mostrando 0 a 0 de 0 registros",
      infoFiltered: "(filtrado de _MAX_ registros totales)",
      paginate: {
        first: "Primero",
        last: "Último",
        next: "Siguiente",
        previous: "Anterior"
      }
    }
  });
});
