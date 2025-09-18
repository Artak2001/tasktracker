let burger_button = document.querySelector(".burger-wrapper");
let burger_menu = document.querySelector(".burger-nav");
let point = 2;

burger_button.addEventListener('click' , ()=>{
    
    if( point % 2 !== 0 ) {
        burger_menu.classList.remove('left-minus')
        point++;
        console.log(point,"true");
    }
    else{
        burger_menu.classList.add('left-minus');
        point++
        console.log(point,"false");
    }

})