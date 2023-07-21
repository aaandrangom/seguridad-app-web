const btnOpenModal = document.querySelector(".btnNormal .modalInfo");
btnOpenModal.addEventListener('click', abrirModal);

var btnCloseModal = document.querySelector(".close-modal");
btnCloseModal.addEventListener('click', cerrarModal);

function abrirModal() {
    var modalInfo = document.querySelector(".modal");
    modalInfo.classList.add("is-visible");
}

function cerrarModal() {
    var modalInfo = document.querySelector(".modal.is-visible");
    modalInfo.classList.remove("is-visible");
}

document.addEventListener("keyup", e => {
    if (e.key == "Escape" && document.querySelector(".modal.is-visible")) {
      document.querySelector(".modal.is-visible").classList.remove("is-visible");
    }
  });