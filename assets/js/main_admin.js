import Page from './modules/Page.js'

document.addEventListener('DOMContentLoaded', function() {

    let myPage = new Page('.navbar__btn','.navbar','.main__admin');
    myPage.animateSideNav();
    myPage.displayAlert('.alert');

});