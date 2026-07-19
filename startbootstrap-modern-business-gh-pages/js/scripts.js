/*!
* Start Bootstrap - Modern Business v5.0.7 (https://startbootstrap.com/template-overviews/modern-business)
* Copyright 2013-2023 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-modern-business/blob/master/LICENSE)
*/
// This file is intentionally blank
// Use this file to add JavaScript to your project


console.log("scripts.js carregado!");
const favoritos = document.querySelectorAll(".favorito-card");

console.log(favoritos);

favoritos.forEach(function(favorito){

    favorito.addEventListener("click", function(){

        const coluna = favorito.closest(".col-lg-3");
        const icone = favorito.querySelector("i");

        if(window.location.pathname.includes("favoritos.html")){

            icone.classList.remove("bi-heart-fill");
            icone.classList.add("bi-heart");

            coluna.classList.add("card-removendo");

            setTimeout(function(){

                coluna.remove();

            },500);

        }else{

            if(icone.classList.contains("bi-heart")){

                icone.classList.remove("bi-heart");
                icone.classList.add("bi-heart-fill");

            }else{

                icone.classList.remove("bi-heart-fill");
                icone.classList.add("bi-heart");

            }

        }

    });

});