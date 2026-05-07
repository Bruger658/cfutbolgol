import "./bootstrap";

document.addEventListener("DOMContentLoaded", () => {
    const confirmDeleteForms = document.querySelectorAll("form[data-confirm-delete]");

     let pendingForm = null;
    const modal = document.createElement("div");
    modal.className =
        "fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4";
    modal.innerHTML = `
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            <h2 class="text-lg font-semibold text-gray-900">Confirmar eliminación</h2>
            <p class="mt-2 text-sm text-gray-600" id="delete-confirm-message"></p>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" id="delete-cancel-btn" class="rounded border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancelar</button>
                <button type="button" id="delete-confirm-btn" class="rounded bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Sí, borrar</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    const messageEl = modal.querySelector("#delete-confirm-message");
    const cancelBtn = modal.querySelector("#delete-cancel-btn");
    const confirmBtn = modal.querySelector("#delete-confirm-btn");

    const closeModal = () => {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
        pendingForm = null;
    };


    confirmDeleteForms.forEach((form) => {
        form.addEventListener("submit", (event) => {
            if (form.dataset.skipConfirm === "true") {
                form.dataset.skipConfirm = "false";
                return;
            }

            event.preventDefault();

            const message =
                form.dataset.confirmMessage ||
                "¿Seguro que querés borrar este elemento?";

            pendingForm = form;
            messageEl.textContent = message;
            modal.classList.remove("hidden");
            modal.classList.add("flex");
        });
    });

    cancelBtn?.addEventListener("click", closeModal);

    modal.addEventListener("click", (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    confirmBtn?.addEventListener("click", () => {
        if (!pendingForm) {
            return;
        }

        pendingForm.dataset.skipConfirm = "true";
        pendingForm.requestSubmit();
        closeModal();
    });
});