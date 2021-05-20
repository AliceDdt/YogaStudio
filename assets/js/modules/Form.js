import CustomError from './CustomError.js';

class Form {
    
    constructor(form) {
    
      this.form = form;
      this.isValid = true; //test if inputs are correct
      this.fields = this.form.querySelectorAll('input');
      this.errorTab = []; //array to insert errors
      
    }


    //form validation process
    validate() {

        //reseting error messages
        this.resetErrors();

        /*fields verification
        errors are pushed into errorTab array*/
        for (const field of this.fields) {

            this.capitalize(field, 'lastName');
            this.capitalize(field, 'firstName');
            this.capitalize(field, 'city');

            //if field is empty
            if (field.value == '' && field.name != 'address2') {            
                this.errorTab.push(this.createError(field, "Champ obligatoire !")); 
            }

            if (field.name == 'lastName' && !isNaN(field.value)
                 || field.name == 'firstName' && !isNaN(field.value)) {
                this.errorTab.push(this.createError(field, "nom et/ou prénom ne peuvent pas être numériques !")); 
            }
            
        }

        if(email.value !='' && !this.emailValidator(email.value)){
            this.errorTab.push(this.createError(email, "Email non valide  !"));
        }

        if(password.value != '' && !this.pwdValidator(password.value)){
            this.errorTab.push(this.createError(password, "Le mot de passe doit contenir au moins 8 caractères dont 1 chiffre, 1 majuscule et 1 caractère spécial"));
        }
        
        if(password.value != password2.value){       
         this.errorTab.push(this.createError(password2, "Les 2 mots de passe ne correspondent pas")); 
                   
        }
        
        //control ok
        if (this.isValid) {
            this.form.submit(); 
        }
        else {
            //display error messages
            this.showErrors();
        }
    }
    
    //display errors 
    showErrors() {
        for(const error of this.errorTab) {
            let input = error.fieldDom; //get the input causing the error
            input.classList.add('error-input');
            input.after(error.getDomError()); //retrieve span
            
        }       
    }
    
    //create an error
    createError(field, message) {
        //if there is an error isValid is set to false
        this.isValid = false;
        return new CustomError(field, message);
    }
    
    //delete all errors 
    resetErrors() {   
       
        this.isValid = true; //reseting isValid     
        
        for(const error of this.errorTab) {
            let input = error.fieldDom; 
            input.classList.remove('error-input');
        }
       
        this.errorTab = []; //empty array
       
        for (const span of document.querySelectorAll('span.form-error')) {            
            span.remove();  //remove span
        }
    }
    
    /*email validation method 
    @params string email
    @returns bool
    */
    emailValidator(email) {
        const reg = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return reg.test(email.toLowerCase());
    }

    /*password validation method 
    @params string password
    @returns bool
    */
    pwdValidator(password) {
        const regPwd = /^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*\W+)[a-zA-Z0-9\W+]{8,}$/;
        return regPwd.test(password)
    }

    /* capitalize field value 
    @params string field, string name
    @returns string field.value
    */
    capitalize(field, name){
        if(field.value !='' && field.name == name){
            field.value = field.value.charAt(0).toUpperCase() + field.value.slice(1);
            return field.value;
        }
    } 
}

export default Form;