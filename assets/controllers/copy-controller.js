import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static values = {
        val: String,
        label: String
    }
    initialize(){
        this.element.classList.add("copy");
        this.value = document.createElement("div");
        this.value.innerHTML = this.valValue;
        this.value.classList.add("value", "z-depth-1");
        this.label = document.createElement("div");
        this.label.innerHTML = this.labelValue;
        this.label.classList.add("label");

        this.element.appendChild(this.label);
        this.element.appendChild(this.value);

        this.element.addEventListener("click", this.click.bind(this))
    }

    async click(ev){
        await navigator.clipboard.writeText(this.valValue);
        M.toast({text: "¡Copiado!"})
    }
}