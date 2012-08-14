define(['underscore','backbone.forms'],function(_, Form) {
                      
            //TWITTER BOOTSTRAP TEMPLATES
            //Requires Bootstrap 2.x
            Form.setTemplates({

                  //HTML
                  form: '\
      <form class="form-horizontal">{{fieldsets}}</form>\
    ',

                  fieldset: '\
      <fieldset>\
        <legend>{{legend}}</legend>\
        {{fields}}\
      </fieldset>\
    ',

                  field: '\
      <div class="control-group">\
        <label class="control-label" for="{{id}}">{{title}}</label>\
        <div class="controls">\
          <div class="input">{{editor}}</div>\
          <div class="help-block">{{help}}</div>\
        </div>\
      </div>\
    ',
                  checkboxes: '\
      <div class="control-group">\
        <label class="control-label" for="{{id}}">{{title}}</label>\
        <div class="controls">\
          {{editor}}\
          <div class="help-block">{{help}}</div>\
        </div>\
      </div>\
    ',

                  nestedField: '\
      <div>\
        <div title="{{title}}" class="input-xlarge">{{editor}}</div>\
        <div class="help-block">{{help}}</div>\
      </div>\
    ',

                  list: '\
      <div class="bbf-list">\
        <ul class="unstyled clearfix">{{items}}</ul>\
        <button class="btn bbf-add" data-action="add">Add</div>\
      </div>\
    ',

                  listItem: '\
      <li class="clearfix">\
        <div class="pull-left">{{editor}}</div>\
        <button class="btn bbf-del" data-action="remove">x</button>\
      </li>\
    ',

                  date: '\
      <div class="bbf-date">\
        <select data-type="date" class="bbf-date">{{dates}}</select>\
        <select data-type="month" class="bbf-month">{{months}}</select>\
        <select data-type="year" class="bbf-year">{{years}}</select>\
      </div>\
    ',

                  dateTime: '\
      <div class="bbf-datetime">\
        <p>{{date}}</p>\
        <p>\
          <select data-type="hour" style="width: 4em">{{hours}}</select>\
          :\
          <select data-type="min" style="width: 4em">{{mins}}</select>\
        </p>\
      </div>\
    ',

                  'list.Modal': '\
      <div class="bbf-list-modal">\
        {{summary}}\
      </div>\
    '
            }, {
  
                  //CLASSNAMES
                  error: 'error' //Set on the field tag when validation fails
            });
      
            Form.editors.Checkboxes = Form.editors.Checkboxes.extend({
                  tagName: 'div',
                 /**
                  * Create the checkbox list HTML
                  * @param {Array} Options as a simple array e.g. ['option1', 'option2']
                  *                or as an array of objects e.g. [{val: 543, label: 'Title for object 543'}]
                  * @return {String} HTML
                  */
                  _arrayToHtml: function (array) {
                        var html = [];
                        var self = this;

                        _.each(array, function(option, index) {
                              var obj = {
                                    name: self.id,
                                    id: self.id+'-'+index
                              };
                              if (_.isObject(option)) {
                                    var val = option.val ? option.val : '';
                                    _.extend(obj,{
                                          value: val,
                                          label: option.label,
                                          popover: option.popover
                                    });
                              }
                              else {
                                    _.extend(obj,{
                                          value: option,
                                          label: option
                                    });
                              }
                              html.push(_.template('\
                                    <label class="checkbox">\
                                          <input type="checkbox" name="<%=name%>" id="<%=id%>" value="<%=value%>" /> \
                                          <% if(popover) { %>\
                                                <span class="popable" title="<%=popover.title%>" data-content="<%=popover.content%>"><%=label%></span>\
                                          <% } else { %>\
                                                <%=label%>\
                                          <% } %>\
                                    </label>\
                              ', obj));
                        });

                        return html.join('');
                  }
            });           
            
            
            return Form;

      });
