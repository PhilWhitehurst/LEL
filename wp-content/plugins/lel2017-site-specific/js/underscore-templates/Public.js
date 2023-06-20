/*
 * Hold underscore html templates used for public areas
 */
if (!Templates) {
    var Templates = {}
}




for (var tmpl in Templates) {
    if (Templates.hasOwnProperty(tmpl)) {
        Templates[tmpl] = _.template(Templates[tmpl]);
    }
}

