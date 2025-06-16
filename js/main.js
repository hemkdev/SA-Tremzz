document.addEventListener("DOMContentLoaded", () => {
    setTimeout(() => {
        document.querySelector(".splash-screen").style.display = "none";
    }, 3000);
});

// MENU ADM
    function getRandomInt(min, max) {
      min = Math.ceil(min);
      max = Math.floor(max);
      return Math.floor(Math.random() * (max - min + 1)) + min;
    }
    // Display the random number once on page load
    const randomNumberEl_1 = document.getElementById('trem-num');
    const randomNum1 = getRandomInt(15, 40);
    randomNumberEl_1.textContent = randomNum1;
    
    const randomNumberEl_2 = document.getElementById('atraso-num');
    const randomNum2 = getRandomInt(1, 5);
    randomNumberEl_2.textContent = randomNum2;
    
    const randomNumberEl_3 = document.getElementById('passageiros-num');
    const randomNum3 = getRandomInt(900, 2000);
    randomNumberEl_3.textContent = randomNum3;

    function formatarMoedaBR(valor) {
      // Usa Intl.NumberFormat para moeda BRL pt-BR
      return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(valor);
    }
    // Exemplo: valor que será formatado
    const cotacao = getRandomInt(10000, 30000);
    const elementoValor = document.getElementById('dinheiro-num');
    elementoValor.textContent = formatarMoedaBR(cotacao);

// TELA 1
    document.addEventListener("DOMContentLoaded", function () {
    setTimeout(() => {
            
            window.location.href = "tela2.html"
        
        }, 3000);
    }, 4000);

// TELA 2
    setTimeout(function () {
            
            window.location.href = "tela3.html"
        
        }, 3000);