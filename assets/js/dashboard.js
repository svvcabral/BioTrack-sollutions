document.addEventListener("DOMContentLoaded", function() {
    
    const ctxCriticidade = document.getElementById('chartCriticidade').getContext('2d');
    new Chart(ctxCriticidade, {
        type: 'doughnut',
        data: {
            labels: ['Baixa', 'Média', 'Alta', 'Suporte de Vida'],
            datasets: [{
                data: [120, 75, 31, 18],
                backgroundColor: [
                    '#009eb5', 
                    '#007a8c', 
                    '#ffc107', 
                    '#dc3545'  
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' }
            },
            cutout: '70%'
        }
    });

    const ctxLocalizacoes = document.getElementById('chartLocalizacoes').getContext('2d');
    new Chart(ctxLocalizacoes, {
        type: 'bar',
        data: {
            labels: ['Urgência', 'UCI', 'Bloco Operatório', 'Laboratório', 'Imagiologia'],
            datasets: [{
                label: 'Nº de Equipamentos',
                data: [65, 45, 58, 32, 44],
                backgroundColor: '#009eb5',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                x: { grid: { display: false } }
            }
        }
    });
});