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
        `;
        M.Sidenav.init(this.element, {})
        document.body.classList.add("sidenaved")
        this.getLinks().then(links => this.renderLinks(links));
    }
    /**
     * 
     * @param {Array<Object>} links 
     */
    renderLinks(links){
        let list = this.element.querySelector("li");
        for (const link of links) {
            let a = document.createElement("a")
            a.href = link.path;
            a.innerHTML = /*html*/ `
                <i class="material-icons">${link.icon}</i>${link.label}
            `;
            list.appendChild(a);
        }
    }
    async getLinks(){
        return [
            {
                path: "/",
                label: "Inicio",
                name: "app_home",
                icon: "home",
                minimumRole: "ROLE_USER"
            },
            {
                path: "/admin/usuarios",
                label: "Usuarios",
                name: "admin_users",
                icon: "group",
                minimumRole: "ROLE_ADMIN"
            },
            {
                path: "/logout",
                label: "Cerrar sesión",
                name: "app_logout",
                icon: "logout",
                minimumRole: "ROLE_USER"
            }
        ]
    }
}