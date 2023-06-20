/*
 * Hold underscore html templates
 */
var Templates = {};
Templates.riderList = [
    '<div class="row">'
            , '<input type="hidden" value="<%= id %>'
            , '" name="User <%=id%> ">'
            , '<span class="col-md-3"><%= first_name %> <%= last_name %></span>'
            , '<span class="col-md-2"><%= team_name %></span>'
            , '<span class="col-md-2"><%= chosen_wave %></span>'
            , '<span class="col-md-2">'
            , '<select class="form-control js_designated_start" id="designated<%=id%>"'
            , ' name="DesignatedStart<%= id %>">'
            , '<%= designated_starts %>'
            , '</select>'
            , '</span>'
            , '</div>'
].join('\n');


for (var tmpl in Templates) {
    if (Templates.hasOwnProperty(tmpl)) {
        Templates[tmpl] = _.template(Templates[tmpl]);
    }
}