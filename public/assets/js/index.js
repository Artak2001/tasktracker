let reg_btn = document.querySelector(".register-btn");
let sign_in_btn = document.querySelector(".sign-in-btn");
let register_area = document.querySelector(".sign-up-area");
let login_area = document.querySelector(".sign-in-area");

// Sign in / Sign Up

reg_btn.addEventListener('click' , ()=>{
    login_area.classList.add("display-none");
    register_area.classList.remove("display-none");
})
sign_in_btn.addEventListener('click' , ()=>{
    register_area.classList.add("display-none");
    login_area.classList.remove("display-none");
})

