# KHLIVE

## Introduction 

Kh-live is a system designed to stream meetings to listeners using different medias.

Kh-live is developed on raspberry's raspbian distribution due to the OS requiring relatively inexpensive hardware but it can potentially run on any linux based system.

**Kh-live is licensed to be used for free in kingdom halls only.**

*Kh-live relies on open source software, developing new functionality based on kh-live?*
 
Remember you may do so only if you agree to our license terms and share your code with us.

Please check the [Wiki](https://github.com/kh-live/khlive/wiki) for additional resources.


## Features

- Line input/output using sound card to **play songs** and **receive answers remotely**
- **VOIP calling and listening** (sip/aix)
- **Stream in/out** in both .mp3 and .ogg format
- Send an **answer via text**
- **Record meetings** in .mp3 and .wav format

## Supported browsers

- Google Chrome
- Mozilla Firefox
- [Kindle Fire Firefox mobile](https://support.mozilla.org/en-US/kb/installing-firefox-android-amazon-kindle-fire)
- Safari
- Internet Explorer 10+
- Microsoft Edge

## Getting started

### Make sure you meet our [Hardware requirements](https://github.com/kh-live/khlive/wiki).

### [Get the raspberry pi going](https://github.com/kh-live/khlive/wiki/Rasberry-Pi-Setup)

### [Manual Installation (No delay)](https://github.com/kh-live/khlive/wiki/Manual-installation)

### [Manual Installation (No delay and No VOIP)](https://github.com/kh-live/khlive/wiki/Manual-Installation-No-VOIP)

### Automatic Installation (30 second delay)

#### Startup

1. Once the operating system finishes loading, you will need to log in #The default username is “pi”, and the default password is “raspberry”.

2. Open the terminal by holding down the keys [Crtl + Alt + T].

3. Copy and paste the following command into the terminal and press *enter* to execute: `sudo raspi-config`

3. Choose **expand filesystem**.

4. Reboot

#### Getting installation

1. Copy and paste the following commands into the terminal: 

a. `wget http://kh-live.co.za/downloads/install_no_voip.sh`

b.  `sudo chmod +x ./install_no_voip.sh`

c.  `sudo ./install_no_voip.sh`

#### Final setup

- The installation set your ip address to 192.168.1.123 and gateway to 192.168.1.1 (you can edit the installation script if these are not the right values).

1. Set the timezone (select Africa / Johannesburg)

2. Set the password (choose something hard to guess - don't loose it)

- The required software will be downloaded

3. You will be asked to configure icecast2. **Say NO!**

- Software installation will take a while

4. (Optional) 7. To be able to access the recordings over windows shares, you can install samba (press y)

5. Installing the songs. You can either download directly from the official website, or you can transfer the files over windows share - \\RASPBERRYPI\khsongs (if you installed it earlier). For local install say yes to download then local to use the files. The files expected are : iasn_E.m4a.zip snnw_E.m4a.zip and iasnm_E.m4a.zip

6. It will then ask you if you'd like to update the operating system.

7. It will ask if you want to configure the sound card, play a test sound (pink noise), and show you the mixer so you can make adjustments (alsamixer).

8. Finally you'll be able to reboot. After that you can connect to http://192.168.1.123 and configure the server.

## Powered by:

![Raspbian OS](3rdparty/raspberry-pi-logo.png)
![Asterisk](3rdparty/asterisk_logo.png)
![Icecast](3rdparty/icecast_logo-large.png)
![vmix-logo](3rdparty/vmix-logo.png)

