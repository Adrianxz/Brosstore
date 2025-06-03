// Sistema de modales independiente sin jQuery ni Bootstrap.modal()
// Variables globales
let selectedProduct = null
let selectedSize = null
let selectedSizeData = null
const cartItems = []

// ===== SISTEMA DE MODALES INDEPENDIENTE =====
const ModalSystem = {
  // Abrir modal
  open: function (modalId) {
    console.log(`Abriendo modal: ${modalId}`)
    const modal = document.getElementById(modalId)
    if (!modal) {
      console.error(`Modal no encontrado: ${modalId}`)
      return
    }

    // Mostrar modal
    modal.style.display = "block"
    modal.classList.add("show")

    // Importante: Quitar aria-hidden para accesibilidad
    modal.removeAttribute("aria-hidden")
    modal.setAttribute("aria-modal", "true")
    modal.setAttribute("role", "dialog")

    // Agregar backdrop
    this.createBackdrop()

    // Bloquear scroll del body
    document.body.classList.add("modal-open")
    document.body.style.overflow = "hidden"
    document.body.style.paddingRight = "15px"

    // Enfocar primer elemento interactivo
    setTimeout(() => {
      const focusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])')
      if (focusable) {
        focusable.focus()
      }
    }, 100)
  },

  // Cerrar modal
  close: function (modalId) {
    console.log(`Cerrando modal: ${modalId}`)
    const modal = document.getElementById(modalId)
    if (!modal) return

    // Ocultar modal
    modal.style.display = "none"
    modal.classList.remove("show")

    // Restaurar atributos de accesibilidad
    modal.setAttribute("aria-hidden", "true")
    modal.removeAttribute("aria-modal")

    // Remover backdrop
    this.removeBackdrop()

    // Restaurar scroll
    document.body.classList.remove("modal-open")
    document.body.style.overflow = ""
    document.body.style.paddingRight = ""
  },

  // Crear backdrop
  createBackdrop: function () {
    // Remover backdrop existente si hay
    this.removeBackdrop()

    // Crear nuevo backdrop
    const backdrop = document.createElement("div")
    backdrop.className = "modal-backdrop fade show"
    backdrop.id = "modal-backdrop"
    document.body.appendChild(backdrop)

    // Agregar evento de click para cerrar modales
    backdrop.addEventListener("click", () => {
      const openModals = document.querySelectorAll(".modal.show")
      if (openModals.length > 0) {
        const lastModal = openModals[openModals.length - 1]
        this.close(lastModal.id)
      }
    })
  },

  // Remover backdrop
  removeBackdrop: () => {
    const backdrop = document.getElementById("modal-backdrop")
    if (backdrop) {
      backdrop.remove()
    }
  },

  // Inicializar sistema de modales
  init: function () {
    console.log("Inicializando sistema de modales independiente")

    // Agregar listeners para botones de cerrar
    document.addEventListener("click", (e) => {
      // Botones con data-dismiss="modal"
      if (e.target.hasAttribute("data-dismiss") && e.target.getAttribute("data-dismiss") === "modal") {
        const modal = e.target.closest(".modal")
        if (modal) {
          this.close(modal.id)
        }
      }

      // Botones con data-bs-dismiss="modal" (Bootstrap 5)
      if (e.target.hasAttribute("data-bs-dismiss") && e.target.getAttribute("data-bs-dismiss") === "modal") {
        const modal = e.target.closest(".modal")
        if (modal) {
          this.close(modal.id)
        }
      }
    })

    // Cerrar modal con ESC
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        const openModals = document.querySelectorAll(".modal.show")
        if (openModals.length > 0) {
          const lastModal = openModals[openModals.length - 1]
          this.close(lastModal.id)
        }
      }
    })

    // Agregar listeners para botones que abren modales
    document.addEventListener("click", (e) => {
      if (e.target.hasAttribute("data-toggle") && e.target.getAttribute("data-toggle") === "modal") {
        const targetModal = e.target.getAttribute("data-target")
        if (targetModal) {
          e.preventDefault()
          this.open(targetModal.replace("#", ""))
        }
      }

      // Bootstrap 5
      if (e.target.hasAttribute("data-bs-toggle") && e.target.getAttribute("data-bs-toggle") === "modal") {
        const targetModal = e.target.getAttribute("data-bs-target")
        if (targetModal) {
          e.preventDefault()
          this.open(targetModal.replace("#", ""))
        }
      }
    })
  },
}

// ===== FUNCIONES PARA MANEJO DE PRODUCTOS Y TALLAS =====

// Función para seleccionar producto
function selectProduct(productId) {
  console.log("Producto seleccionado:", productId)

  // Obtener el botón desde el evento global o buscar por onclick
  let button = null
  if (window.event && window.event.target) {
    button = window.event.target.closest("button")
  } else {
    // Fallback: buscar el botón por onclick que contenga el productId
    const buttons = document.querySelectorAll('button[onclick*="selectProduct"]')
    button = Array.from(buttons).find(
      (btn) => btn.getAttribute("onclick") && btn.getAttribute("onclick").includes(productId.toString()),
    )
  }

  if (!button) {
    console.error("No se pudo encontrar el botón del producto")
    alert("Error: No se pudo encontrar la información del producto")
    return
  }

  selectedProduct = {
    id: productId,
    name: button.getAttribute("data-nombre") || "Producto",
    price: Number.parseInt(button.getAttribute("data-precio")) || 0,
  }

  console.log("Datos del producto:", selectedProduct)

  // Actualizar información del producto en el modal
  const productoNombreTalla = document.getElementById("productoNombreTalla")
  if (productoNombreTalla) {
    productoNombreTalla.textContent = selectedProduct.name
  }

  // Mostrar modal de tallas usando nuestro sistema independiente
  ModalSystem.open("modalTallas")

  // Cargar tallas dinámicamente
  loadProductSizes(productId)
}

// Función para cargar tallas dinámicamente
function loadProductSizes(productId) {
  const tallasContainer = document.getElementById("tallasContainer")
  if (!tallasContainer) {
    console.error("Contenedor de tallas no encontrado")
    return
  }

  // Mostrar loading
  tallasContainer.innerHTML = `
    <div class="text-center py-5">
      <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Cargando...</span>
      </div>
      <h6 class="mb-2">Cargando tallas disponibles...</h6>
      <p class="text-muted mb-0">Por favor espera un momento</p>
    </div>
  `

  // Crear tallas de demostración (para evitar problemas con el PHP)
  setTimeout(() => {
    // Simular tallas para demostración
    const tallasDemo = [
      { id: 1, nombre: "XS", stock: 5 },
      { id: 2, nombre: "S", stock: 10 },
      { id: 3, nombre: "M", stock: 0 },
      { id: 4, nombre: "L", stock: 8 },
      { id: 5, nombre: "XL", stock: 3 },
      { id: 6, nombre: "XXL", stock: 0 },
    ]

    let html = '<div class="row g-3">'

    tallasDemo.forEach((talla) => {
      const hasStock = talla.stock > 0
      const stockClass = hasStock ? "btn-outline-primary" : "btn-outline-secondary"
      const disabled = hasStock ? "" : "disabled"
      const stockText = hasStock ? `Stock: ${talla.stock}` : "Sin stock"
      const stockIcon = hasStock ? "fas fa-check-circle text-success" : "fas fa-times-circle text-danger"

      html += `
        <div class="col-6 col-md-4 col-lg-3">
          <button type="button" 
                  class="btn ${stockClass} w-100 h-100 btnSeleccionarTalla position-relative" 
                  data-talla-id="${talla.id}"
                  data-producto-id="${productId}"
                  data-producto-nombre="${selectedProduct.name}"
                  data-talla-descrip="${talla.nombre}"
                  data-precio="${selectedProduct.price}"
                  data-stock="${talla.stock}"
                  ${disabled}
                  style="min-height: 80px;">
            <div class="d-flex flex-column align-items-center justify-content-center h-100">
              <span class="fs-4 fw-bold mb-1">${talla.nombre}</span>
              <small class="d-flex align-items-center">
                <i class="${stockIcon} me-1"></i>
                ${stockText}
              </small>
            </div>
            ${
              hasStock
                ? `
              <div class="position-absolute top-0 end-0 p-1">
                <i class="fas fa-plus-circle text-primary"></i>
              </div>
            `
                : ""
            }
          </button>
        </div>
      `
    })

    html += "</div>"

    html += `
      <div class="mt-4">
        <div class="alert alert-info d-flex align-items-center" role="alert">
          <i class="fas fa-info-circle me-2"></i>
          <div>
            <strong>Información:</strong> Selecciona una talla con stock disponible para continuar.
          </div>
        </div>
      </div>
    `

    tallasContainer.innerHTML = html
    tallasContainer.classList.add("fade-in")

    // Mostrar mensaje de éxito
    showSuccessMessage("Tallas cargadas correctamente")
  }, 800)
}

// Función para mostrar mensaje de éxito
function showSuccessMessage(message) {
  const toast = document.createElement("div")
  toast.className = "position-fixed top-0 end-0 p-3"
  toast.style.zIndex = "9999"
  toast.innerHTML = `
    <div class="toast show" role="alert">
      <div class="toast-header bg-success text-white">
        <i class="fas fa-check-circle me-2"></i>
        <strong class="me-auto">Éxito</strong>
        <button type="button" class="btn-close btn-close-white" onclick="this.closest('.position-fixed').remove()"></button>
      </div>
      <div class="toast-body">
        ${message}
      </div>
    </div>
  `

  document.body.appendChild(toast)

  setTimeout(() => {
    if (toast.parentNode) {
      toast.remove()
    }
  }, 3000)
}

// Función para seleccionar talla desde botón dinámico
function selectSizeFromButton(button) {
  console.log("Seleccionando talla desde botón:", button)

  if (button.disabled) {
    console.log("Botón deshabilitado - sin stock")
    alert("Esta talla no tiene stock disponible")
    return
  }

  selectedSizeData = {
    tallaId: button.getAttribute("data-talla-id"),
    productoId: button.getAttribute("data-producto-id"),
    productoNombre: button.getAttribute("data-producto-nombre"),
    tallaDescrip: button.getAttribute("data-talla-descrip"),
    precio: Number.parseInt(button.getAttribute("data-precio")) || 0,
    stock: Number.parseInt(button.getAttribute("data-stock")) || 0,
  }

  console.log("Talla seleccionada:", selectedSizeData)
  selectedSize = selectedSizeData.tallaDescrip

  // Actualizar el producto seleccionado con los datos de la talla
  selectedProduct = {
    ...selectedProduct,
    id: selectedSizeData.productoId,
    name: selectedSizeData.productoNombre,
    price: selectedSizeData.precio,
    tallaId: selectedSizeData.tallaId,
    stock: selectedSizeData.stock,
  }

  // Cerrar modal de tallas
  ModalSystem.close("modalTallas")

  // Configurar modal de cantidad
  setupQuantityModal()

  // Mostrar modal de cantidad con delay
  setTimeout(() => {
    ModalSystem.open("modalCantidad")
  }, 300)
}

// Configurar modal de cantidad
function setupQuantityModal() {
  const productoNombreCantidad = document.getElementById("productoNombreCantidad")
  const tallaSeleccionada = document.getElementById("tallaSeleccionada")
  const stockDisponible = document.getElementById("stockDisponible")

  if (productoNombreCantidad) {
    productoNombreCantidad.textContent = selectedSizeData.productoNombre
  }

  if (tallaSeleccionada) {
    tallaSeleccionada.textContent = selectedSizeData.tallaDescrip
  }

  if (stockDisponible) {
    stockDisponible.textContent = selectedSizeData.stock
  }

  const cantidadInput = document.getElementById("cantidad")
  if (cantidadInput) {
    cantidadInput.value = 1
    cantidadInput.max = selectedSizeData.stock
  }

  updateStockAlert(selectedSizeData.stock)
}

function updateStockAlert(stock) {
  const stockAlert = document.getElementById("stockAlert")
  if (!stockAlert) return

  if (stock > 5) {
    stockAlert.className = "alert alert-success mb-0"
    stockAlert.innerHTML = `
      <i class="fas fa-check-circle me-2"></i>
      <strong>Stock disponible:</strong> ${stock} unidades
    `
  } else if (stock > 0) {
    stockAlert.className = "alert alert-warning mb-0"
    stockAlert.innerHTML = `
      <i class="fas fa-exclamation-triangle me-2"></i>
      <strong>Stock limitado:</strong> ${stock} unidades
    `
  } else {
    stockAlert.className = "alert alert-danger mb-0"
    stockAlert.innerHTML = `
      <i class="fas fa-times-circle me-2"></i>
      <strong>Sin stock disponible</strong>
    `
  }
}

// Inicializar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {
  console.log("DOM cargado - Inicializando sistema")

  // Inicializar sistema de modales
  ModalSystem.init()

  // Event listener para tallas con delegation
  document.addEventListener("click", (e) => {
    const tallaButton = e.target.closest(".btnSeleccionarTalla")
    if (tallaButton) {
      console.log("Click en botón de talla detectado")
      e.preventDefault()
      e.stopPropagation()
      selectSizeFromButton(tallaButton)
    }
  })

  // Event listeners para cantidad
  const btnMenos = document.getElementById("btnMenos")
  const btnMas = document.getElementById("btnMas")
  const btnConfirmarCantidad = document.getElementById("btnConfirmarCantidad")

  if (btnMenos) {
    btnMenos.addEventListener("click", () => {
      const input = document.getElementById("cantidad")
      if (input) {
        const currentValue = Number.parseInt(input.value) || 1
        if (currentValue > 1) {
          input.value = currentValue - 1
        }
      }
    })
  }

  if (btnMas) {
    btnMas.addEventListener("click", () => {
      const input = document.getElementById("cantidad")
      if (input) {
        const currentValue = Number.parseInt(input.value) || 1
        const maxStock = selectedSizeData ? selectedSizeData.stock : 999
        if (currentValue < maxStock) {
          input.value = currentValue + 1
        } else {
          alert(`Stock máximo alcanzado: ${maxStock}`)
        }
      }
    })
  }

  if (btnConfirmarCantidad) {
    btnConfirmarCantidad.addEventListener("click", () => {
      const cantidadInput = document.getElementById("cantidad")
      if (!cantidadInput) return

      const quantity = Number.parseInt(cantidadInput.value) || 1

      if (selectedProduct && selectedSize) {
        alert(`${selectedProduct.name} (${selectedSize}) - Cantidad: ${quantity} agregado al carrito`)
        ModalSystem.close("modalCantidad")

        // Aquí iría la lógica para agregar al carrito

        selectedProduct = null
        selectedSize = null
        selectedSizeData = null
      }
    })
  }

  console.log("Sistema inicializado correctamente")
})
