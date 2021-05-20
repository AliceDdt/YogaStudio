import Page from './modules/Page.js'

document.addEventListener('DOMContentLoaded', function() {

    let myPage = new Page('.navbar__btn','.navbar','.main__admin');
    myPage.animateSideNav();
    myPage.displayAlert('.alert');

    // if(document.querySelector('.alert')){ 
    
    // setTimeout(function() {
    //     document.querySelector('.alert').classList.add("hidden");
    //   }, 3000);
    // }

});