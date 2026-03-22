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
  useEffect(() => {
    initI18n(locale).then();
  }, []);
  function startSeason(teamId) {
    let form = document.getElementById('start-season-form');
    let formTeamInput = document.getElementById('start-season-form-team-input');
    formTeamInput.value = teamId.toString();
    form.submit();
  }
  let teamsTable;
  teamsTable = teams.map(team => {
    return /*#__PURE__*/React.createElement("tr", {
      key: team.id
    }, /*#__PURE__*/React.createElement("td", null, team.name), /*#__PURE__*/React.createElement("td", null, /*#__PURE__*/React.createElement("img", {
      alt: team.name,
      className: "f1-car-picture",
      src: team.pictureUrl
    })), /*#__PURE__*/React.createElement("td", null, /*#__PURE__*/React.createElement("button", {
      className: "btn btn-primary btn-sm choose-team-button",
      "data-teamId": team.id,
      onClick: () => startSeason(team.id)
    }, t('choose'))));
  });
  return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
    className: "table-responsive"
  }, /*#__PURE__*/React.createElement("table", {
    className: "table"
  }, /*#__PURE__*/React.createElement("tbody", null, teamsTable))));
}