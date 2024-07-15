import flags from './flags.js'

function windDirect(w){
if(w>=0 && w<23){return "северный";}
if(w>=23 && w<68){return "северно-восточный";}
if(w>=68 && w<113){return "восточный";}
if(w>=113 && w<158){return "юго-восточный";}
if(w>=158 && w<203){return "южный";}
if(w>=203 && w<248){return "юго-западный";}
if(w>=248 && w<293){return "западный";}
if(w>=293 && w<338){return "северно-западный";}
if(w>=338 && w<=360){return "северный";}
}



async function requestAPI(url, method='POST', payload) {
  let response = await fetch(url,{
    method: method,
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(payload)
  });

  if (response.ok) {
    let json = await response.json();
    return json;
  }
  else {
    console.log("Ошибка HTTP: " + response.status);
  }

}


let app = document.getElementById("weather");


document.addEventListener('DOMContentLoaded', () => {

 requestAPI('./settings.json','GET').then(
   (result) => {
     let dataweather = {
       datasets : [],
       labels : []
     }

     let item_number = 0;
     let domainLables = [];
     let promisesCount = result.places.length;

     result.places.forEach( item =>{
       requestAPI('./place_'+item+'.json','GET').then(
         (result) => {

           let flag = '('+result.current.sys.country+')';

             flags.forEach( item=>{
               if(item.code==result.current.sys.country){
                 flag = item.emoji;
               }
             })

           let now = Date.parse('01 Jan 1970 '+result.current.time+' GMT');
           now  = now/1000 + result.current.timezone - 10800 - 10800;



           let b = new Date(now*1000);
           let hours = '0' + b.getHours();
           let minutes = '0' + b.getMinutes();
           let seconds = '0' + b.getSeconds();

           let time = hours.slice(-2) + ':' + minutes.slice(-2) + ':' + seconds.slice(-2);

           app.insertAdjacentHTML("beforeend", '' +
             '    <div class="weather-item">\n' +
             '      <div class="weather-item-icon">\n' +
             '        <img src="https://dolzhenkov.ru/weather/icons/'+result.current.weather[0].icon+'.svg" alt="'+result.current.weather[0].description+'" /> <div>'+result.current.weather[0].description+'</div>\n' +
             '      </div>\n' +
             '      <div class="weather-item-name">'+result.current.name+' '+flag+'</div>\n' +
             '      <div class="weather-item-date">'+time+'</div>\n' +
             '      <div class="weather-item-temp">'+result.current.main.temp_max+'°C</div>\n' +
             '      RH: '+result.current.main.humidity+'%\n' +
             '      <div class="weather-item-wind">\n' +
             '        <div class="weather-item-wind-arrow" style="transform: rotate(' + result.current.wind.deg + 'deg); transform-origin: 50% 50%"></div>\n' +
             '        <div class="weather-item-wind-speed"  >'+result.current.wind.speed+' м/c</div>\n' +
             '        <div class="weather-item-wind-descr">' + windDirect(result.current.wind.deg) + '</div>\n' +
             '      </div>\n' +
             '    </div>')

           let domainTemp = [];

           result.hourly.forEach( item => {

             domainTemp.push(item.t);

             if(item_number===0){

               if(item.dt.slice(-5)=='00:00'){
                 domainLables.push('Полночь');
               }else {
                 domainLables.push(item.dt.slice(-5));
               }
             }
           })

           dataweather.datasets.push({
             "label": result.current.name,
             "data": domainTemp });
           item_number++;
           promisesCount--;
           dataweather.labels = domainLables;
           if(promisesCount==0){

             document.querySelector('.weather-chart').style.opacity =1;

             let chart = drawChart(dataweather);

             if(window.innerWidth<640){
               chart.options.scales.y.ticks.mirror = true;
               chart.options.scales.y.title.display = false;
               chart.update();
             }else{
               chart.options.scales.y.ticks.mirror = false;
               chart.options.scales.y.title.display = true;
               chart.update();
             }

             window.onresize  = (event) => {
               if(window.innerWidth<640){
                 chart.options.scales.y.ticks.mirror = true;
                 chart.options.scales.y.title.display = false;
               }else{
                 chart.options.scales.y.ticks.mirror = false;
                 chart.options.scales.y.title.display = true;
               }
             };

           }
         })

     })


     function drawChart(data){
       let chart = new Chart(document.getElementById('myChart'), {
         type: 'line',
         data: data,
         options: {
           responsive: true,
           maintainAspectRatio:false,
           plugins: {
             title: {
               display: true,
               text: 'Температура воздуха за 24 часа'
             },
           },
           scales: {
             x: {
               display: true,
               ticks: {
                 maxRotation: 90,
                 minRotation: 90
               },
               title: {
                 display: true,
                 text: 'Время, МСК'
               }
             },
             y: {
               display: true,
               ticks: {
               },

               title: {
                 display: false,
                 text: '°C'
               }
             }
           }
         }
       });

       chart.canvas.parentNode.style.height = '50vh';

       return chart;
     }

   });

})
