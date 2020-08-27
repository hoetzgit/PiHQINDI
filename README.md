# PiHQINDI
## INDI driver for the Raspberry Pi HQ Camera

This INDI driver is based on the ASCOM driver written by Rob Musquetier available at: https://www.musquetier.nl/downloads/RPiCameraV3_Setup.exe. Many thanks for use of his PHP driver and much of the text below. Hopefully this project can assist in refining the script!

This driver will enable you to use the, in Q2 2020 released version 3 Raspberry Pi 12 Mpx camera with Sony IMX477 sensor for your favorite INDI enabled programs.

This driver is part of a project to build an INDI based Plate Solver (similar to the Celestron StarSense) that will provide platesolve based alignment and position refinement services to telescopes using INDI.

## INSTALLATION INSTRUCTIONS 

Step 1) Order and initial setup your Raspberry Pi:

Order the Raspberry Pi version 3 camera (HQ Camera) at your favorite supplier. If you don't have a suitable Raspberry Pi, order it as well. I recommend a Raspberry Pi 3 or 4 (either with 2 or 4GB RAM), but older models will probably work as well. Include in your order a micro-SSD card of at least 16GB with NOOBS (Raspberry Pi image) pre-installed. When you want to install NOOBS on the SD card yourself find instructions here: https://www.raspberrypi.org/help/noobs-setup/2/

When needed order a nice Raspberry Pi casing (ensure it has facilities to deal with the camera flatcable which needs to exit the casing in a proper way) together with a suitable power adapter.

You probably also need to order an adapter ring to mount the camera sensor to your telescope. Example: https://www.astromarket.org/andere-adapters/ts-optics-adapter-voor-t2-op-c-mount/p,47967 and maybe a T2 to 48mm adapter (if required for your telescope).

Also a longer e.g. 50cm (20") camera flat cable will be handy because the standard with the camera sensor included flatcable is only 15cm (6") long.

Make sure you have the needed cable(s) to hook up the Raspberry Pi to your monitor (micro HDMI or HDMI depending on the Raspberry Pi model, https://www.raspberrypi.org/products/micro-hdmi-to-standard-hdmi-a-cable/) and when needed a mouse and keyboard.

Optionally order a test lense (for when the sensor is not used in combination with a telescope), e.g.: https://www.kiwi-electronics.nl/landing-release/6mm-3mp-lens-for-rpi-hq-camera or https://www.kiwi-electronics.nl/landing-release/16mm-10mp-lens-for-rpi-hq-camera

Step 2) Attach camera to the Raspberry Pi:

Insert the camera flat cable in the Raspberry Pi (blue side of the cable facing towards the USB connectors) and the camera (if not pre-fitted) with the blue side facing away from the sensor). See https://thepihut.com/blogs/raspberry-pi-tutorials/how-to-replace-the-raspberry-pi-camera-cable

Place the Raspberry Pi in the casing and attach the monitor, keyboard, mouse and power adapter and spin up the Raspberry Pi.

Always shutdown the Raspberry Pi (with sudo shutdown -h 0) and uncouple the power if you are detaching or attaching the camera cable, it is sensitive for static electricity and you might damage it when performing these action when it is still powered on.

Step 3) Update your Raspberry Pi and enable SSH access:

Boot up the Raspberry Pi (default password is "raspberry"), enter the needed data during the installation wizard and open een terminal session (when default booting the graphical interface).

First perform an update of the Raspberr Pi and the camera's firmware and reboot the setup:

sudo rpi-update

sudo shutdown -r 0

Secondly update all packages to the current version to ensure your Raspberry Pi is fully up to date:

sudo apt-get update -y
sudo apt-get full-upgrade -y

Ensure in the future your Raspberry Pi automatically updates all packages:

sudo apt-get install unattended-upgrades -y

Start the Raspberry Pi config tool and configure the following settings:

sudo raspi-config

- Change the user password to your likings!
- Change the hostname (when desired) with option 2 Network Options->N1 Hostname.
- When desired change the Wi-Fi setting (when not already done during the initial setup) with option 2 Network Options->N2 Wi-fi.
- Switch on the camera use option 5 Interface Options->P1 Camera.
- When desired switch on remote access (via SSH terminal like Putty) with option 5 Interface Options->P2 SSH.
- Ensure the camera memory is configured for at least 128MB with option 7 Advanced Options->A3 Memory Split

Close the config tool (select the Reboot->Yes option). The reboot should be finished in app. 2 - 3 minutes.

Step 4) Configure your camera and test initial working:

When needed download and install the putty terminal server software to access your Raspberry Pi from another machine. Configure as user pi:[IP address] and save the configuration once before starting it up.

When you have a keyboard, mouse and display directly attached to your Raspberry Pi the previous step is not needed.

Log onto the Raspberry Pi using your newly configure password (and open een terminal session when in graphical mode).

Allow the Apache user to use the camera by executing the following command:

sudo usermod -a -G video www-data

Go the webservers home directory:

cd /var/www/html

Test your Raspberry Pi camera with the command:

raspistill -v -o output.jpg

This should create your first (probably out of focus) picture in the file output.jpg

Step 5) Setting up needed software to control the Raspberry Pi camera:
Install the following packages on your Raspberry Pi to use this driver:

- apache2
- php
- imagemagick

Install these packages by executing the following commands:

sudo app install apache2 php imagemagick -y

Allow the Apache group www-data to use the camera by executing command:

usermod -a -G www-data www-data

Step 6) Install php script in /var/www/html included at the bottom of this readme file:

To use this driver install the php script included in this readme file below on your Raspberry Pi in the directory folder /var/www/html as RPi_camera.php (NOTE: case saensitive file name).

sudo /var/www/html/nano RPi_camera2.php

Copy and paste the scripts below into the editor and save the file (<CTTR> O) and exit the editor (<CTRL> X).

Change the ownership of the file so the web server can use the file:

sudo chown www-data:www-data RPi_camera.php

Change the priviledges on the file so the webserver can read it:

sudo chmod 660 RPi_camera.php

Step 7) Retrieve your IP address (if not done yet):

Retrieve you IP address of your raspberry Pi by exeecuting this command:

Hostname -I 

With the address you can change configure the Raspberry Pi in the INDI driver setup menu. Now startup and your favorite client application (e.g. kstars/EKOS) are good to go to start using the INDI driver for the Raspberry V3 camera!!! 

When you desided to use an alternative TCP/IP port for your webserver on your Raspberry Pi, change it to your likings (Apache default uses port 80 which is the drivers default value).

Step 8) Configure the Raspberry Pi with a fixed IP address (optional):

It might be a good idea to give your Raspberry Pi a permanent address otherwise aftger a reboot of your Raspberry Pi it could recieve another IP address causing a communication error next time you connect to it.

This can be done by altering this file /etc/dhcpcd.conf on your Raspberry Pi by entering the following command:

sudo nano /etc/dhcpcd.conf

Now scroll down a bit and find the lines with:

#static ip_address=[IP address]

Remove the trailing hash and fill in the IP address you want to be used by your Raspberry Pi from now onwards by your Raspberry Pi and save this configuration file (<CTRL> O) and exit the editor (<CTRL> X). It might be a good idea to ensure on your router that the address chosen will not be handled out to any other device anymore... 

Reboot your Raspberry Pi by executing:

sudo shutdown -r 0

Step 9) Play and learn:

Experiment with the available settings. Background information on the workings of these settings can be read at the following page: https://www.raspberrypi.org/documentation/raspbian/applications/camera.md 

The script is using the reapistill command to capture images and the php script makes the most useful parameters available via the RESTful API of the php script.

## Available parameters:

Parameter	| Default Value	| Options
--------- | ------------- | -------
type|jpg|jpg, bmp
analog_gain|1|1 - 12
exposure|off|all options mentioned on the referred camera webpage are available
flicker|off|off, auto, 50hz, 60hz
awb|auto|off, auto, sun, cloud, shade, tungsten, fluorescent, incandescent, flash, horizon, greyworld
hflip|false|false, true
vflip|false|false, true
binning|1|1, 2, 3, 4
shutter|1000000|1 - 239000000 (uSec.)
drc|off|off,low,medium,high
ag|1|1 - 16
dg|1|1 - 255
mode|3|1, 2, 3, 4
verbose|false|false, true
