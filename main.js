//CONSTANTS
const puppeteer = require('puppeteer-core');
const execPath = require( 'chromium-all-codecs-bin' )();
const http = require('http');
const https = require('https');
var   path = require('path');
const fs = require('fs').promises;
const fs2 = require('fs');
var faker = require('faker');
var request = require('request');


//GLOBAL VARIABLES
var browser =null;
var pages =null;
var httpport=3000;
//VARX
var randomName,email,pass,data,month,day,year;
  

//VOID MAIN()
(async () => {   
  await CreateBrowser(); 
})().catch(error => { 
  console.error("Http Server Error: ", error); 
});

async function ConfigPage(page){
  await page.setViewport({
      width  : 500,
      height : 500
  });  
  await page.setExtraHTTPHeaders({
	'Accept-Language': 'pt-br'
  });  
  await page.setDefaultNavigationTimeout(80000);  
  await page.on('response', async response => {  
    await pageOnload(response); 
  });
}

async function CreateBrowser(){
  console.log('creating browser...');
  browser = await puppeteer.launch({ headless: false, args: [
      '--no-sandbox', 
      '--disable-setuid-sandbox',
      '--disable-dev-shm-usage',      
      '--kiosk',
      '--noerrdialogs',
      '--disable-translate',
      '--no-first-run',
      '--disable-infobars',
      '--disable-features=TranslateUI',
      //'--restore-last-session',
      '--disable-gpu'
      //'--lang=pt-br,pt-BR'
  ]
  ,headless: false
  ,executablePath: execPath
  });
  await browser.newPage();
  pages = await browser.pages();  
  await ConfigPage(pages[0]);
  await ConfigPage(pages[1]);

  await CriaDados();
}


async function CriaDados(){
  //###########CRIA DADOS
  await pages[ 0 ].goto('about:blank');  
  var client = await pages[ 0 ].target().createCDPSession();
  await client.send('Network.clearBrowserCookies');
  await client.send('Network.clearBrowserCache');
  email='';
  randomName = faker.name.findName();
  randomName=randomName.split(' ').join('');
  randomName+=faker.name.findName().split(' ')[0];
  randomName=randomName.trim();
  randomName=randomName.replace(/[^a-z0-9]/gi, '');
  randomName=randomName.substring(0, 24);
  pass=faker.internet.password();
  data=faker.date.between('2001-01-01', '1970-01-01');
  month = data.getUTCMonth() + 1; //months from 1-12
  day = data.getUTCDate();
  year = data.getUTCFullYear();
  //GET MAIL
  await pages[ 0 ].bringToFront();
  await pages[ 0 ].goto('https://generator.email/email-generator');
  await pages[ 0 ].waitForSelector('#userName');  
  await pages[ 0 ].waitForSelector('#domainName2');
  email=await pages[ 0 ].evaluate((email) => {
      email= 
      document.getElementById("userName").value+
      "@"+
      document.getElementById("domainName2").value;
      return email;      
  },email);
  console.log('DATA CREATEO');

  //#######PoPULATE TW
  client = await pages[ 1 ].target().createCDPSession();
  await client.send('Network.clearBrowserCookies');
  await client.send('Network.clearBrowserCache');  
  await pages[ 1 ].bringToFront();
  await pages[ 1 ].goto('https://www.twitch.tv/login');
  await pages[ 1 ].waitForSelector(
    'div[data-a-target="facebook-button-div"]'); 
  await pages[ 1 ].evaluate(() => {
    var obj1=document.querySelectorAll('button');
    obj1[1].click();
  });
  await pages[ 1 ].waitForSelector('#email-input'); 
  await pages[ 1 ].type('#signup-username', randomName);
  await pages[ 1 ].type('#password-input', pass);
  await pages[ 1 ].type('#password-input-confirmation', pass);
  const dta = 
  await pages[ 1 ].$$('input[data-a-target="tw-input"]');
  await dta[3].type(day.toString());
  await dta[4].type(year.toString());
  await pages[ 1 ].select(
    'select[data-a-target="birthday-month-select"]', month.toString());
  await pages[ 1 ].type('#email-input', email);
  await pages[1].waitForSelector('button[data-a-target="passport-signup-button"]:not([disabled])');
  await pages[ 1 ].click('button[data-a-target="passport-signup-button"]');
  console.log('Resolve Captcha');  
}



async function pageOnload(response){  
  var myurl=response.url().trim();      
  //#Mail Received
  if(myurl.startsWith(
  'https://generator.email/inbox')==true){      
    console.log('Inbox Received');
    await pages[ 0 ].waitForSelector('#email-table');
    var codigo='';
    codigo=await pages[ 0 ].evaluate((codigo)=> {
      var ccv=document.getElementById('email-table').children;
      codigo=ccv[0].children[1].textContent;
      return codigo;
    },codigo);
    codigo=
    codigo.split('-')[0].replace(/[^0-9]/gi, '').trim();
    console.log('CODE',codigo);
    for (var i = 0; i < codigo.length; i++) {  
      await pages[1].keyboard.press(codigo.charAt(i))
    }  
  }
  //#First TW LOGIN
  if(myurl.startsWith(
  'https://www.twitch.tv/?no-reload=true')==true){   
     await pages[ 0 ].goto('about:blank');
     await pages[ 1 ].goto('https://www.twitch.tv/settings/profile');
     await continueprofile();
  }
}



async function continueprofile(){
  //PUT IMAGE ON PROFILE
  var gender=Math.floor(Math.random() * 1) + 1;
  var image='https://randomuser.me/api/portraits/women/';
  if(gender==0)image='https://randomuser.me/api/portraits/men/';
  var phn=Math.floor(Math.random() * 98) + 1;
  image+=phn.toString()+'.jpg';
  download(image, './rosto.jpg',async function(){    
    await pages[ 1 ].waitForSelector('input[type=file]');
    const inputUploadHandle = 
    await pages[ 1 ].$('input[type=file]');
    inputUploadHandle.uploadFile(`./rosto.jpg`);    
    var btnsel='button[data-test-selector="profile-edit-presentation__submit"]';
    var btsav = await pages[ 1 ].waitForSelector(btnsel);
    await pages[ 1 ].evaluate((btnsel) => 
      document.querySelector(btnsel).click()
    ,btnsel);
    await finisheandTransfer();
  });  
}


async function finisheandTransfer(){
  //SAVE DATA TO EXTERNAL
  var cookies = await pages[ 1 ].cookies();
  var jscookie=JSON.stringify(cookies, null, 2);
  request({
    url: 'SITE PHP RECIVE DATA/index.php', //CONFIGURE YOUR SITE PHP TO RECEIVE DATA
    method: 'POST',    
    json: 
      {
      login: '666',
      grava:randomName,
      pas:pass,
      mail:email,
      data:jscookie      
      }
  },async function(error, response, body){
     console.log(body);
     await CriaDados();
  });
}

var download = function(uri, filename, callback){
  request.head(uri, function(err, res, body){
    request(uri).pipe(fs2.createWriteStream(filename)).on('close', callback);
  });
};


/*
  //getcookies
  const cookies = await pages[ 1 ].cookies();
  await fs.writeFile('./cookies.json', 
  JSON.stringify(cookies, null, 2));
  //setcookies
  const cookiesString = await fs.readFile('./cookies.json');
  const cookies = JSON.parse(cookiesString);
  await pages[ 1 ].setCookie(...cookies);
  await pages[ 1 ].goto('https://www.twitch.tv/');
  */
  // ../Downloads/nome.wav
