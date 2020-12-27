#!/bin/bash



####################################################################
####              START CODING
####################################################################

FILE=sudo #Detect and install sudo rights
if test -f "$FILE"; then
    source ~/.bashrc
else    
    nosudo ""
    echo "running as root" >> sudo
fi

cr=`echo $'\n.'` #preparation of
cr=${cr%.}       #newline
while true; do
    read -p "0 Instal dependencys - 1 Run Code - 2 Stop Code - 3 Noting $cr" question
    case $question in
        [0]* ) #---------------install puppeteer       
        echo "0- Installing dependencys";        
        echo "Create package.json ? [y/n]"; 
        read;if [[ "${REPLY}" == "y" ]]; then
          echo "Complete the Quest";
          npm init #create package.json
        fi      
        echo "Dowload node Chromium ? [y/n]"; 
        read;if [[ "${REPLY}" == "y" ]]; then
          npm i puppeteer-core && npm start;        
          npm install puppeteer --save 
          npm install --save chromium-all-codecs-bin  
          npm install fluent-ffmpeg
        fi              
        install-pkg xvfb
        break;;
        [1]* ) #---------------run node
        echo "1- Starting node.Js...";
        #xvfb-run -s ":99 -ac -screen 0 800x600x24" node main.js
        node main.js;
        break;;
        [2]* ) #---------------stop node
        echo "2- Stoping all dependencys, wait browser reconect or refresh";
        #end Zombies and Dependencys (update repl.it)
        killall chrome
        #killall xvfb
        killall x11vnc
        killall fluxbox
        killall Xorg
        killall init
        exit;
        break;;
        [3]* ) #---------------EXIT bash
        echo "3- Noting Selected";
        exit;
        break;;
        * ) echo "Please answer the question";;
    esac
done
echo "Finished";



