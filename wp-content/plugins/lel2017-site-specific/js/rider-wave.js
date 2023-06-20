/*
 * Phil Whitehurst
 *
 * Administer start waves
 */

AjaxComponent.attachTo('form[action="chooseRiderWave"]',
        {
            trigger_event: 'UpdatedChosenWave'
        });

/*
 * Output messages from Chosen Wave Update
 */

MessageComponent.attachTo('#lel-response-message', {
    listen_events: [
        'UpdatedChosenWave'
    ]
});

/*
 * Output info messages if desingated wave set
 */

InfoComponent.attachTo('#lel-wave-detail', {
    listen_events: [
        'UpdatedChosenWave'
    ]
});

/*
 * Output extra content if set
 */

ExtraComponent.attachTo('#lel-extra', {
    listen_events: [
        'UpdatedChosenWave'
    ]
});

/*
 * Hide the choose wave form if designated wave set.
 */
HideShowComponent.attachTo('form[action="chooseRiderWave"]', {
    hide: [{event: 'designatedWaveSet'}],
    show: [{event: ''}]
});

