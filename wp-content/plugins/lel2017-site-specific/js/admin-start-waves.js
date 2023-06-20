/*
 * Phil Whitehurst
 *
 * Administer start waves
 */

jQuery(function () {

    jQuery('#add-wave-submit').click(lel_add_wave_submit);
    lel_get_waves();

});

function lel_add_wave_submit() {
    var action = '?action=addRiderWave';
    // Grab form data
    var data = jQuery('#lel-admin-start-wave').serialize();

    jQuery.ajax({
        type: "POST",
        url: wp.ajaxurl + action,
        data: data,
        success: lel_process_response,
        dataType: 'json'
    });

}

function lel_process_response(data) {
    var status = data.status;
    var msg = data.msg;

    if (status === 'success') {
        var msgclass = "alert-success";
        lel_cancel_wave_submit();
        lel_get_waves()

    }
    else
    {
        var msgclass = "alert-danger";
    }

    jQuery('#lel-update-message').removeClass('alert');
    jQuery('#lel-update-message').removeClass('alert-success');
    jQuery('#lel-update-message').removeClass('alert-danger');
    jQuery('#lel-update-message').addClass('alert');
    jQuery('#lel-update-message').addClass(msgclass);
    jQuery('#lel-update-message').html(msg);


}

function lel_get_waves() {
    var action = '?action=getRiderWaves';

    jQuery.ajax({
        type: "GET",
        url: wp.ajaxurl + action,
        success: lel_show_rider_waves,
        dataType: 'json'
    });

}

function lel_show_rider_waves(data) {
    var output = '<p><strong>Existing start waves</strong></p>';

    var arrayLength = data.length;
    for (var i = 0; i < arrayLength; i++) {

        var tmp = data[i].time_limit.split(':');
        var time_limit = (tmp[1] !== undefined ? tmp[0] + ' hours ' + tmp[1] + ' mins' : tmp[0] + ' hours');

        output += '<p>' + data[i].description
                + ', '
                + data[i].start_time.substr(0, 16)
                + ','
                + time_limit

                + ' , '
                + data[i].total_places
                + ' places'
                + '<a  href="#lel-admin-start-wave" onclick="lel_copy_wave(' + data[i].id + ')" > Amend</a>'
                + '</p>';
        //Do something
    }
    output += '<br>';


    jQuery('#lel-rider-waves').html(output);

}

function lel_copy_wave(id) {
    var action = '?action=getRiderWaves';

    jQuery.ajax({
        type: "GET",
        url: wp.ajaxurl + action + '&id=' + id,
        success: lel_update_wave_form,
        dataType: 'json'
    });
}

function lel_update_wave_form(data) {
    var wave = data[0];
    jQuery('#Description').val(wave.description);
    jQuery('#StartTime').val(wave.start_time);


    jQuery('#TimeLimit').val(wave.time_limit);

    jQuery('#TotalPlaces').val(wave.total_places);
    // set value of hidden id form field
    jQuery('#WaveID').val(wave.id);

    // Show / hide form buttons
    jQuery('#add-wave-submit').addClass('hidden')
            .off("click");
    jQuery('#update-wave-submit').removeClass('hidden')
            .click(lel_update_wave_submit);
    jQuery('#delete-wave-submit').removeClass('hidden')
            .click(lel_delete_wave_submit);
    jQuery('#cancel-wave-submit').removeClass('hidden')
            .click(lel_cancel_wave_submit);

}

function lel_cancel_wave_submit() {
    // Reset the form and buttons
    jQuery('#add-wave-submit').removeClass('hidden')
            .click(lel_add_wave_submit);
    jQuery('#update-wave-submit').addClass('hidden')
            .off("click");
    jQuery('#delete-wave-submit').addClass('hidden')
            .off("click");
    jQuery('#cancel-wave-submit').addClass('hidden')
            .off("click");
    jQuery('#lel-admin-start-wave')[0].reset();
    jQuery('#WaveID').val('');


}

function lel_update_wave_submit() {
    var action = '?action=updateRiderWave';
    // Grab form data
    var data = jQuery('#lel-admin-start-wave').serialize();

    jQuery.ajax({
        type: "POST",
        url: wp.ajaxurl + action,
        data: data,
        success: lel_process_response,
        dataType: 'json'
    });

}

function lel_delete_wave_submit() {
    var action = '?action=deleteRiderWave';
    // Grab form data
    var data = jQuery('#lel-admin-start-wave').serialize();

    jQuery.ajax({
        type: "POST",
        url: wp.ajaxurl + action,
        data: data,
        success: lel_process_response,
        dataType: 'json'
    });
}

