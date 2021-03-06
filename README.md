If your bug / question remains unanswered in gitter, please open an Issue – otherwise it will be lost in a sea of messages :)

### Prerequisite
    # Install sqlite
    sudo apt install sqlite3
    
    # Install php sqlite extension
    sudo apt install php7.1-sqlite
    
### Installation

    composer create-project dridi-walid/pci:dev-master
    git clone https://github.com/dridi-walid/pci.git && cd $(basename $_ .git) && composer install

### Run built-in php web server

    composer serve # php -S localhost:9001 -d display_errors=1

### Run build

make POST request with Github payload format to launch build and get project status

	POST localhost:9001/build

	Content-Type: application/json

	{
	  "id": 4458399707,
	  "sha": "1c844bac2dbb5814242be1cb1a45594e42180718",
	  "name": "dridi-walid/micro-framework-skeleton"
	}
  
### Display output console

make GET request with commit hash

  	GET localhost:9001/output/1c844bac2dbb5814242be1cb1a45594e42180718
  
### Display build status

make GET request with commit hash

  	GET localhost:9001/status/1c844bac2dbb5814242be1cb1a45594e42180718

PCI project is online at http://52.28.188.226 and frequently updated
