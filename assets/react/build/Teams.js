import React, { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { initI18n } from '../i18n.js';
function useTeams() {
  const [teams, setTeams] = useState([]);
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
export default function TeamsTable({
  locale
}) {
  const teams = useTeams();
  const {
    t
  } = useTranslation();
  const [expandedTeamId, setExpandedTeamId] = useState(null);
  useEffect(() => {
    initI18n(locale).then();
  }, []);
  function toggleTeam(teamId) {
    setExpandedTeamId(currentTeamId => currentTeamId === teamId ? null : teamId);
  }
  function chooseDriver(teamId, driverId) {
    const form = document.getElementById('start-season-form');
    const formTeamInput = document.getElementById('start-season-form-team-input');
    const formDriverInput = document.getElementById('start-season-form-driver-input');
    formTeamInput.value = teamId.toString();
    formDriverInput.value = driverId.toString();
    form.submit();
  }
  const teamsTable = teams.flatMap(team => {
    const isExpanded = expandedTeamId === team.id;
    const rows = [/*#__PURE__*/React.createElement("tr", {
      key: team.id
    }, /*#__PURE__*/React.createElement("td", null, team.name), /*#__PURE__*/React.createElement("td", null, /*#__PURE__*/React.createElement("img", {
      alt: team.name,
      className: "f1-car-picture",
      src: team.pictureUrl
    })), /*#__PURE__*/React.createElement("td", null, /*#__PURE__*/React.createElement("button", {
      type: "button",
      className: "btn btn-dark-red btn-sm choose-team-button",
      "data-team-id": team.id,
      onClick: () => toggleTeam(team.id)
    }, isExpanded ? t('hide_drivers') : t('choose'))))];
    if (isExpanded) {
      rows.push(/*#__PURE__*/React.createElement("tr", {
        key: `drivers-${team.id}`
      }, /*#__PURE__*/React.createElement("td", {
        colSpan: 3
      }, /*#__PURE__*/React.createElement("div", {
        className: "mt-2 mb-2"
      }, /*#__PURE__*/React.createElement("div", {
        className: "fw-bold mb-2"
      }, t('choose_driver')), /*#__PURE__*/React.createElement("div", {
        className: "d-flex flex-column gap-2"
      }, team.drivers.map(driver => /*#__PURE__*/React.createElement("button", {
        key: driver.id,
        type: "button",
        className: "btn btn-dark-red text-start",
        onClick: () => chooseDriver(team.id, driver.id)
      }, "#", driver.carNumber, " ", driver.name, " ", driver.surname)))))));
    }
    return rows;
  });
  return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
    className: "table-responsive"
  }, /*#__PURE__*/React.createElement("table", {
    className: "table"
  }, /*#__PURE__*/React.createElement("tbody", null, teamsTable))));
}