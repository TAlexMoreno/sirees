import { Controller } from "@hotwired/stimulus";

export default class extends Controller {

    static values = {
        csrf: String
    }

    initialize(){
        this.element.classList.add("card", "sff");
        this.element.innerHTML = /*html*/`
            <div class="card-content">
                <span class="card-title">Iniciar sesión</span>
                <form id="login_form" class="row noMargin">
                    <input type="hidden" name="_csrf_token" value="${this.csrfValue}"/>
                    <div class="input-field">
                        <i class="material-icons prefix">account_circle</i>
                        <input type="text" id="username" name="username" required/>
                        <label for="username">Nombre de usuario</label>
                    </div>
                    <div class="input-field">
                        <i class="material-icons prefix">password</i>
                        <input type="password" id="password" name="password" required/>
                        <label for="password">Contraseña</label>
                    </div>
                    <span class="errors red-text"></span>
                </form>
            </div>
            <div class="card-action right-align">
                <button type="submit" form="login_form" class="btn">Siguiente</button>
            </div>
        `;
        this.element.querySelector("#login_form").addEventListener("submit", this.login.bind(this));
    }
    /**
     * @param {SubmitEvent} ev 
     */
    async login(ev){
        ev.preventDefault();
        let data = new FormData(ev.target);
        let response = await fetch("", {
            body: data,
            method: "POST"
        });
        if (response.status != 200){
            this.setError("Ha sucedido un error inesperado");
            return;
        }
        let respData = await response.json();
        if (!respData.success){
            this.setError("Credenciales incorrectas");
            return;
        }
        window.location.href = respData.redirect;
    }
    setError(str){
        this.element.querySelector(".errors").innerHTML = str;
    }
}