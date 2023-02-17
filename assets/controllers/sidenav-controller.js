import { Controller } from "@hotwired/stimulus";
import M from "@materializecss/materialize";
import "../styles/controllers/sidenav.scss";

export default class extends Controller {
    
    static values = {
        userData: Object
    }

    initialize(){
        this.element.classList.add("sidenav", "sidenav-fixed");
        this.element.innerHTML = /*html*/ `
            <li>
                <div class="user-view">
                    <div class="background"><img src="https://via.placeholder.com/300x200/333333" alt="fondo"/></div>
                    <a href="#user"><img src="https://via.placeholder.com/70x70/ffffff" alt="profile" class="circle"/></a>
                    <a href="#name"><span class="white-text">${this.userDataValue.nombreCompleto}</span></a>
                    <a href="#email"><span class="white-text">${this.userDataValue.correo}</span></a>
                </div>
            </li>
            <li><a href="/"><i class="material-icons">home</i>Inicio</a></li>
            <li><div class="divider"></div></li>
            <li><a href="/logout"><i class="material-icons">logout</i>Cerrar sesión</a></li>
        `;
        M.Sidenav.init(this.element, {})
        document.body.classList.add("sidenaved")
    }
}