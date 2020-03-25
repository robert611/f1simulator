class LastRaceResults extends React.Component {
    state = {
        drivers: null
    }

    componentDidMount() {
        fetch('/drivers/last/race/results')
            .then((response) => {
                return response.json();
            }).then((data) => {
                console.log(data);
                this.setState({drivers: data});
            });
    }

    render() {
        let drivers;

        if(Array.isArray(this.state.drivers))
        {
            drivers = this.state.drivers.map((driver) => {
                return (
                    <tr key={driver.id} id={`driver${driver.id}`} data-driverid={driver.id} className={driver.isUser == true ? 'highlight-driver' : ''}>
                        <td>{driver.position}</td>
                        <td>{driver.name} {driver.surname}</td>
                        <td>{driver.team.name}</td>
                        <td><img className="f1-car-picture" src={`/assets/cars/${driver.team.picture}`} /></td>
                        <td>{driver.points}</td>
                    </tr>
                )
            });
        }
        
        return (
            <div>
                <div className="table-responsive">
                    <table className="table">
                        <thead>
                            <tr>
                                <th>Miejsce</th>
                                <th>Kierowca</th>
                                <th>Zespół</th>
                                <th>Bolid</th>
                                <th>Punkty</th>
                            </tr>
                        </thead>
                        <tbody>
                            {drivers}
                        </tbody>
                    </table>
                </div>
            </div>
        )
    }
}

document.getElementById('last-race-results') ? ReactDOM.render(<LastRaceResults />, document.getElementById('last-race-results')) : null;