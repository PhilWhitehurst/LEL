/*
 * Phil Whitehurst
 * February 2016
 * Update Bag Drop Details
 */



/*
 * Convert bag drop form into ajax submission
 */

AjaxComponent.attachTo('form[action="updateBagDrops"]',
        {
            trigger_event: 'updatedBagDrops'
        });
/*
 * Add message component for updated bag drop choices
 */
MessageComponent.attachTo('div[type="message-bagdrop"]', {
    listen_events: [
        'updatedBagDrops'
    ]
});

/*
 * Add info component for updated bag drop choices
 */
InfoComponent.attachTo('div[type="info-bagdrop"]', {
    listen_events: [
        'updatedBagDrops'
    ]
});