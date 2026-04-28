import "./bootstrap";

document.addEventListener("DOMContentLoaded", () => {
    const confirmDeleteForms = document.querySelectorAll("form[data-confirm-delete]");

    confirmDeleteForms.forEach((form) => {
        form.addEventListener("submit", (event) => {
            const message =
                form.dataset.confirmMessage ||
                "¿Seguro que querés borrar este elemento?";

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
});