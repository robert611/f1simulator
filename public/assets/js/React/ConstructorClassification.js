class ConstructorClassification extends React.Component {
    state = {
        teams: null
    }

    componentDidMount(error) {
        fetch('/teams/classification')
            .then((response) => {
                return response.json();
            }).then((data) => {
                this.setState({teams: data});
            });

        console.log(error);
    }

    render() {
        let teams;

        if(Array.isArray(this.state.teams))
        {
            teams = this.state.teams.map((team) => {
                return (
                    <tr key={team.id} id={`team${team.id}`} data-teamid={team.id}>
                        <td>{team.position}</td>
                        <td>{team.name}</td>
                        <td><img className="f1-car-picture" src={`/assets/cars/${team.picture}`} /></td>
                        <td>{team.points}</td>
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
                                <th>Zespół</th>
                                <th>Bolid</th>
                                <th>Punkty</th>
                            </tr>
                        </thead>
                        <tbody>
                            {teams}
                        </tbody>
                    </table>
                </div>
            </div>
        )
    }
}
document.getElementById('teams-classification') ? ReactDOM.render(<ConstructorClassification />, document.getElementById('teams-classification')) : null;
