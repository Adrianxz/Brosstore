function AK(id) {
  const $ = window.$ // Declare the $ variable before using it
  event.preventDefault()

  const Id = String(id)
  if (!Id || Id === "undefined" || Id === "null") {
    console.error("ID de producto no valido:", Id)
    return
  }

  // LIMPIAR COMPLETAMENTE TODO EL CONTENIDO ANTERIOR INMEDIATAMENTE
  resetModalContent()

  // Mostrar modal con estado de carga
  $(".js-modal1").show()

  $.ajax({
    method: "POST",
    url: "Backend/Producto/VerProductosR.php",
    data: { Id: Id },
    dataType: "json",
    success: (response) => {
      if (response.error) {
        showError(response.error)
        return
      }

      const { nombre, descripcion, precio, fotoPrincipal, fotosAdicionales, tallas } = response

      // Actualizar contenido
      updateModalContent(nombre, descripcion, precio, fotoPrincipal, fotosAdicionales, tallas, Id)
    },
    error: (xhr, status, error) => {
      console.error("Error en AJAX:", error)
      showError("Hubo un error al obtener los datos del producto.")
    },
  })
}

// Función para resetear completamente el contenido del modal
function resetModalContent() {
  // Limpiar textos
  $(".modal-title").text("Cargando producto...")
  $(".descripcion").html(
    '<div class="text-center p-4"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i><p class="mt-2 text-muted">Cargando información...</p></div>',
  )
  $(".cantidad").text("")
  $(".precio").text("")

  // Limpiar y resetear slider
  if ($(".slick3").hasClass("slick-initialized")) {
    $(".slick3").slick("unslick")
  }
  $(".slick3").html(
    '<div class="text-center p-5 w-100" style="min-height: 400px; display: flex; align-items: center; justify-content: center;"><div><i class="fa fa-spinner fa-spin fa-3x text-muted"></i><p class="mt-3 text-muted">Cargando imágenes...</p></div></div>',
  )

  // Resetear selector de talla
  const selectTalla = $('select[name="talla"]')
  selectTalla.empty().append('<option value="">Cargando tallas...</option>').val("").trigger("change.select2")

  // Resetear cantidad
  $(".cantidad-producto").val(1)

  // Limpiar data del botón
  $(".js-addcart-detail").removeData("id").removeAttr("data-id")
}

// Función para actualizar el contenido del modal
function updateModalContent(nombre, descripcion, precio, fotoPrincipal, fotosAdicionales, tallas, productId) {
  // Actualizar información básica
  $(".modal-title").text(nombre)
  $(".descripcion").html(descripcion)
  $(".cantidad").text("Seleccione una talla")
  $(".precio").text(
    Number.parseFloat(precio).toLocaleString("es-CO", {
      style: "currency",
      currency: "COP",
    }),
  )

  // Construir y actualizar imágenes
  updateProductImages(fotoPrincipal, fotosAdicionales)

  // Actualizar tallas
  updateProductSizes(tallas)

  // Establecer el ID en el botón agregar al carrito
  $(".js-addcart-detail").data("id", productId).attr("data-id", productId)
}

// Función para actualizar las imágenes del producto
function updateProductImages(fotoPrincipal, fotosAdicionales) {
  let html = `
        <div class="item-slick3" data-thumb="${fotoPrincipal}">
            <div class="wrap-pic-w pos-relative">
                <img src="${fotoPrincipal}" alt="IMG-PRODUCT" style="opacity: 0;" onload="this.style.opacity=1; this.style.transition='opacity 0.3s';">
                <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="${fotoPrincipal}">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
        </div>
    `

  if (Array.isArray(fotosAdicionales) && fotosAdicionales.length > 0) {
    fotosAdicionales.forEach((foto) => {
      const path = "images/" + foto.FOTO
      html += `
                <div class="item-slick3" data-thumb="${path}">
                    <div class="wrap-pic-w pos-relative">
                        <img src="${path}" alt="IMG-PRODUCT" style="opacity: 0;" onload="this.style.opacity=1; this.style.transition='opacity 0.3s';">
                        <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="${path}">
                            <i class="fa fa-expand"></i>
                        </a>
                    </div>
                </div>
            `
    })
  }

  // Actualizar HTML y reinicializar slider
  $(".slick3").html(html)

  // Pequeño delay para asegurar que las imágenes se carguen antes de inicializar slick
  setTimeout(() => {
    $(".slick3").slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: true,
      fade: true,
      dots: true,
      appendDots: $(".wrap-slick3-dots"),
      customPaging: (slider, i) => {
        const thumb = $(slider.$slides[i]).data("thumb")
        return `<img src="${thumb}" style="width:60px; height:60px; object-fit:cover; transition: opacity 0.3s;" onload="this.style.opacity=1;">`
      },
      prevArrow: '<button type="button" class="slick-prev custom-prev"><i class="fa fa-chevron-left"></i></button>',
      nextArrow: '<button type="button" class="slick-next custom-next"><i class="fa fa-chevron-right"></i></button>',
    })
  }, 100)
}

// Función para actualizar las tallas
function updateProductSizes(tallas) {
  const selectTalla = $('select[name="talla"]')
  selectTalla.empty().append('<option value="">Elija una opción</option>')

  if (Array.isArray(tallas) && tallas.length > 0) {
    tallas.forEach((talla) => {
      selectTalla.append(`<option value="${talla.TALLA_ID}">${talla.TALLA_DESCRIP}</option>`)
    })
  } else {
    selectTalla.append('<option value="">No hay tallas disponibles</option>')
  }

  selectTalla.val("").trigger("change.select2")
}

// Función para mostrar errores
function showError(message) {
  $(".modal-title").text("Error")
  $(".descripcion").html(
    `<div class="alert alert-danger text-center"><i class="fa fa-exclamation-triangle"></i> ${message}</div>`,
  )
  $(".cantidad").text("")
  $(".precio").text("")
  $(".slick3").html(
    '<div class="text-center p-5"><i class="fa fa-exclamation-triangle fa-3x text-danger"></i><p class="mt-3 text-danger">Error al cargar el producto</p></div>',
  )
}

// Mejorar el reseteo al cerrar la modal
$(document).ready(() => {
  $(".js-hide-modal1").on("click", () => {
    // Resetear completamente el modal
    resetModalContent()

    // Ocultar modal
    $(".js-modal1").hide()
  })

  // También resetear si se hace clic fuera del modal
  $(".js-modal1").on("click", function (e) {
    if (e.target === this) {
      resetModalContent()
      $(this).hide()
    }
  })
})

// Función adicional para precargar imágenes (opcional, para mejor UX)
function preloadImage(src) {
  return new Promise((resolve, reject) => {
    const img = new Image()
    img.onload = () => resolve(img)
    img.onerror = reject
    img.src = src
  })
}
