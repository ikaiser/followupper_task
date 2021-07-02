function initDateTimePicker(){
  M.AutoInit();

  /* Search */
  if ( $(".start_date_datetimepicker_search").length > 0 ){
    $('.start_date_datetimepicker_search').datepicker({
      format: 'dd-mm-yyyy',
      defaultDate: new Date('{{ date( "Y-m-d" ,strtotime($search["search_start_date"])) }}'),
      setDefaultDate: true
    });
  }

  if ( $(".todo_start_date_datetimepicker").length > 0 ){
    $('.todo_start_date_datetimepicker').datepicker({
      format: 'dd-mm-yyyy',
    });
  }

}

function setAddTodoElementModal( e ){

  var date      = $(this).data("date");
  var quotation = $(this).data("quotation");
  var user      = $(this).data("user");
  var quotationName = $(this).data("quotation-name");
  var userName      = $(this).data("user-name");

  if (quotation !== undefined) {
    $("#todo_quotation").val(quotation)
    $(".quotation-name").html(quotationName)
  }

  if (user !== undefined) {
    $("#todo_user").val(user)
    $(".user-name").html(userName)
  }

  $('#start_date').val(date);
  $('#end_date').val(date);

  $('#todo_add').modal('open');
}

function setEditTodoElementModal( e ){
  var target = $(this).data("target");
  $('#'+target).modal('open');
}

function addTodoFormSubmitValidation(){
  var form = $("#todo_add_form")

  var noError = true
  var error   = ""

  var quotation = $("#todo_quotation").val()
  if ( quotation === "" ){
    noError = false
    error += 'You must select a quotation<br />'
  }

  var activity = $("#todo_activity").val()
  if ( activity === "" ){
    noError = false
    error += 'You must select an activity<br />'
  }

  var user = $("#todo_user").val()
  if ( user === "" ){
    noError = false
    error += 'You must select a user<br />'
  }

  // var title = $("#todo_title").val()
  // if ( title === "" ){
  //   noError = false
  //   error   += "You must chose a TODO title <br />"
  // }

  if ( noError ){
    form.submit()
  }else{
    M.toast( { html: error, classes: 'red' } )
  }

}

function editTodoFormSubmitValidation(){
  var form  = $(this).closest(".todo_edit_form");

  var noError = true
  var error   = ""

  var quotation = form.find(".todo_quotation_edit").val()
  if ( quotation === "" ){
    noError = false
    error += 'You must select a quotation<br />'
  }

  var user = form.find(".todo_user_edit").val()
  if ( user === "" ){
    noError = false
    error += 'You must select a user<br />'
  }

  var activity = form.find(".todo_activity_edit").val()
  if ( activity === "" ){
    noError = false
    error += 'You must select an activity<br />'
  }

  // var title = form.find(".todo_title_edit").val()
  // if ( title === "" ){
  //   noError = false
  //   error   += "You must chose a TODO title <br />"
  // }

  var description = form.find(".todo_description_edit").val()
  var startDate   = form.find(".start_date_edit").val()
  var endDate     = form.find(".end_date_edit").val()
  var endDate     = form.find(".end_date_edit").val()

  var completed   = 0
  if (form.find(".todo_completed").is(":checked")){
    completed = 1
  }

  var todoId      = form.find(".todo_id_edit").val()

  params = {
    _token : $('input[name="_token"]').val(),
    todo_title: title,
    todo_description: description,
    start_date: startDate,
    end_date: endDate,
    todo_quotation: quotation,
    todo_user: user,
    todo_completed: completed,
    todo_activity: activity,
  }
  console.log(noError);
  if ( noError ){
    $.ajax({
        url:"/todos/edit/"+todoId,
        method:"POST",
        data: params,
        success:function(data){
            M.toast( { html: data.message, classes: 'green' } )
            setTimeout(function(){
              location.reload()
            }, 500);
        }
    });
  }else{
    M.toast( { html: error, classes: 'red' } )
  }
}

$(document).ready(function () {

  tippy('.tippy-tooltip', {
    content(reference){
      var id       = reference.getAttribute('data-template');
      var template = document.getElementById(id);
      return template.innerHTML;
    },
    allowHTML: true,
  });

  initDateTimePicker();

  $(document).on("dblclick", ".add-todo-element", setAddTodoElementModal );
  $(document).on("click", ".edit-todo-element", setEditTodoElementModal );
  $(document).on("click", "#todo_add_btn", addTodoFormSubmitValidation );
  $(document).on("click", ".todo_edit_form_submit", editTodoFormSubmitValidation );

});
