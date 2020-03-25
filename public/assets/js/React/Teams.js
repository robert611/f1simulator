class Teams extends React.Component {
    state = {
        teams: null
    }

    componentDidMount() {
        fetch('/teams')
            .then((response) => {
                return response.json();
            }).then((data) => {
                this.setState({teams: data});
            });
    }

    render() {
        let teams;

        function startSeason(teamId) {
            let form =  document.getElementById('start-season-form');
            let formTeamInput = document.getElementById('start-season-form-team-input');
    
            formTeamInput.value = teamId;
    
            form.submit();
        }
        

        if(Array.isArray(this.state.teams))
        {
            teams = this.state.teams.map((team) => {
                return (
                    <tr key={team.id}>
                        <td>{team.name}</td>
                        <td><img className="f1-car-picture" src={`/assets/cars/${team.picture}`} /></td>
                        <td><button className="btn btn-outline-info btn-sm choose-team-button" data-teamid={team.id} onClick={() => startSeason(team.id)}>Wybierz</button></td>
                    </tr>
                )
            });
        }

        return (
            <div>
                <div className="table-responsive">
                    <table className="table">
                        <tbody>
                            {teams}
                        </tbody>
                    </table>
                </div>
            </div>
        )
    }
}

document.getElementById('f1-teams') ? ReactDOM.render(<Teams />, document.getElementById('f1-teams')) : null;

