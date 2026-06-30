import React, {useEffect, useState} from 'react';
import { useTranslation } from 'react-i18next';
import { initI18n } from '../i18n.js';

type Driver = {
    id: number;
    carNumber: number;
    name: string;
    surname: string;
};

type Team = {
    id: number;
    name: string;
    picture: string;
    pictureUrl: string;
    drivers: Driver[];
};

type TeamsProps = {
    locale: string;
};

function useTeams() {
    const [teams, setTeams] = useState<Team[]>([]);

    useEffect(() => {
        async function fetchTeams() {
            const response = await fetch('/teams');
            const data = await response.json();
            setTeams(data);
            console.log(data);
        }

        fetchTeams().then();
    }, []);

    return teams;
}

export default function TeamsTable({ locale }: TeamsProps) {
    const teams = useTeams();
    const { t } = useTranslation();
    const [expandedTeamId, setExpandedTeamId] = useState<number | null>(null);

    useEffect(() => {
        initI18n(locale).then();
    }, []);

    function toggleTeam(teamId: number) {
        setExpandedTeamId((currentTeamId: number) => currentTeamId === teamId ? null : teamId);
    }

    function chooseDriver(teamId: number, driverId: number) {
        const form = document.getElementById('start-season-form') as HTMLFormElement | null;
        const formTeamInput = document.getElementById('start-season-form-team-input') as HTMLInputElement | null;
        const formDriverInput = document.getElementById('start-season-form-driver-input') as HTMLInputElement | null;
        formTeamInput.value = teamId.toString();
        formDriverInput.value = driverId.toString();
        form.submit();
    }

    const teamsTable = teams.flatMap((team) => {
        const isExpanded = expandedTeamId === team.id;

        const rows = [
            (
                <tr key={team.id}>
                    <td>{team.name}</td>
                    <td>
                        <img alt={team.name} className="f1-car-picture" src={team.pictureUrl} />
                    </td>
                    <td>
                        <button
                            type="button"
                            className="btn btn-dark-red btn-sm choose-team-button"
                            data-team-id={team.id}
                            onClick={() => toggleTeam(team.id)}
                        >
                            {isExpanded ? t('hide_drivers') : t('choose')}
                        </button>
                    </td>
                </tr>
            )
        ];

        if (isExpanded) {
            rows.push(
                <tr key={`drivers-${team.id}`}>
                    <td colSpan={3}>
                        <div className="mt-2 mb-2">
                            <div className="fw-bold mb-2">
                                {t('choose_driver')}
                            </div>
                            <div className="d-flex flex-column gap-2">
                                {team.drivers.map((driver) => (
                                    <button
                                        key={driver.id}
                                        type="button"
                                        className="btn btn-dark-red text-start"
                                        onClick={() => chooseDriver(team.id, driver.id)}
                                    >
                                        #{driver.carNumber} {driver.name} {driver.surname}
                                    </button>
                                ))}
                            </div>
                        </div>
                    </td>
                </tr>
            );
        }

        return rows;
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
