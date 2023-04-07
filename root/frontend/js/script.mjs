import {backButton} from "./constants.js";
import {displayAppointments} from "./appointments.mjs";


$(document).ready(() => {
    displayAppointments();
    backButton.addEventListener('click', displayAppointments);
});