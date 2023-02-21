import { Controller } from "@hotwired/stimulus";
import "../styles/controllers/generalform.scss";

export default class extends Controller {
    static values = {
        url: String,
        token: String,
        method: {type: String, default: "POST"}
    }
    initialize(){
        if (!["POST", "GET", "PUT", "DELETE"].includes(this.methodValue)) {
            throw new Exception("Metodo incorrecto");
        }
        let form = document.createElement("form");
        form.innerHTML = this.element.innerHTML
        this.element.innerHTML = "";
        this.element.classList.add("general-form")
        this.element.appendChild(form);
        form.addEventListener("submit", this.submit.bind(this));
        M.updateTextFields();
    }
    /**
     * 
     * @param {SubmitEvent} ev 
     */
    async submit(ev){
        ev.preventDefault();
        let data = new FormData(ev.target);
        this.startLoading();
        let response = await fetch(this.url, {
            method: this.methodValue,
            body: data,
            headers: new Headers({
                "Authorization": `Bearer ${this.tokenValue}`
            })
        });
        if (response.status != 200) {
            alert("ha ocurrido un error inesperado");
            return;
        }
        let responseData = await response.json();
        if (responseData.status == "ok"){
            this.element.dispatchEvent(new CustomEvent("submit-success", {
                bubbles: true,
                detail: responseData.data
            }));
        }else {
            this.element.dispatchEvent(new CustomEvent("submitted-failure", {
                bubbles: true
            }));
        }
        this.endLoading();
    }
    get url(){
        let url = new URL(`${location.protocol}//${location.host}${this.urlValue}`);
        return url;
    }
    connect(){
        this.element.querySelector("input:not(:read-only)").focus();
    }

    startLoading(){
        this.element.classList.add("loading");
        this.element.querySelectorAll("input:not(:read-only)").forEach(el => {
            el.setAttribute("readonly", true);
            el.classList.add("muted");
        });
    }
    endLoading(){
        this.element.classList.remove("loading");
        this.element.querySelectorAll(".muted").forEach(el => {
            el.removeAttribute("readonly");
            el.classList.remove("muted");
        });
    }
}