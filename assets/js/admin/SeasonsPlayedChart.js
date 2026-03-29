import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

function buildSeasonsPlayedChart(container) {
    const data = {
        labels: [
            'Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec',
            'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień'
        ],
        datasets: [
            {
                label: 'Vs komputer',
                data: [5, 8, 6, 10, 7, 12, 9, 11, 6, 8, 7, 10],
                backgroundColor: 'rgba(54, 162, 235, 0.7)'
            },
            {
                label: 'Vs gracze',
                data: [3, 4, 5, 6, 8, 7, 10, 9, 5, 6, 4, 7],
                backgroundColor: 'rgba(255, 99, 132, 0.7)'
            }
        ]
    };

    const config = {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Sezony rozegrane w miesiącach'
                }
            },
            scales: {
                x: {
                    stacked: false // ważne — żeby były obok siebie, nie na sobie
                },
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    new Chart(container, config);
}

addEventListener("DOMContentLoaded", () => {
    const seasonsPlayedChartContainer = document.getElementById('seasons-played-chart');

    if (seasonsPlayedChartContainer) {
        buildSeasonsPlayedChart(seasonsPlayedChartContainer);
    }
});
