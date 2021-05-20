class CustomError {
    constructor(field, message) {
        this._fieldDom = field; //input target
        this.message = message;  //error message
    }
    
    //input getter 
    get fieldDom() {
        return this._fieldDom;
    }
    
    //returns error message 
    getDomError() {
        const spanDom = document.createElement('span');
        spanDom.classList.add('form-error');
        spanDom.innerText = this.message;
        
        return spanDom;
    }
}

export default CustomError;