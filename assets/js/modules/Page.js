class Page{

    constructor(selectorBtn, selectorNav, selectorContent){
        this.btnNav = document.querySelector(selectorBtn);
        this.sideNav = document.querySelector(selectorNav);
        this.main = document.querySelector(selectorContent);
    }

    animateSideNav(){
        this.btnNav.addEventListener('click', () => {
            this.toggleElement(this.sideNav, 'open');
        });
        
        this.main.addEventListener('click', () => {
            this.removeElement(this.sideNav, 'open');                    
       });
    
    }

    displayAlert(selector){
        if(document.querySelector(selector)){
            setTimeout( () => {this.addElement(document.querySelector(selector), 'hidden')}, 2500);
        }
    }

    toggleElement(element, className){
        element.classList.toggle(className);
    }

    removeElement(element, className){
        element.classList.remove(className);
    }

    addElement(element, className){
        element.classList.add(className);
    }

}

export default Page;