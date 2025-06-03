document.addEventListener("DOMContentLoaded", function() {
    // Cargar la API de Google Identity Services
    const script = document.createElement('script');
    script.src = 'https://accounts.google.com/gsi/client';
    script.async = true;
    script.defer = true;
    script.onload = initGoogleAuth;
    document.head.appendChild(script);
});

function initGoogleAuth() {
    const googleButton = document.getElementById("Gmail");
    
    if (googleButton) {
        googleButton.addEventListener("click", function(e) {
            e.preventDefault();
            showGoogleAccountModal();
        });
    }
}

function showGoogleAccountModal() {
    // Limpiar sesiones previas
    localStorage.removeItem("google_id_token");
    
    if (typeof google === 'undefined' || !google.accounts || !google.accounts.id) {
        showError("El servicio de Google no está disponible. Recarga la página.");
        return;
    }

    // Configurar el cliente de Google
    google.accounts.id.initialize({
        client_id: "626919515251-8shlre3ltg2hp18djivlgj2h47jrj9ra.apps.googleusercontent.com",
        callback: handleGoogleResponse,
        ux_mode: "popup",
        auto_select: false
    });

    // Mostrar el modal de selección de cuentas
    google.accounts.id.prompt(notification => {
        if (notification.isNotDisplayed()) {
            const reason = notification.getNotDisplayedReason();
            console.error("Modal no mostrado. Razón:", reason);
            
            if (reason === "browser_not_supported" || reason === "invalid_client") {
                showError("Tu navegador no es compatible. Prueba con Chrome o Firefox.");
            } else {
                showError("No se pudo mostrar el selector de cuentas. Intenta nuevamente.");
            }
        }
    });
}

function handleGoogleResponse(response) {
    if (!response.credential) {
        showError("No se recibió el token de Google");
        return;
    }

    showLoading("Verificando tu cuenta...");
    
    // Guardar el token temporalmente
    localStorage.setItem("google_id_token", response.credential);
    
    // Verificar con el backend
    verifyWithBackend(response.credential);
}

async function verifyWithBackend(idToken) {
    try {
        const response = await fetch("Backend/Usuario/AgregarUsuariosGmail.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id_token: idToken })
        });

        const data = await response.json();
        
        if (!response.ok || !data.success) {
            throw new Error(data.error || "Error en la autenticación");
        }

        showSuccess(data.message || "¡Bienvenido!");
        setTimeout(() => window.location.href = "index", 1500);
        
    } catch (error) {
        console.error("Error:", error);
        showError(error.message);
        logoutGoogle();
    }
}

// Funciones de utilidad (se mantienen igual que antes)
function showLoading(message) {
    Swal.fire({
        title: message,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

function showSuccess(message) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 1500, 
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    Toast.fire({
        icon: 'success',
        title: message
    }).then(() => {
        setTimeout(() => {
            window.location.href = "index"; // Redirigir después del mensaje
        }, 500);
    });
}

function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message,
        confirmButtonColor: '#d33'
    });
}

/*
function logoutGoogle() {
    localStorage.removeItem("google_id_token");
    Swal.fire({
        icon: 'info',
        title: 'Sesión cerrada',
        text: 'Has cerrado sesión correctamente.',
        confirmButtonColor: '#3085d6'
    });
}
*/