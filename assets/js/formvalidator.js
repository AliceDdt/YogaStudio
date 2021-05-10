
import Form from './modules/Form.js';

let form;

/******** MAIN CODING */
document.addEventListener('DOMContentLoaded', function() {

   const formDom = document.querySelector('#form');
   form = new Form(formDom);

      formDom.addEventListener('submit', e =>{
         e.preventDefault();

         form.validate();

 });

});