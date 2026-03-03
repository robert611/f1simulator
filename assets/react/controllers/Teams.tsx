import React, {useEffect, useState} from 'react';

type Team = {
    id: number;
    name: string;
    picture: string;
    pictureUrl: string;
};

function useTeams() {
    const [teams, setTeams] = useState<Team[]>([]);

    useEffect(() => {
        async function fetchTeams() {
            const response = await fetch('/teams');
            const data = await response.json();
            setTeams(data);
        }

        fetchTeams().then();
    }, []);

    return teams;
}

export default function TeamsTable() {
    const teams = useTeams();

    function startSeason(teamId: Number) {
        let form= document.getElementById('start-season-form') as HTMLFormElement;
        let formTeamInput = document.getElementById('start-season-form-team-input') as HTMLInputElement;
        formTeamInput.value = teamId.toString();
        form.submit();
    }

    let teamsTable: any;
    teamsTable = teams.map((team) => {
        return (
            <tr key={team.id}>
                <td>{team.name}</td>
                <td><img alt={team.name} className="f1-car-picture" src={team.pictureUrl} /></td>
                <td>
                    <button className="btn btn-primary btn-sm choose-team-button"
                            data-teamId={team.id}
                            onClick={() => startSeason(team.id)}>
                        Wybierz
                    </button>
                </td>
            </tr>
        )
    });

    return (
        <div>
            <div className="table-responsive">
                <table className="table">
                    <tbody>
                        {teamsTable}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
