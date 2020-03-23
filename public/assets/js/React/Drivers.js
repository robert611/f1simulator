class Drivers extends React.Component {
    state = {
        drivers: null
    }

    componentDidMount() {
        fetch('/drivers/get')
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
                    <tr key={driver.id} id={`driver${driver.id}`} data-driverid={driver.id} className={driver.is_user ? 'highlight-driver' : ''}>
                        <td>{driver.position}</td>
                        <td>{driver.name}</td>
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

document.getElementById('drivers-classification') ? ReactDOM.render(<Drivers />, document.getElementById('drivers-classification')) : null;
