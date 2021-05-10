import CustomError from './CustomError.js';

class Form {
    
    constructor(form) {//argument form qui contient le ciblage du formulaire
    
      this.form = form; //la propriété this.form contient le ciblage du formulaire  
      this.isValid = true; //tester si l'ensemble du controle des champs est correct
      this.fields = this.form.querySelectorAll('input');
      this.errorTab = []; // à chaque erreur on insérera l'erreur dedans
      
    }


    //methode de validation du formulaire
    validate() {

        //reset des messages d'erreur
        this.resetErrors();

        //controler les champs
        for (const field of this.fields) {

            this.capitalize(field, 'lastName');
            this.capitalize(field, 'firstName');
            this.capitalize(field, 'city');

            //controler si le champ est vide
            if (field.value == '' && field.name != 'address2') {
            //stocker dans le tableau this.errorTab, notre erreur               
                this.errorTab.push(this.createError(field, "Champ obligatoire !")); 
            }

            if (field.name == 'lastName' && !isNaN(field.value)
                 || field.name == 'firstName' && !isNaN(field.value)) {
                 this.errorTab.push(this.createError(field, "nom et/ou prénom ne peuvent pas être numériques !")); 
            }
            
        }

        if(email.value !='' && !this.emailValidator(email.value)){
            //stocker dans le tableau this.errorTab, notre erreur
                this.errorTab.push(this.createError(email, "Email non valide  !"));
        }

        if(password.value != '' && !this.pwdValidator(password.value)){
            this.errorTab.push(this.createError(password, "Le mot de passe doit contenir au moins 8 caractères dont 1 chiffre, 1 majuscule et 1 caractère spécial"));
        }
        
        if(password.value != password2.value){       
         this.errorTab.push(this.createError(password2, "Les 2 mots de passe ne correspondent pas")); 
                   
        }
        
//le controle est ok
        if (this.isValid) {
            this.form.submit(); 
        }
        else {
            //afficher les messages d'erreur
            this.showErrors();
        }
    }
    
    //methode d'affichage des messages d'erreur dans le DOM
    showErrors() {
        for(const error of this.errorTab) {
            let input = error.fieldDom; //utilisation d'un getter pour récupérer le ciblage de l'input
            input.classList.add('error-input');
            input.after(error.getDomError()); //utiliser une methode pour récupérer l'element span
            
        }       
    }
    
    //methode createError
    createError(field, message) { //f contient le ciblage du champ, m le message d'erreur
        //une erreur
        this.isValid = false;
        return new CustomError(field, message);
    }
    
    //suppression de toutes les erreurs
    resetErrors() {        
        this.isValid = true;
        for(const error of this.errorTab) {
            let input = error.fieldDom; 
            input.classList.remove('error-input');
        }
        this.errorTab = []; //bug trouvé le tableau qui contient les erreurs n'etait pas ré-initialisé !
        for (const span of document.querySelectorAll('span.form-error')) {            
          //supprimer le span
          // span contient la ligne du nodelist -> le ciblage d'un span.form-error
          span.remove(); //remove permet de supprimer un element dans le DOM
        }
    }
    
    /*email validation method 
    @params string email
    */
    emailValidator(email) {
        const reg = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return reg.test(email.toLowerCase());
    }

    /*password validation method 
    @params string password
    */
    pwdValidator(password) {
        const regPwd = /^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*\W+)[a-zA-Z0-9\W+]{8,}$/;
        return regPwd.test(password)
    }

    capitalize(field, name){
        if(field.value !='' && field.name == name){
            field.value = field.value.charAt(0).toUpperCase() + field.value.slice(1);
            return field.value;
        }
    }


    
}

export default Form;