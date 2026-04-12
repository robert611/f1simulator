import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

function buildSeasonsPlayedChart(container) {
    const seasonsPlayedData = JSON.parse(container.dataset.seasonsPlayedChartData);

    const computerData = [].fill(0, 0, 12);
    const multiplayerData = [].fill(0, 0, 12);

    Array.from(seasonsPlayedData['computer']).map(function (element) {
        computerData[element['month'] - 1] = element['seasonsPlayed'];
    });

    Array.from(seasonsPlayedData['multiplayer']).map(function (element) {
        multiplayerData[element['month'] - 1] = element['seasonsPlayed'];
    });

    const data = {
        labels: [
            'Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec',
            'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień',
        ],
        datasets: [
            {
                label: 'Vs komputer',
                data: computerData,
                backgroundColor: 'rgba(54, 162, 235, 0.7)'
            },
            {
                label: 'Vs gracze',
                data: multiplayerData,
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
                    text: 'Sezony rozegrane w miesiącach',
                },
            },
            scales: {
                x: {
                    stacked: false // ważne — żeby były obok siebie, nie na sobie
                },
                y: {
                    beginAtZero: true,
                },
            },
        },
    };

    new Chart(container, config);
}

addEventListener("DOMContentLoaded", () => {
    const seasonsPlayedChartContainer = document.getElementById('seasons-played-chart');

    if (seasonsPlayedChartContainer) {
        buildSeasonsPlayedChart(seasonsPlayedChartContainer);
    }
});
