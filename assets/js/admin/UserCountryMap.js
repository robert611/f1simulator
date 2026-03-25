import svgMap from 'svgmap';

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
        values: {
            PL: {users: 80, percentageOfAllUsers: 10},
            GB: {users: 10, percentageOfAllUsers: 10},
            US: {users: 10, percentageOfAllUsers: 10}
            // ...
        }
    }
});
