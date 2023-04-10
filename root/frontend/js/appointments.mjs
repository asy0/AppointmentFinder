import {appointmentContainer, apiUrl, appointmentsContainer, backButton} from "./constants.js";
import {displayAppointment} from "./appointment.mjs";

const fetchAppointments = async () => {
    const response = await fetch(apiUrl + '/appointments/?ordering=-expiration_date');
    if (!response.ok) {
        const error = new Error(`Error ${response.status}: ${response.statusText}`);
        error.status = response.status;
        throw error;
    }
    return await response.json();
}

const renderAppointments = async (appointments) => {
    appointments.forEach(appointment => {
        const currentDate = new Date();
        const expirationDate = new Date(appointment.expiration_date);
        const isExpired = currentDate > expirationDate;

        const card = `
            <div class="col">
                <div class="card h-100 ${isExpired ? 'expired' : ''}">
                    <div class="card-body">
                        <h5 class="card-title ${isExpired ? '' : 'clickable'}" data-appointment-id="${appointment.id}">${appointment.title}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Location: ${appointment.location}</h6>
                        <p class="card-text">${appointment.description}</p>
                        <p class="card-text"><small class="text-muted">Expiration Date: ${appointment.expiration_date}</small></p>
                    </div>
                </div>
            </div>
        `;
        $(appointmentsContainer).append(card);
    });
    $('h5.clickable[data-appointment-id]').on('click', (event) => displayAppointment(event.target.dataset.appointmentId));
};


const prepareAppointments = () => {
    if (appointmentsContainer.children.length > 0) {
        $(appointmentsContainer).empty();
    }
    $(appointmentContainer).hide();
    $(appointmentsContainer).show();
    $(backButton).hide();
}

const displayAppointments = async () => {
    prepareAppointments();
    try {
        const appointments = await fetchAppointments();
        appointments.sort((a, b) => new Date(a.expiration_date) - new Date(b.expiration_date));
        await renderAppointments(appointments);
    } catch (error) {
        if (error.status === 404) {
            alert('No appointments found. Please create a new appointment.');
        } else {
            alert('Failed to fetch appointments. Please try again later.');
        }
    }
};

export {displayAppointments};