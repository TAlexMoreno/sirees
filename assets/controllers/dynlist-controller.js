import { Controller } from "@hotwired/stimulus";
import M from "@materializecss/materialize";
import _, { debounce, select } from "underscore";
// import { AXSContextMenu, MenuItem } from "../classes/AXSContextMenu";
// import SC from "../classes/SC";
// import { loading } from "../misc/functions";
import '../styles/controllers/dynlist.scss';
export default class extends Controller {
    static values = {
        columns: Object,
        classes: {type: Array, default: ["centered", "highlight"]},
        page: {type: Number, default: 1},
        show: {type: Number, default: 10},
        total: {type: Number, default: -1},
        showNumbers: {type: Array, default: [10, 50, 100, 200, 500, 1000]},
        omnisearch: {type: Boolean, default: true},
        searchValue: {type: String, default: ""},
        ventanaSize: {type: Number, default: 5},
        buttons: {type: Array, default: [
            `<button onclick="window.add()" class="btn bg_principal"><i class="material-icons">add</i></button>`,
        ]},
        orderable: {type: Array, default: []},
        emptymessage: {type: String, default: "No se encontraron registros"},
        tabsenabled: {type: Boolean, default: false},
        tabs: {type: Object, default: {}},
        countTabs: {type: Boolean, default: false},
        multiselect: {type: Boolean, default: false},
        staticFilters: {type: Object, default: {} }
    };
    initialize(){
        this.selectedRows = [];
        this.lastBoxChanged = null;
        this.element.addEventListener("toggleMultiselect", () => this.toggleMs());
        this.element.addEventListener("requestSelectedData", () => {
            window.dispatchEvent(new CustomEvent("dynlistSelectedDataResponse", {
                bubbles: true,
                detail: {
                    "selectedUids": this.selectedRows
                }
            }));
        });
        if (this.getCache("show")){
            this.showValue = this.getCache("show");
        }
    }
    async connect(){
        this.directions = ["ASC", "DESC"];
        this.searchValue = this.searchValue ?? this.getCache("searchQuery") ?? "";
        this.cell = typeof window.cell === "function" ? window.cell : (column, row)=>{ return row[column]; };
        this.row = typeof window.row === "function" ? window.row : (row, data) => { return row}
        let omnisearch = this.omnisearchValue ? /*html*/`
            <nav class="bg_principal omnisearch">
                <div class="nav-wrapper">
                    <div class="input-field">
                        <input class="omnisearchInput" type="search" required>
                        <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                        <i class="material-icons omnisearchClose">close</i>
                    </div>
                </div>
            </nav>
        ` : "";
        this.element.innerHTML = /*html*/`
            ${omnisearch}
            <div class='card-content'>
                <span class='card-title'>${this.title}</span>
                <table></table>
            </div>
            <div class='card-action right-align'>
                ${this.buttonsValue.join("")}
            </div>
        `;
        if (this.omnisearchValue){
            this.searchInput = this.element.querySelector(".omnisearchInput");
            this.searchInput.value = this.searchValue
            this.searchInput.addEventListener("input", debounce(async ()=>{
                this.setCache("searchQuery", this.searchInput.value);
                this.pageValue = 1;
                this.searchValue = this.searchInput.value;
                this.constructTable(await this.getData());
            }, 420));
            this.element.querySelector(".omnisearchClose").addEventListener("click",async ()=>{
                this.searchInput.value = "";
                this.searchValue = "";
                this.setCache("searchQuery", "");
                this.constructTable(await this.getData());
            });
        }
        this.tabSelection = this.getCache("tabSelection");
        if (this.tabsenabledValue) this.tabs();
        this.handleHash();
        this.handleShow();
        this.order = [];
        this.orderableValue.forEach((col, idx) => {
            this.order.push({
                priority: idx == 0 ? 1 : 0,
                direction: idx == 0 ? "DESC" : null,
                column: col
            });
        });
        this.tabSelection = this.getCache("tabSelection");
        if (!window.location.hash) this.constructTable(await this.getData());
        window.addEventListener("hashchange", () => this.handleHash());
        window.addEventListener("shortcuts-loaded", () => this.setSC());
        if (window.scm) this.setSC();
    }
    setSC(){
        window.scm.appendSC(new SC({
            nombre: "Avanzar una pagina",
            keycom: "ctrl+alt+right",
            callbk: e => {
                window.location.hash = "pagina+1"
            },
            descri: "Navega hacia adelante en una lista",
            idxble: true
        }));
        window.scm.appendSC(new SC({
            nombre: "Retrocede una pagina",
            keycom: "ctrl+alt+left",
            callbk: e => {
                window.location.hash = "pagina-1"
            },
            descri: "Navega hacia atras en una lista",
            idxble: true
        }));
        window.scm.appendSC(new SC({
            nombre: "Ir a la pagina final",
            keycom: "ctrl+alt+up",
            callbk: e => {
                window.location.hash = "pagina+inf"
            },
            descri: "Navega hacia el final de una lista",
            idxble: true
        }));
        window.scm.appendSC(new SC({
            nombre: "Ir a la primera pagina",
            keycom: "ctrl+alt+down",
            callbk: e => {
                window.location.hash = "pagina-inf"
            },
            descri: "Navega hacia el principio de la lista",
            idxble: true
        }));
        window.scm.appendSC(new SC({
            nombre: "Agregar elemento a la tabla",
            keycom: "ctrl+alt+n",
            callbk: e => {
                if (!("add" in window)) {
                    M.toast({text: "Funcion no implementada"});
                    return;
                }
                window.add();
            },
            idxble: true
        }));
        window.scm.appendSC(new SC({
            nombre: "Alternar selección multiple",
            keycom: "ctrl+alt+m",
            callbk: e => {
                this.toggleMs();
            },
            idxble: true
        }));
    }
    getCache(item){
        return localStorage.getItem(`dynlist${location.pathname}_${item}`);
    }
    setCache(item, value){
        return localStorage.setItem(`dynlist${location.pathname}_${item}`, value);
    }
    handleShow(){
        if (this.getCache("show")){
            this.showValue = this.getCache("show");
        }else {
            if (!this.showNumbersValue.includes(this.showValue)) {
                this.showValue = this.showNumbersValue[0];
            }
        }
        let select = this.element.querySelector("#showNumber");
        if (!select) {
            return;
        }
        select.value = this.showValue;
        select.onchange = async () => {
            this.setCache("show", select.value);
            this.showValue = select.value;
            this.pageValue = 1;
            this.constructTable(await this.getData());
        }
    }
    async handleHash(){
        let hash = location.hash;
        if (hash === "#_") return;
        if (hash.includes("#pagina")){
            let command = hash.replace("#pagina", "");
            if (/\+|-/.test(command)){
                let sign = command.includes("-") ? -1 : 1
                let distance = command.replace("-", "").replace("+", "");
                let calculated = distance == "inf" ? (sign < 0 ? 1 : this.totalPages) : (this.pageValue + sign*distance);
                if (calculated > this.totalPages || calculated < 1) return;
                this.pageValue = calculated;
                this.constructTable(await this.getData());
                location.hash = "pagina_"+this.pageValue;
            }else if (/_/.test(command)){
                this.pageValue = Number(command.replace("_", ""));
                this.constructTable(await this.getData());
            }
        }
    }
    constructTable(data){
        let table = this.element.querySelector("table");
        table.classList.add(...this.classesValue);
        table.classList.add("dynlist")
        table.innerHTML = "";
        let thead = document.createElement("thead");
        this.createHeader(thead);
        table.appendChild(thead);
        let tbody = document.createElement("tbody");
        this.createBody(tbody, data);
        table.appendChild(tbody);
        let tfoot = document.createElement("tfoot");
        this.createFooter(tfoot);
        table.appendChild(tfoot);
        if (this.multiselectValue) {
            this.insertMs();
            this.updateBoxStatus();
            this.updateOmnisearchStatus();
            this.updateSelectedLen();
        }
    }
    tabs(){
        let divs = document.createElement("div");
        let tabs = document.createElement("ul");
        tabs.classList.add("tabs", "tabs-fixed-width", "dynlist");
        let selected = (this.tabSelection - 1) ?? 0;
        this.individualTabs = [];
        Object.entries(this.tabsValue.map).forEach((entrie, index) => {
            if (index == 0) {
                this.tabSelection = entrie[0];
            }
            let tab = document.createElement("li");
            this.individualTabs.push(tab)
            tab.classList.add("tab");
            tab.innerHTML = /*html*/`
                <a href="#tabletab_${entrie[0]}" ${index == selected ? "class='active'" : ""} >${entrie[1]}</a>
            `;
            tabs.appendChild(tab);
            let div = document.createElement("div");
            div.setAttribute("id", `tabletab_${entrie[0]}`);
            divs.appendChild(div);
        });
        this.element.querySelector(".card-content").insertBefore(tabs, this.element.querySelector("table"));
        this.element.querySelector(".card-content").insertBefore(divs, this.element.querySelector("table"));
        this.tabsController = M.Tabs.init(tabs, {
            onShow: e => this.tabClicked(e)
        });
        _.delay(() => this.tabsController.updateTabIndicator(), 500);
        window.addEventListener("layoutChange", () => {
            _.delay(() => this.tabsController.updateTabIndicator(), 500);
        })
        this.createTabContextMenu(tabs);
        if (this.countTabsValue){
            let query = this.constructQuery({getTabCount: true});
            fetch(query, {
                method: "GET",
                headers: new Headers({
                    'Authorization': `Bearer ${this.token}`
                })
            }).then(response => response.json()).then(data => {
                if (data.status != "ok") return;
                let reselect = false;
                this.element.querySelectorAll(".tab").forEach(tab => {
                    let a = tab.querySelector("a");
                    let id = a.href.split("_").at(1);
                    let filter = data.data.filter(el => el.filter == id)
                    let count = filter.length > 0 ? filter.at(0).count : 0;
                    a.innerText += ` (${count})`;
                    if (count === 0) tab.classList.add("hide");
                    reselect = reselect || (tab.classList.contains("hide") && a.classList.contains("active"));
                })
                if (reselect){
                    let selected = this.tabsController.el.querySelector(".tab:not(.hide)>a")?.href.split("#").at(1);
                    if (selected) this.tabsController.select(selected);
                }
            });
        }
    }
    createTabContextMenu(scope){
        this.contextTabMenu = new AXSContextMenu({
            scope: scope,
            items: this.menuItems(),
            classes: ["z-depth-2"]
        });
    }
    /**
     * @param {MouseEvent} me 
     */
    toggleTab(tab){
        tab.classList.toggle("hide");
        this.tabsController.updateTabIndicator();
        this.contextTabMenu.updateItems({items: this.menuItems()});
    }
    hideMenuItem(){
        return new MenuItem({
            label: "ocultar",
            icon: /*html*/`<span class="material-icons">close</span>`,
            callback: (me) => this.toggleTab(me.target.closest(".tab"))
        });
    }
    menuItems(){
        let arr = [this.hideMenuItem(), new MenuItem({separator: true})];
        this.individualTabs.forEach(tab => {
            let label = tab.querySelector("a").innerText.toLowerCase();
            
            arr.push(new MenuItem({
                label: label,
                icon: /*html*/`<span class="material-icons">${!tab.classList.contains("hide") ? "visibility" : "visibility_off"}</span>`,
                callback: (me) => this.toggleTab(tab)
            }));
        })
        return arr;
    }
    async tabClicked(e){
        let id = e.getAttribute("id");
        let data = id.split("_")[1];
        this.tabSelection = data;
        this.setCache("tabSelection", this.tabSelection);
        this.constructTable(await this.getData());
    }
    createHeader(thead){
        let tr = document.createElement("tr");
        for (let column in this.columns){
            let th = document.createElement("th");
            th.innerText = this.columns[column];
            th.setAttribute("cname", column);
            // if (this.orderableValue.includes(column)) th.addEventListener("click", ev => this.changeOrder(column));
            tr.appendChild(th);
        }
        thead.appendChild(tr);
    }
    changeOrder(column){
        let index = 0;
        let max = 0;
        for (let idx in this.order){
            let order = this.order[idx];
            if (order.column == column){
                index = idx
            }
            if (order.priority > max && index !== idx) max = order.priority;
        }
        let direcActual = this.directions.indexOf(this.order[index].direction) + 1;
        if (direcActual == this.directions.length) direcActual = null;
        this.order[index].direction = direcActual !== null ? this.directions[direcActual] : null;
        this.order[index].priority = max + 1;
        if (this.order[index].direction === null) this.order[index].priority = 0;
        this.getData();
    }
    querifyOrders(){
        if (typeof this.order == "undefined") return {};
        let newOrders = this.order.filter(o => o.priority != 0);
        let queries = {};
        for (const order of newOrders) {
            queries[`orderBy[${order.column}]`] = order.direction;
        }
        return queries;
    }
    createBody(tbody, data){
        if (data.length == 0){
            let tr = document.createElement("tr");
            tr.innerHTML = /*html*/`
                <td colspan="${Object.entries(this.columnsValue).length}">${this.emptymessageValue}</td>
            `;
            tbody.appendChild(tr);
            return;
        }
        for(let row of data){
            let tr = document.createElement("tr");
            tr.setAttribute("data-uid", row.id);
            for (let column in this.columns){
                let td = document.createElement("td");
                td.innerHTML = this.cell(column, row);
                tr.appendChild(td);
            }
            tr = this.row(tr, row);
            tbody.appendChild(tr)
        }
    }
    createFooter(tfoot){
        tfoot.innerHTML = "";
        let tr = document.createElement("tr");
        tr.innerHTML = /*html*/`
            <td colspan="${this.colspan}">
                <div class="wrapper">
                    <div class="text">Mostrando</div>
                    <div class="select">
                        <select id="showNumber" class="browser-default"></select>
                    </div>
                    <div class="text">de ${this.totalValue == -1 ? "?" : this.totalValue}</div>
                    <div class="selectedIndicator hide">
                        <i class="material-icons inline-icon">select_check_box</i>
                        <span class="selectedLen">${this.selectedRows}</span>
                    </div>
                    <ul class="pagination"></ul>
                </div>
            </td>
        `;
        this.showNumbersValue.forEach(s => {
            let option = document.createElement("option");
            option.setAttribute("value", s);
            option.innerHTML = s;
            tr.querySelector("#showNumber").append(option);
        })
        this.getPagination(tr.querySelector(".pagination"));
        tfoot.appendChild(tr);
    }
    getPagination(ul){
        let items = [];
        items.push({a: `<a href="#pagina-inf"><i class="material-icons">keyboard_double_arrow_left</i></a>`, status: false})
        items.push({a: `<a href="#pagina-1"><i class="material-icons">chevron_left</i></a>`, status: false})
        
        if (this.totalValue < 0) {
            items.push(`<a href="#!">0</a>`)
        }else {
            let paginas = Math.ceil(this.totalValue/this.showValue);
            let ventanaMin = this.pageValue - this.ventanaSizeValue;
            let ventanaMax = this.pageValue + this.ventanaSizeValue;
            let min = ventanaMin < 1 ? 1 : ventanaMin;
            let max = ventanaMax > paginas ? paginas : ventanaMax;
            for(let p = min; p <= max; p++){
                items.push({a:`<a href="#pagina_${p}">${p}</a>`, status: p == this.pageValue});
            }
            
        }
        items.push({a: `<a href="#pagina+1"><i class="material-icons">chevron_right</i></a>`, status:false})
        items.push({a: `<a href="#pagina+inf"><i class="material-icons">keyboard_double_arrow_right</i></a>`, status:false})
        if(this.pageValue <= 1){
            items[0].status = "disabled";
            items[1].status = "disabled";
        }
        if(this.pageValue >= this.totalPages){
            items[items.length - 2].status = "disabled";
            items[items.length - 1].status = "disabled";
        }
        items.forEach(item => {
            let li = document.createElement("li");
            li.innerHTML = item.a;
            if (item.status === true) li.classList.add("active");
            if (item.status === "disabled") {
                li.classList.add("disabled");
                li.querySelector("a").setAttribute("href", "#_")
            }
            li.classList.add("waves-effect")
            ul.appendChild(li);
        });
    }
    updateFooter(){
        let tfoot = this.element.querySelector("tfoot");
        this.createFooter(tfoot);
        this.handleShow();
        if (this.multiselectValue){
            this.updateSelectedLen();
        }
    }
    async getData(){
        // loading("Cargando datos de la tabla");
        let response = await fetch(this.constructQuery(), {
            method: "GET",
            headers: new Headers({
                'Authorization': `Bearer ${this.token}`
            })
        });
        if (response.status == 403) window.location.href = "/logout";
        let result = await response.text();
        try {
            result = JSON.parse(result);
        } catch (error) {
            console.log(error);
            console.log(result);
        }
        fetch(this.constructQuery({getTotal: true}), {
            method: "GET",
            headers: new Headers({
                'Authorization': `Bearer ${this.token}`
            })
        }).then(response => response.json()).then(json => {
            if (json.status == "ok"){
                this.totalValue = json.data[0].total;
                this.updateFooter();
            }
        })
        // loading();
        if (result.status = "ok") return result.data;

    }
    constructQuery({getTotal=false, getUids=false, getTabCount=false} = {}){
        let url = new URL(window.location.origin + this.url);
        let offset = this.showValue * (this.pageValue - 1);
        if (this.searchValue !== "") url.searchParams.append("searchQuery", this.searchValue ?? "")
        if (getTotal) url.searchParams.append("getTotal", true);
        if (getUids) {
            url.searchParams.append("getUids", true);
        }else {
            url.searchParams.append("show", this.showValue);
            url.searchParams.append("offset", offset);
        }
        let orders = this.querifyOrders();
        for (const i in orders) {
            url.searchParams.append(i, orders[i]);
        }
        for (const j in this.staticFiltersValue){
            url.searchParams.append(j, this.staticFiltersValue[j]);
        }
        if (this.tabsenabledValue && this.tabSelection != 0) {
            url.searchParams.append(`filter[${this.tabsValue.field}]`, this.tabSelection);
        }
        if (getTabCount){
            url.searchParams.append(`getFilterCount`, true);
        }
        return url.toString();
    }
    get columns() {
        return this.columnsValue;
    }
    get token() {
        return this.data.get("token");
    }
    get url(){
        return this.data.get("apiurl") == "" ? window.location.href : this.data.get("apiurl");
    }
    get title(){
        return this.data.get("title") ?? "Titulo";
    }
    get colspan(){
        return Object.keys(this.columnsValue).length;
    }
    get totalPages(){
        return this.total == -1 ? -1 : Math.ceil(this.totalValue/this.showValue);
    }
    toggleMs(){
        this.multiselectValue = !this.multiselectValue;
        if (this.multiselectValue) {
            this.insertMs();
        } else {
            this.removeMs();
        }
    }
    insertMs(){
        if (!this.multiselectValue) return;
        let header = this.element.querySelector("thead tr");
        this.omniselect = this.createCheckbox();
        this.omniselect.setAttribute("id", "omniselect");
        header.prepend(this.omniselect);
        this.omniselect.addEventListener("click", ev => this.omnisearchClick(ev));
        this.element.querySelectorAll("tbody tr").forEach((row, idx) => {
            let cb = this.createCheckbox();
            cb.setAttribute("data-row-idx", idx);
            cb.addEventListener("click", (ev) => {
                if (ev.target.tagName === "INPUT"){
                    this.boxChanged(row.getAttribute("data-uid"), idx, ev.target.checked, ev.shiftKey);
                }
            })
            row.prepend(cb);
        });
        this.updateBoxStatus();
        this.updateSelectedLen();
    }
    omnisearchClick(ev){
        if (ev.target.tagName !== "INPUT") return;
        if (!ev.target.checked){
            this.selectedRows = [];
            this.updateBoxStatus();
            this.updateSelectedLen()
            return;
        }
        fetch(this.constructQuery({getUids: true}), {
            method: "GET",
            headers: new Headers({
                'Authorization': `Bearer ${this.token}`
            })
        }).then(response => response.json()).then(data => {
            if (data.status !== "ok") return;
            this.selectedRows = data.data.map(el => el.id);
            this.updateBoxStatus();
            this.updateSelectedLen()
        });
    }
    createCheckbox(){
        let cb = document.createElement("th");
        cb.classList.add("multiselect-check")
        cb.innerHTML = /*html*/`
            <label>
                <input type="checkbox" />
                <span></span>
            </label>
        `;
        return cb;
    }
    updateBoxStatus(){
        let rows = this.element.querySelectorAll("tbody tr");
        rows.forEach(row => {
            let uid = Number(row.getAttribute("data-uid"));
            row.querySelector(".multiselect-check input").checked = this.selectedRows.indexOf(uid) >= 0;
        });
    }
    updateOmnisearchStatus(){
        if (this.selectedRows.length > 0){
            if (this.totalValue === this.selectedRows.length){
                this.omniselect.querySelector("input").checked = true;
                this.omniselect.querySelector("input").indeterminate = false;
            }else {
                this.omniselect.querySelector("input").checked = false;
                this.omniselect.querySelector("input").indeterminate = true;
            }
        }else {
            this.omniselect.querySelector("input").checked = false;
            this.omniselect.querySelector("input").indeterminate = false;
        }
    }
    boxChanged(uid, idx, state, shift){
        uid = Number(uid);
        this.lastBoxChanged = idx;
        if (state){
            this.selectedRows.push(uid);
        }else {
            let i = this.selectedRows.indexOf(uid);
            this.selectedRows.splice(i, 1);
        }
        this.updateOmnisearchStatus();
        this.updateSelectedLen();
    }
    updateSelectedLen(){
        this.element.querySelector("tfoot .selectedIndicator").classList.remove("hide");
        this.element.querySelector("tfoot .selectedLen").innerHTML = this.selectedRows.length;
    }
    removeMs(){
        this.element.querySelector("tfoot .selectedIndicator").classList.add("hide");
        this.element.querySelectorAll(".multiselect-check").forEach(el => el.remove());
    }
}