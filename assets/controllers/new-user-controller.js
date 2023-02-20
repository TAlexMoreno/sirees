import { Controller } from "@hotwired/stimulus";
import M from "@materializecss/materialize";
import "../styles/controllers/copy.scss";

export default class extends Controller {
    initialize(){
        this.element.querySelector("[data-controller=general-form]").addEventListener("submit-success", this.success.bind(this))
        this.modalElement = document.createElement("div");
        this.modalElement.classList.add("modal", "modal-fixed-footer")
        let pass = this.element.querySelector("input[name=password]").value;
        this.modalElement.innerHTML = /*html*/ `
            <div class="modal-content">
                <h5>Alumno creado exitosamente</h5>
                <p>La contraseña del usuario y su matrícula se han generado de manera automática y son las siguiente (click para copiar):</p>
                <div data-controller="copy" data-copy-val-value="${pass}" data-copy-label-value="Contraseña"></div>
                <div data-controller="copy" data-copy-val-value="PH" data-copy-label-value="Matrícula" id="username-value"></div>
            </div>
            <div class="modal-footer right-align">
                <button class="btn" id="exit">ir a la lista de usuarios</button>
            </div>
        `;
        document.body.appendChild(this.modalElement);
        this.modal = M.Modal.init(this.modalElement, {
            dismissible: false
        })
        this.modalElement.querySelector("#exit").addEventListener("click", () => {window.location.href = "/admin/usuarios"});
    }
    /**
     * 
     * @param {CustomEvent} ev 
     */
    success(ev){
        this.modalElement.querySelector("#username-value").setAttribute("data-copy-val-value", ev.detail.username);
        this.modal.open();
    }
}
