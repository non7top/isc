isc
===

ISPconfig3 command line interface (via SOAP)

I needed a simple tool to add client/site/db/ftp combination to an ISPConfig3-controlled environment. Messing with new web interface of ISPConfig3 is too slow and unreasonable waste of time so I used exsiting examples of using SOAP API and created this simple tool.

Requirements
===
**php_soap** installed on the server  
**remote user** created in ISPConfig with access to Domain/Site/Client tools  
**php** where the script is being run (tested with php5.4-cli)

Usage
===
For now only one usage pattern is supported

    $ ./isc.php  example.com site_wizard add test  
    Logged successfull. Session ID:30a1cd580a8fc904874220fc0342e5cd  
    Created client: test  
    Created web site: test.example.com  
    Created ftp user: test_ftp / ##########  
    Created DB user: c##_test / ##########  
    Created DB: c##_test  

config.php
===
Example file

    $ cat config.php
    <?  
    die ("Access prohibited");  
    ## File format  
    http://example.com:8080|username|password  


    
