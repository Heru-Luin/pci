If your bug / question remains unanswered in gitter, please open an Issue – otherwise it will be lost in a sea of messages :)

### Installation

    composer create-project dridi-walid/pci:dev-master
    or
    git clone https://github.com/dridi-walid/pci.git && cd pci && composer install

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
