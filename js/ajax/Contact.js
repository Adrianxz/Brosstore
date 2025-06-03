$(document).on('click', '#EnviarC', function(e) {
    e.preventDefault();
    var correo = $("input[name='email']").val();
    var msg = $('#msg').val();

    $.ajax({
        type: 'POST',
        url: 'Backend/Contact.php',
        data: { correo: correo, msg: msg },
        success: function(response) {
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
                title: 'Correo enviado con éxito'
            }).then(() => {
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            });
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: xhr.responseJSON?.error || 'No se pudo enviar el correo'
            });
        }
    });
});
