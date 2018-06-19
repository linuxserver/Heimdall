/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap')

// jQuery & jQuery UI
import 'jquery-ui/ui/widgets/autocomplete.js'
import 'jquery-ui/ui/widgets/sortable.js'
import 'select2/dist/js/select2.js'

$.when($.ready).then(function () {
  if ($('.message-container').length) {
    setTimeout(
      function () {
        $('.message-container').fadeOut()
      }, 3500)
  }

  if ($('.livestats-container').length) {
    $('.livestats-container').each(function (index) {
      var id = $(this).data('id')
      var dataonly = $(this).data('dataonly')
      var increaseby = (dataonly === 1) ? 20000 : 1000
      var container = $(this)
      var maxTimer = 30000
      var timer = 5000;
      (function worker () {
        $.ajax({
          url: '/get_stats/' + id,
          dataType: 'json',
          success: function (data) {
            container.html(data.html)
            if (data.status === 'active') timer = increaseby
            else {
              if (timer < maxTimer) timer += 2000
            }
          },
          complete: function () {
          // Schedule the next request when the current one's complete
            setTimeout(worker, timer)
          }
        })
      })()
    })
  }

  function readURL (input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader()

      reader.onload = function (e) {
        $('#appimage img').attr('src', e.target.result)
      }

      reader.readAsDataURL(input.files[0])
    }
  }

  $('#upload').change(function () {
    readURL(this)
  })
  /* $(".droppable").droppable({
        tolerance: "intersect",
        drop: function( event, ui ) {
            var tag = $( this ).data('id');
            var item = $( ui.draggable ).data('id');

            $.get('tag/add/'+tag+'/'+item, function(data) {
                if(data == 1) {
                    $( ui.draggable ).remove();
                } else {
                    alert('not added');
                }
            });

        }
      }); */

  $('#sortable').sortable({
    stop: function (event, ui) {
      var idsInOrder = $('#sortable').sortable('toArray', {
        attribute: 'data-id'
      })
      $.post(
        '/order',
        { order: idsInOrder }
      )
    }

  })
  $('#sortable').sortable('disable')

  $('#app').on('click', '#config-button', function (e) {
    e.preventDefault()
    var app = $('#app')
    var active = (app.hasClass('header'))
    app.toggleClass('header')
    if (active) {
      $('.add-item').hide()
      $('.item-edit').hide()
      $('#app').removeClass('sidebar')
      $('#sortable').sortable('disable')
    } else {
      $('#sortable').sortable('enable')
      setTimeout(function () {
        $('.add-item').fadeIn()
        $('.item-edit').fadeIn()
      }, 350)
    }
  }).on('click', '#add-item, #pin-item', function (e) {
    e.preventDefault()
    var app = $('#app')
    var active = (app.hasClass('sidebar'))
    app.toggleClass('sidebar')
  }).on('click', '.close-sidenav', function (e) {
    e.preventDefault()
    var app = $('#app')
    app.removeClass('sidebar')
  }).on('click', '#test_config', function (e) {
    e.preventDefault()
    var apiUrl = $('#create input[name=url]').val()

    var overrideUrl = $('#create input[name="config[override_url]"').val()
    if (overrideUrl.length && overrideUrl !== '') {
      apiUrl = overrideUrl
    }

    var data = {}
    data['url'] = apiUrl
    $('input.config-item').each(function (index) {
      var config = $(this).data('config')
      data[config] = $(this).val()
    })

    $.post('/test_config', { data: data }, function (data) {
      alert(data)
    })
  })
  $('#pinlist').on('click', 'a', function (e) {
    e.preventDefault()
    var current = $(this)
    var id = current.data('id')
    $.get('items/pintoggle/' + id + '/true', function (data) {
      var inner = $(data).filter('#sortable').html()
      $('#sortable').html(inner)
      current.toggleClass('active')
    })
  })

  var elem = $('.color-picker')[0]
  var hueb = new Huebee(elem, {
  // options
  })

  $('#appname').autocomplete({
    source: availableApps,
    select: function (event, ui) {
      $.post('/appload', { app: ui.item.value }, function (data) {
        $('#appimage').html("<img src='/storage/" + data.icon + "' /><input type='hidden' name='icon' value='" + data.icon + "' />")
        $('input[name=colour]').val(data.colour)
        hueb.setColor(data.colour)
        $('input[name=pinned]').prop('checked', true)
        if (data.config !== null) {
          $.get('/view/' + data.config, function (getdata) {
            $('#sapconfig').html(getdata).show()
          })
        }
      }, 'json')
    }
  })

  $('.tags').select2()
  // $('.tags').select2({
  //   theme: 'bootstrap'
  // })
})
