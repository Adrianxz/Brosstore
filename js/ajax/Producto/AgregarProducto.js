let imagenesSeleccionadas = []; // Array para almacenar los archivos seleccionados

// Función para limpiar nombre de archivo
function limpiarNombreArchivo(nombreOriginal) {
    return nombreOriginal
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[^a-zA-Z0-9.\-_]/g, '-')
        .replace(/\s+/g, '-')
        .toLowerCase();
}

document.getElementById('FotoInput').addEventListener('change', function (event) {
    const nuevosArchivos = Array.from(event.target.files);
    const preview = document.getElementById('preview');

    if (imagenesSeleccionadas.length + nuevosArchivos.length > 3) {
        alert("Solo puedes seleccionar un máximo de 3 imágenes.");
        event.target.value = '';
        return;
    }

    nuevosArchivos.forEach((archivo) => {
        if (!archivo.type.match('image.*')) return;
        if (imagenesSeleccionadas.some(img => img.name === archivo.name)) return;

        imagenesSeleccionadas.push(archivo);

        const reader = new FileReader();
        reader.onload = function (e) {
            const contenedor = document.createElement('div');
            contenedor.style.position = 'relative';
            contenedor.style.display = 'inline-block';

            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '100px';
            img.style.border = '1px solid #ccc';
            img.style.borderRadius = '8px';
            img.style.padding = '3px';

            const eliminar = document.createElement('span');
            eliminar.innerHTML = '✖';
            eliminar.style.position = 'absolute';
            eliminar.style.top = '2px';
            eliminar.style.right = '5px';
            eliminar.style.cursor = 'pointer';
            eliminar.style.color = 'red';
            eliminar.onclick = function () {
                contenedor.remove();
                imagenesSeleccionadas = imagenesSeleccionadas.filter(f => f.name !== archivo.name);
            };

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = 'imagenPrincipal';
            checkbox.value = limpiarNombreArchivo(archivo.name);
            checkbox.style.position = 'absolute';
            checkbox.style.bottom = '5px';
            checkbox.style.left = '5px';
            checkbox.onclick = function () {
                document.querySelectorAll('input[name="imagenPrincipal"]').forEach((box) => {
                    if (box !== checkbox) box.checked = false;
                });
            };

            contenedor.appendChild(img);
            contenedor.appendChild(eliminar);
            contenedor.appendChild(checkbox);
            preview.appendChild(contenedor);
        };

        reader.readAsDataURL(archivo);
    });

    event.target.value = '';
});

// Función de validación
function validar() {
    console.log("validando");
    var mensaje = "";

    var nombre = $('input[name="Nombre"]').val();
    var descripcion = $('#summernote').summernote('code');
    var categoria = $('select[name="Categoria"]').val();
    var genero = $('select[name="Genero"]').val();
    var proveedor = $('select[name="Proveedor"]').val();
    
    var precio = $('input[name="Precio"]').val();

    if (nombre.trim() === '') mensaje = 'El campo "Nombre" es obligatorio.';
    else if (descripcion.trim() === '' || descripcion === '<p><br></p>') mensaje = 'El campo "Descripción" es obligatorio.';
    else if (categoria === '' || categoria === null) mensaje = 'Debe seleccionar una categoría.';
    else if (genero === '' || genero === null) mensaje = 'Debe seleccionar un genero.';
    else if (proveedor === '' || proveedor === null) mensaje = 'Debe seleccionar un proveedor.';
    else if (precio.trim() === '') mensaje = 'El campo "Precio" es obligatorio.';
    else if (imagenesSeleccionadas.length === 0) mensaje = 'Debe seleccionar al menos una imagen.';
    else if (!document.querySelector('input[name="imagenPrincipal"]:checked')) mensaje = 'Debe seleccionar una imagen como principal.';

    if (mensaje !== "") {
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 5000,
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

        return false;
    }

    return true;
}

// Enviar el formulario con AJAX
$(document).on("click", "#AgregarProducto", function (event) {
    event.preventDefault();

    if (validar()) {
        let formData = new FormData();

        // Campos del formulario
        formData.append('Nombre', $('input[name="Nombre"]').val());
        formData.append('descripcion', $('#summernote').summernote('code'));
        formData.append('Categoria', $('select[name="Categoria"]').val());
        formData.append('Proveedor', $('select[name="Proveedor"]').val());
        formData.append('Precio', $('input[name="Precio"]').val());
        formData.append('Genero', $('select[name="Genero"]').val());

        // Imágenes
        imagenesSeleccionadas.forEach((archivo) => {
            if (archivo instanceof File && archivo.size > 0) {
                let nombreImagenLimpio = limpiarNombreArchivo(archivo.name);
                formData.append('Foto[]', archivo);
                formData.append('NombreImagen[]', encodeURIComponent(nombreImagenLimpio));
            }
        });

        // Imagen principal
        const imagenPrincipal = document.querySelector('input[name="imagenPrincipal"]:checked');
        if (imagenPrincipal) {
            let nombreImagenPrincipalLimpio = limpiarNombreArchivo(imagenPrincipal.value);
            formData.append('ImagenPrincipal', nombreImagenPrincipalLimpio);
        }

        // Tallas seleccionadas y cantidades
        document.querySelectorAll('input[name="tallas[]"]:checked').forEach((checkbox) => {
            const tallaId = checkbox.value;
            const cantidadInput = document.getElementById('cantidad_' + tallaId);

            if (cantidadInput && cantidadInput.value.trim() !== '') {
                formData.append('tallas[]', tallaId);
                formData.append(`cantidad[${tallaId}]`, cantidadInput.value);
            }
        });

        // Enviar solicitud
        fetch('../Backend/Producto/AgregarProducto.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);

            if (data.status === 'error') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: data.message
                });
            } else if (data.status === 'success') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });

                Toast.fire({
                    icon: 'success',
                    title: 'Registro con éxito'
                }).then(() => {
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                });
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al procesar la solicitud. Intenta nuevamente.'
            });
        });
    }
});
