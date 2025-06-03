
function imprimirValores() {
    var form = document.getElementById('Register');
    var inputs = form.getElementsByTagName('input');

    for (var i = 0; i < inputs.length; i++) {
        console.log(inputs[i].name + ': ' + inputs[i].value);
    }

    var L = document.getElementById('X');

    console.log(L.value);
}

function validar() 
{
  
    console.log("validando");
    //var usuario = sessionStorage.getItem('usuario');
    //if (!usuario) {
    //    return;
    //}

    var form = document.getElementById('Register');

    var X = form.getElementsByTagName('input');

    var formData = new FormData();

    var mensaje = "";

    for (var i = 0; i < X.length; i++) 
    {
        
        var XS = X[i];

        if (XS.value.trim() === '') {
            mensaje = 'Todos los campos son obligatorios';
        }


        if (XS.name === 'Email') {
            if (!Vcorreo(XS.value)) {
                mensaje += 'El correo electrónico ingresado no es válido. <br>';
            }
        }

        if (XS.name === 'Telefono') {
            if (!VTel(XS.value)) {
                mensaje += "Numero no valido";
            }
        }

        if (XS.name === 'Contra') {
            if (XS.value.length < 6) {
                mensaje += "La contraseña debe tener minimo 6 caracteres. <br>";
            }
        }
        
    }

    if (mensaje !== "") 
    {
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
          title: "Todos los campos deben ser obligatorios"
      });

        return false;
    } 

    else 
    {
        return true;
    }

}



$(document).on("click", "#Registrar", function (event) {
    event.preventDefault(); // Evita que se refresque la página al enviar el formulario

    if (validar()) {

        let L = document.querySelector('#Register');

        event.preventDefault();

        let formData = new FormData(L);

        fetch('Backend/Usuario/AgregarUsuarios.php', {
            method: 'POST',
            body: formData
        }).then(
            response => response.json()
        ).then(data => {
            console.log(data);

            if(data.errorMessage)
            {
                Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: data.errorMessage
                    });
            }
            
            else
            {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1000, 
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            Toast.fire({
                icon: 'success',
                title: 'Registro con exito'
            }).then(() => {

                setTimeout(() => {
                    window.location.href = "index";
                }, 500);
            })

        }

        })


        .catch(error => {
            console.error(error);
        });
    }

        
});



function Vcorreo(correo) {
    var expReg = /^[a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*@[a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,5}$/;

    return expReg.test(correo);
}

function VTel(telefono) {
    var expReg = /^[0-9]{0,10}$/;
    return expReg.test(telefono);
}