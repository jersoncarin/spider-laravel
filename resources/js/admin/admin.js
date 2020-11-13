

if(typeof user_sources !== 'undefined') {
    // Set new default font family and font color to mimic Bootstrap's default styling
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    const dataSet = JSON.parse(user_sources)

    // Pie Chart Example
    var ctx = document.getElementById("user_sources_pie");
    var myPieChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ["Direct", "Referral"],
        datasets: [{
        data: [
            dataSet.direct_sources,
            dataSet.referral_sources
        ],
        backgroundColor: ['#4e73df', '#36b9cc'],
        hoverBackgroundColor: ['#2e59d9', '#36b9cc'],
        hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        tooltips: {
        backgroundColor: "rgb(255,255,255)",
        bodyFontColor: "#858796",
        borderColor: '#dddfeb',
        borderWidth: 1,
        xPadding: 15,
        yPadding: 15,
        displayColors: false,
        caretPadding: 10,
        },
        legend: {
        display: false
        },
        cutoutPercentage: 80,
    },
    });

}

if(typeof earning_sources !== "undefined") {
    // Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

var data = []
for(x of JSON.parse(earning_sources)) {
   data.push(parseFloat(x.total))
}

var months = []
const labels_month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
for(x of JSON.parse(earning_sources)) {
   months.push(
       labels_month[x.month - 1]
   )
}



function number_format(number, decimals, dec_point, thousands_sep) {
  // *     example: number_format(1234.56, 2, ',', ' ');
  // *     return: '1 234,56'
  number = (number + '').replace(',', '').replace(' ', '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}

// Area Chart Example
var ctx = document.getElementById("earnings_chart");
var myLineChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: months,
    datasets: [{
      label: "Earnings",
      lineTension: 0.3,
      backgroundColor: "rgba(78, 115, 223, 0.05)",
      borderColor: "rgba(78, 115, 223, 1)",
      pointRadius: 3,
      pointBackgroundColor: "rgba(78, 115, 223, 1)",
      pointBorderColor: "rgba(78, 115, 223, 1)",
      pointHoverRadius: 3,
      pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
      pointHoverBorderColor: "rgba(78, 115, 223, 1)",
      pointHitRadius: 10,
      pointBorderWidth: 2,
      data: data,
    }],
  },
  options: {
    maintainAspectRatio: false,
    layout: {
      padding: {
        left: 10,
        right: 25,
        top: 25,
        bottom: 0
      }
    },
    scales: {
      xAxes: [{
        time: {
          unit: 'date'
        },
        gridLines: {
          display: false,
          drawBorder: false
        },
        ticks: {
          maxTicksLimit: 7
        }
      }],
      yAxes: [{
        ticks: {
          maxTicksLimit: 5,
          padding: 10,
          // Include a dollar sign in the ticks
          callback: function(value, index, values) {
            return number_format(value);
          }
        },
        gridLines: {
          color: "rgb(234, 236, 244)",
          zeroLineColor: "rgb(234, 236, 244)",
          drawBorder: false,
          borderDash: [2],
          zeroLineBorderDash: [2]
        }
      }],
    },
    legend: {
      display: false
    },
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      titleMarginBottom: 10,
      titleFontColor: '#6e707e',
      titleFontSize: 14,
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      intersect: false,
      mode: 'index',
      caretPadding: 10,
      callbacks: {
        label: function(tooltipItem, chart) {
          var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
          return datasetLabel + number_format(tooltipItem.yLabel);
        }
      }
    }
  }
});

}


var search
$('.search').keyup(function(e) {
    search = $(this).val()
    if(search.trim() != '' && e.keyCode == 13) {
        searchme(search)
    }
    
})

$('.search-btn').click(function() {

  if(search.trim() != '') {
    searchme(search)
  }

})

function searchme(search) {

    let query = '?q='

    if(search == '') {
        query = ''
    }
    
    location.href = location.pathname + query + search
}

$('.edit').click(function() {
  let id = $(this).data('id')
  let url = $(this).data('url')
  let sets = $(this).data('sets')

  fetch_by_url(url,id,sets)
  $('.hidden-id').val(id)
  $('#edit-modal').modal('show')
})

const csrf_token = $('meta[name="csrf-token"]').attr('content')

function fetch_by_url(url,res_id,sets) {

  $.ajax({
      url: url + '/data',
      type: 'post',
      data: {
          _token: csrf_token,
          id : res_id
      },
      dataType: 'json',
      success: function (data) {
          
        for(x of sets.split(',')) {

          $('input[name="' + x + '"]').val(eval("data[0]." + x))

          if(x.includes('select')) {
            let split = x.split('_')
            split.pop()
            let join = split.join('_')
            let select = $('select[name="' + join + '"]')
            select.val(eval("data[0]." + join))
            
            if(select.val() == 2) {
              $('.position_ma_div').removeClass('d-none')
            } else {
              $('.position_ma_div').addClass('d-none')
            }
          }
        }

      }
  })

}

$('.position_select_agent').on('change',function(e) {

  let select = $(this).val()

  if(select == 2) {
    $('.position_ma_div').removeClass('d-none')
  } else {
    $('.position_ma_div').addClass('d-none')
  }

})

var offset = 1
var max = 0
var first_scroll = false
var hasTop = false
var hasSubject = false
var hasTimeout = false
var hasLoaded = false

if(typeof subject_id !== "undefined") {

  axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : csrf_token,
    'Content-Type' : 'application/json'
  }

  $('.send-chat').submit(function(e) {
      e.preventDefault()
      let form = $(this)

      let message = form.find('.input_chat_message').val()

      send_chat(message,subject_id,(response) => {

          if(response.status) {
              hasLoaded = false
              load_chat(1,subject_id)
              form[0].reset()
          }
          
      })

  })

  $('#chat-box').on('scroll', function() {
      var scrollTop = $(this).scrollTop();
      if (scrollTop + $(this).innerHeight() >= this.scrollHeight) {

          //load_chat(1,subject_id)

          first_scroll = false
          

      } else if (scrollTop <= 0) {

          offset++

          if(!first_scroll || offset <= max) {
              load_chat(offset,subject_id)
              first_scroll = true
              hasTop = true
          }
          

      } else {

          hasTop = false;
      }

     
  })

  load_chat(1,subject_id)
  hasSubject = true
 
}



function load_chat(offset,subject_id) {

  axios.get('/admin/csc/requests/chat?page=' + offset + '&s_id=' + subject_id + '&token=' + csrf_token)
  .then(response => {
      offset = response.data.chats.current_page
      max = response.data.chats.last_page

      let isBottom = ''
      let isTop = ''

     $.each(response.data.chats.data,function(key,val) {


          if(hasTop) {

              isTop += ap_pre_pend(val)
              
          } else {

              isBottom += ap_pre_pend(val)
          }

     })

     if(!hasTop) {
          $('.chat-box').html(isBottom)
     } else {
          $('.chat-box').prepend($(isTop))
     }

     if(!hasTimeout || !hasLoaded) {
          scrollToBottom()
          hasLoaded = true
     }

  }).catch(error => console.log(error))
}


function scrollToBottom() {
  $(".chat-box").animate({ scrollTop: $('.chat-box').prop("scrollHeight")}, 1000);
}

const ap_pre_pend = (val) => {

  let monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
  "Jul","Aug", "Sep", "Oct", "Nov", "Dec"
  ];

  let role = ''
  let bg = 'bg-light'
  let text = 'text-muted'
  let img = '<img src="https://res.cloudinary.com/mhmd/image/upload/v1564960395/avatar_usae7z.svg" alt="user" width="50" class="rounded-circle">'
  
  if(val.sender == 'CSR') {
      role = 'ml-auto'
      img = ''
      text = 'text-white'
      bg = 'bg-primary'
  }

  let date = new Date(val.created_at)

  let now = formatAMPM(date) + ' | ' + monthNames[date.getMonth()] + ' ' + addTrailingZeros(date.getDate())

  let data = '<div class="media w-50 ' +  role +' mb-3">' + img
  data += '<div class="media-body ml-3">'
  data += '<div class="' + bg + ' rounded py-2 px-3 mb-2">'
  data += '<p class="text-small mb-0 ' + text +'">' + val.message + '</p>'
  data += '</div>'
  data += '<p class="small text-muted">' + now +'</p>'
  data += '</div>'
  data += '</div>'

  return data
}

const addTrailingZeros = function(n) {
  return n < 10 ? '0' + n : n
}

const formatAMPM = function(date) {
  var hours = date.getHours();
  var minutes = date.getMinutes();
  var ampm = hours >= 12 ? 'pm' : 'am';
  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'
  minutes = minutes < 10 ? '0'+minutes : minutes;
  var strTime = hours + ':' + minutes + ' ' + ampm;
  return strTime;
}



const send_chat = (message,subject_id,callable) => {
  axios.post('/admin/csc/requests/send',{message:message , subject_id : subject_id })
  .then(response => {
     callable(response)
  }).catch(error => console.log(error))
}

const interval = () => {

  if(hasSubject == true) {

      hasTimeout = true
      
      var element = document.querySelector(".chat-box")
      if (element.scrollHeight - element.scrollTop === element.clientHeight) {
          load_chat(1,subject_id)
      }
  }

  setTimeout(interval,2000)
}

interval()
