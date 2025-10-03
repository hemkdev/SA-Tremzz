// Gráfico de Usuários
const ctxUsuarios = document.getElementById('usuariosChart').getContext('2d');
new Chart(ctxUsuarios, {
    type: 'line',
    data: {
        labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
        datasets: [{
            label: 'Novos Usuários',
            data: [20, 35, 28, 45, 60, 55, 70],
            borderColor: '#dc3545',  // Borda do quadrado (vermelho)
            backgroundColor: 'rgba(220, 53, 69, 0.8)',  // Preenchimento do quadrado (vermelho semi-opaco; aumentei opacidade para visível)
            tension: 0.4,
            pointStyle: 'line'  // Forma do ponto/quadrado no gráfico e legenda
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                labels: {
                    color: '#e0e0e0',  // Cor do texto
                    usePointStyle: true,  // Ativa o uso de pointStyle (torna o indicador um quadrado)
                    pointStyle: 'line',  // Especifica retângulo/quadrado (opções: 'circle', 'rect', 'rectRounded', 'triangle', 'star', etc.)
                    padding: 20  // Espaçamento ao redor do quadrado na legenda
                }
            }
        }
    }
});


// Gráfico de Mensagens
const ctxMensagens = document.getElementById('mensagensChart').getContext('2d');
new Chart(ctxMensagens, {
    type: 'bar',
    data: {
        labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
        datasets: [{
            label: 'Mensagens Enviadas',
            data: [100, 150, 120, 180, 200, 160, 220],
            backgroundColor: '#dc3545',
            borderColor: '#dc3545',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                labels: {
                    color: '#e0e0e0'
                }
            }
        }
    }
});