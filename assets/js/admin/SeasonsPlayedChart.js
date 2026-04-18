import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

function buildSeasonsPlayedChart(container) {
    const seasonsPlayedData = JSON.parse(container.dataset.seasonsPlayedChartData);

    const computerData = [].fill(0, 0, 12);
    const multiplayerData = [].fill(0, 0, 12);

    Array.from(seasonsPlayedData['computer']).map(function (element) {
        const index = monthToDatasetIndex(element['month']);
        computerData[index] = element['seasonsPlayed'];
    });

    Array.from(seasonsPlayedData['multiplayer']).map(function (element) {
        const index = monthToDatasetIndex(element['month']);
        multiplayerData[index] = element['seasonsPlayed'];
    });

    function monthToDatasetIndex(month) {
        const currentMonth = new Date().getMonth() + 1;
        const lastArrayIndex = 11;

        if (currentMonth === month) {
            return lastArrayIndex;
        }

        if (month < currentMonth) {
            return lastArrayIndex - (currentMonth - month);
        }

        return month - currentMonth - 1;
    }

    const data = {
        labels: getLast12Months(),
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

function getLast12Months() {
    const months = [
        'Styczeń',
        'Luty',
        'Marzec',
        'Kwiecień',
        'Maj',
        'Czerwiec',
        'Lipiec',
        'Sierpień',
        'Wrzesień',
        'Październik',
        'Listopad',
        'Grudzień',
    ];

    const currentMonth = new Date().getMonth() + 1;

    const lastYearMonths = months.slice(currentMonth, 12);
    const currentYearMonth = months.slice(0, currentMonth);

    return lastYearMonths.concat(currentYearMonth);
}

addEventListener("DOMContentLoaded", () => {
    const seasonsPlayedChartContainer = document.getElementById('seasons-played-chart');

    if (seasonsPlayedChartContainer) {
        buildSeasonsPlayedChart(seasonsPlayedChartContainer);
    }
});
