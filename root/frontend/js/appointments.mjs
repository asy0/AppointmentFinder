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
    const currentDate = new Date();
    const oneDay = 24 * 60 * 60 * 1000; // One day in milliseconds

    appointments.forEach(appointment => {
        const expirationDate = new Date(appointment.expiration_date);
        const timeDiff = expirationDate.getTime() - currentDate.getTime();
        const daysDiff = Math.ceil(timeDiff / oneDay);
        const isExpired = currentDate > expirationDate;
        const isExpiringToday = daysDiff === 0;
        const isExpiringTomorrow = daysDiff === 1;
        const isExpiringThisWeek = daysDiff > 1 && daysDiff <= 7;

        const card = `
            <div class="col">
                <div class="card h-100 ${isExpired ? 'expired' : ''}">
                    <div class="card-body">
                        <h5 class="card-title clickable" data-appointment-id="${appointment.id}">${appointment.title}</h5><br />
                        <h6 class="card-subtitle mb-2 text-muted">Location: ${appointment.location}</h6><br />
                        <p class="card-text">${appointment.description}</p> <br />
                       
                        ${isExpired ? '<p class="card-text text-danger">Expired</p>' :
            isExpiringToday ? '<p class="card-text text-warning"><b>Expiring today!</b></p>' :
                isExpiringTomorrow ? '<p class="card-text text-warning"><b>Expiring tomorrow!</b></p>' :
                    isExpiringThisWeek ? `<p class="card-text text-warning"><b>Expiring in ${daysDiff} days</b></p>` :
                        ''}
                      <p class="cardfooter"><b><small class="text-muted">Expiration Date: ${appointment.expiration_date}</small></b></p>
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
        let errorHeader = $('<h1 class="text-center w-50 my-3 text-white mx-auto"></h1>');
        if (error.status === 404) {
            $(errorHeader).text('No appointments found...');
        } else {
            $(errorHeader).text('Something went wrong...');
        }
        $(appointmentsContainer).append(errorHeader);
    }
};

export {displayAppointments};

