/*
 * Hold underscore html templates used in volunteer area
 */
if (!Templates) {
    var Templates = {}
}

Templates.riderSearchList = [
    '<form class="rider-entry" >'
            , '<input type="hidden" value="<%=rider_id %>'
            , '" name="rider">'
            , '<input type="hidden" value="<%=action %>'
            , '" name="action">'
            , '<span class="col-md-3"><%= rider_id %> </span>'
            , '<span class="col-md-3"><%= first_name %> <%= last_name %></span>'

            , '<span class="col-md-2"><%= country %></span>'
            , '&nbsp;'
            , '<input type="submit" value="<%= confirm %>" class="btn btn-primary">'


            , '</form>'
            , '<br>'
].join('');

Templates.volunteerTrackingList = [
    '<tr class="rider-list" data-id="<%= ID %>" >'
            , '<td> <%= timestamp %></td>'
            , '<td> <%= action %></td>'
            , '<td> <%= rider_id %></td>'
            , '<td> <%= first_name %> <%= last_name %></td>'
            , '<td> <%= country %></td>'
            , '<td> <%= time_in_hand %></td>'
            , '<td> <%= direction %></td>'
            , '<td>'
            , '<form class="rider-entry">'
            , '<input type="hidden" name="ID" value="<%= ID %>">'
            , '<button class="btn btn-primary">Delete</button>'
            , '</form>'
            , '</td>'
            , '</tr>'
].join('');

Templates.publicTrackingList = [
    '<tr class="rider-list"  >'
            , '<td> <%= timestamp %></td>'
            , '<td> <%= action %></td>'
            , '<td> <%= control %></td>'
            , '<td> <%= distance %></td>'
            , '<td> <%= time_in_hand %></td>'
            , '</tr>'
].join('');

Templates.volunteerTrackingNotes = [
    '<tr class="rider-list" data-id="<%= ID %>" >'
            , '<td> <%= timestamp %></td>'
            , '<td> <%= action %></td>'
            , '<td> <%= control %></td>'
            , '<td> <%= distance %></td>'
            , '<td> <%= time_in_hand %></td>'
            , '<td> <%= direction %></td>'
            , '<td>'
            , '<form class="rider-entry">'
            , '<input type="hidden" name="ID" value="<%= ID %>">'
            , '<button class="btn btn-primary">Delete</button>'
            , '</form>'
            , '</td>'
            , '</tr>'
].join('');

Templates.volunteerRiderNotes = [
    '<div class="rider-list" >'
            , '<strong>Name:</strong> <%= first_name %> <%= last_name %><br>'
            , '<strong>Phone:</strong> <%= phone %> <br>'
            , '<strong>Country:</strong> <%= country %> <br>'

            , '<strong>Emergency Contact: </strong> <%= emergency_contact %> <%= emergency_phone %><br>'
            , '<strong>Food: </strong> <%= food_choices %><br>'

            , '<form class="rider-notes">'
            , '<input type="hidden" name="rider" value="<%= rider_user_id %>">'
            , '<div class="form-group">'
            , '<label for="notes">Notes:</label>'
            , '<div><%= notes %></div>'
            , '<br>'
            , '<textarea rows="5" name="notes" class="form-control"></textarea><br>'
            , '<button class="btn btn-primary">UPDATE</button>'
            , '</form>'
            , '</div>'
].join('');




for (var tmpl in Templates) {
    if (Templates.hasOwnProperty(tmpl)) {
        Templates[tmpl] = _.template(Templates[tmpl]);
    }
}

