

$(document).on("click","#Ingresar",function(e){


event.preventDefault();

var name = $('input[name="Correo"]').val();

var contra = $('input[name="Contra"]').val();

	$.ajax({
		type:'POST',
		url:'Backend/Usuario/LoginUsuario.php',
		data:{name:name, contra:contra},

		success:function(response)
		{
			 window.location.href = 'index';
		},


		error:function(error)
		{
			 const Toast = Swal.mixin({
                  toast: true,
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 3000,
                  timerProgressBar: true,
                  didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                  }
                })

                Toast.fire({

                    icon: 'error',
                    html: error.responseJSON.Message

                })
		}

	});

});