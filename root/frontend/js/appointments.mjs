import {appointmentContainer, apiUrl, appointmentsContainer, backButton} from "./constants.js";
import {displayAppointment} from "./appointment.mjs";

const fetchAppointments = async () => {
    const response = await fetch(apiUrl + '/appointments');
    if (!response.ok) {
        const error = new Error(`Error ${response.status}: ${response.statusText}`);
        error.status = response.status;
        throw error;
    }
    return await response.json();
}

const renderAppointments = async (appointments) => {
    if (appointments.length === 0) {
        const noAppointmentsHeader = $('<h1 class="text-center w-50 my-3 text-white mx-auto"></h1>');
        $(noAppointmentsHeader).text('No appointments found...');
        $(appointmentsContainer).append(noAppointmentsHeader);
        return;
    }
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
    <div class="col mx-auto">
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
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <p class="mb-0"><b><small class="text-muted">Expiration Date: ${appointment.expiration_date}</small></b></p>
                <div class="my-auto clickable" data-appointment-id="${appointment.id}">
                    <i class="fa-solid fa-trash"></i>
                </div>
            </div>
        </div>              
    </div>
`;


        $(appointmentsContainer).append(card);
    });

    $('h5.clickable[data-appointment-id]').on('click', (event) => displayAppointment(event.target.dataset.appointmentId));
    $('div[data-appointment-id]').on('click', (event) => {
        const appointmentId = $(event.target).closest('[data-appointment-id]').data('appointment-id');
        deleteAppointment(appointmentId);
    });

};


const deleteAppointment = async (appointmentId) => {
    const response = await fetch(apiUrl + '/appointments/' + appointmentId + '/', {
        method: 'DELETE',
    });
    // Create flash message div
    const flashMessage = $('<div class="alert fade show text-center mx-auto" role="alert"></div>');
    if (!response.ok) {
        $(flashMessage).addClass('alert-danger');
        $(flashMessage).text('Could not delete, something went wrong...');
    } else {
        $(flashMessage).addClass('alert-success');
        $(flashMessage).text('Appointment deleted successfully!');
        // Remove with fading column with card where h5 has data-appointment-id equal to appointmentId
        $(`h5[data-appointment-id="${appointmentId}"]`).parent().parent().parent().fadeOut(1000, () => {
            $(this).remove();
        });
    }
    // Show flash message between header and appointments container and fade it out after 3 seconds with deleting it
    $(flashMessage).insertAfter('h1').delay(1000).fadeOut(1000, () => {
        $(this).remove();
    });
}

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
        await renderAppointments(appointments);
    } catch (error) {
        let errorHeader = $('<h1 class="text-center w-50 my-3 text-white mx-auto"></h1>');
        $(errorHeader).text('Something went wrong...');
        $(appointmentsContainer).append(errorHeader);
    }
};

export {displayAppointments};

