/*
import { addAppointmentButton } from './constants.js';
import { addAppointment } from './appointment.mjs';

addAppointmentButton.addEventListener('click', addAppointment);
*/


import {backButton} from "./constants.js";
import {displayAppointments} from "./appointments.mjs";


$(document).ready(() => {
    displayAppointments();
    backButton.addEventListener('click', displayAppointments);
});