import svgMap from 'svgmap';

function buildSvgMap(container) {
    const mapData = JSON.parse(container.dataset.userCountryMapData);

    new svgMap({
        targetElementID: 'admin-user-country-svg-map',
        data: {
            data: {
                users: {
                    name: 'Użytkownicy',
                    format: '{0}',
                    thousandSeparator: ',',
                    thresholdMax: 50000,
                    thresholdMin: 1000
                },
                percentageOfAllUsers: {
                    name: 'Procent wszystkich użytkowników',
                    format: '{0} %'
                }
            },
            applyData: 'users',
            values: mapData,
        },
    });
}

addEventListener("DOMContentLoaded", () => {
    const svgMapContainer = document.getElementById('admin-user-country-svg-map');

    if (svgMapContainer) {
        buildSvgMap(svgMapContainer);
    }
});
