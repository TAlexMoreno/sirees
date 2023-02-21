import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    initialize(){
        this.element.classList.add("modal", "modal-fixed-footer");
        this.element.innerHTML = /*html*/ `
            <div class="modal-content">
                <h5>Nuevo usuario</h5>
                <p>Escoja el tipo de usuario que quiere crear</p>
                <br>
                <div class="input-field col s12">
                    <select name="tipo" id="tipo">
                        
                    </select>
                    <label for="tipo">Tipo de usuario</label>
                </div>
            </div>
            <div class="modal-footer right-align">
                <button class="btn-flat red-text modal-close">Cancelar</button>
                <button class="btn" id="continuar">Continuar</button>
            </div>
        `;
        this.select = this.element.querySelector("#tipo");
        this.modal = M.Modal.init(this.element, {
            dismissible: false
        })
        window.add = () => {
            this.modal.open();
        }
        this.loadOptions().then(opciones => {
            for (const key in opciones) {
                let opt = document.createElement("option");
                opt.value = key;
                opt.innerHTML = key;
                this.select.append(opt);
            }
            M.FormSelect.init(this.select, {});
        });
        this.element.querySelector("#continuar").addEventListener("click", this.continuar.bind(this));
    }
    continuar(){
        let val = this.select.value.toLocaleLowerCase();
        window.location = `/admin/usuarios/nuevo/${val}`;
    }
    async loadOptions(){
        let response = await fetch("/ajaxUtils/roles");
        return await response.json();
    }
}