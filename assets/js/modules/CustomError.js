class CustomError {
    constructor(field, message) {
        this._fieldDom = field; //ciblage de l'input
        this.message = message;  //le message d'erreur
    }
    
    //getter du ciblage de l'input
    get fieldDom() {
        return this._fieldDom;
    }
    
    //renvoie l'objet span à insérer
    getDomError() {
        const spanDom = document.createElement('span');
        spanDom.classList.add('form-error');
        spanDom.innerText = this.message;
        
        return spanDom;
    }
}

export default CustomError;